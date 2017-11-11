<?php namespace Pz\Doctrine\Rest\Response;

use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\Response;

class FractalResponseFactory implements RestResponseFactory
{
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @var string|null
     */
    protected $baseUrl;

    /**
     * FractalResponse constructor.
     *
     * @param string|null              $baseUrl
     * @param TransformerAbstract|null $transformer
     */
    public function __construct($baseUrl = null, TransformerAbstract $transformer = null)
    {
        $this->baseUrl = $baseUrl;
        $this->transformer($transformer);
    }

    /**
     * @param TransformerAbstract|null $transformer
     *
     * @return TransformerAbstract|$this
     */
    public function transformer(TransformerAbstract $transformer = null)
    {
        if ($transformer !== null) {
            $this->transformer = $transformer;
            return $this;
        }

        return $this->transformer;
    }

    /**
     * @param RestRequest  $request
     * @param QueryBuilder $qb
     *
     * @return RestResponse
     */
    public function collection(RestRequest $request, QueryBuilder $qb)
    {
        $resourceKey = $this->getIndexResourceKey($qb);
        $paginator = new Paginator($qb, false);
        $resource = new Collection($paginator, $this->transformer(), $resourceKey);

        if ($request->getLimit() !== null) {
            $resource->setPaginator(new DoctrinePaginatorAdapter($paginator,
                function(int $page) use ($resourceKey, $request) {
                    return "{$this->baseUrl}/$resourceKey?".http_build_query([
                        'page' => [
                            'number'    => $page,
                            'size'      => $request->getLimit()
                        ]
                    ]);
                }
            ));
        }

        return $this->resourceResponse($request, $resource);
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function item(RestRequest $request, $entity)
    {
        $resourceKey = ($entity instanceof JsonApiResource) ? $entity->getResourceKey() : null;

        return $this->resourceResponse($request, new Item($entity, $this->transformer(), $resourceKey));
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function created(RestRequest $request, $entity)
    {
        $headers = [];
        $resourceKey = null;

        if ($entity instanceof JsonApiResource) {
            $resourceKey = $entity->getResourceKey();
            $headers['Location'] = $this->linkJsonApiResource($entity);
        }

        return $this->resourceResponse(
            $request,
            new Item($entity, $this->transformer(), $resourceKey),
            Response::HTTP_CREATED,
            $headers
        );
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function updated(RestRequest $request, $entity)
    {
        return $this->item($request, $entity);
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function deleted(RestRequest $request, $entity)
    {
        return $this->resourceResponse(null, RestResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function notFound(RestRequest $request)
    {
        return $this->resourceResponse(null, RestResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param \Error|\Exception|RestException $exception
     *
     * @return RestResponse
     * @throws \Error|\Exception|RestException
     */
    public function exception($exception)
    {
        switch (true) {
            case ($exception instanceof RestException):
                return RestResponse::create(['errors' => $exception->errors()], $exception->httpStatus());
                break;

            default:
                break;
        }

        throw $exception;
    }

    /**
     * Return configured fractal by request format.
     *
     * @param RestRequest $request
     *
     * @return Manager
     */
    protected function fractal(RestRequest $request)
    {
        $fractal = new Manager();

        if ($request->isAcceptJsonApi()) {
            $fractal->setSerializer(new JsonApiSerializer($this->baseUrl));
        }

        if ($includes = $request->getInclude()) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->getExclude()) {
            $fractal->parseExcludes($excludes);
        }

        if ($fields = $request->getFields()) {
            $fractal->parseFieldsets($fields);
        }

        return $fractal;
    }

    /**
     * @param RestRequest            $request
     * @param ResourceInterface|null $resource
     * @param int                    $httStatus
     * @param array                  $headers
     *
     * @return RestResponse
     */
    protected function resourceResponse(
        RestRequest $request,
        ResourceInterface $resource = null,
        $httStatus = RestResponse::HTTP_OK,
        array $headers = []
    ) {
        if ($request->isAcceptJsonApi()) {
            $headers['Content-Type'] = static::JSON_API_CONTENT_TYPE;
        }

        $data = $this->fractal($request)->createData($resource)->toArray();

        return RestResponse::create($data, $httStatus, $headers);
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return string
     */
    protected function getIndexResourceKey(QueryBuilder $qb)
    {
        $class = $qb->getRootEntities()[0];
        if (isset(class_implements($class)[JsonApiResource::class])) {
            return call_user_func("$class::getResourceKey");
        }

        return $qb->getRootAliases()[0];
    }

    /**
     * @param JsonApiResource $resource
     *
     * @return string|null
     */
    protected function linkJsonApiResource(JsonApiResource $resource)
    {
        return sprintf('%s/%s/%s', $this->baseUrl, $resource->getResourceKey(), $resource->getId());
    }

    /**
     * @param RestRequest $request
     *
     * @return \Closure
     */
    protected function getPaginatorRouteGenerator(RestRequest $request)
    {
        return function(int $page) {
            return null;
        };
    }
}
