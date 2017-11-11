<?php namespace Pz\Doctrine\Rest\Response;

use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
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
    public function index(RestRequest $request, QueryBuilder $qb)
    {
        $headers = [];
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

        if ($request->isAcceptJsonApi()) {
            $headers['Content-Type'] = static::JSON_API_CONTENT_TYPE;
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData($resource)
                ->toArray(),
            RestResponse::HTTP_OK,
            $headers
        );
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function show(RestRequest $request, $entity)
    {
        $headers = [];
        $resourceKey = null;

        if ($entity instanceof JsonApiResource) {
            $resourceKey = $entity->getResourceKey();

            if ($request->isAcceptJsonApi()) {
                $headers['Location'] = $this->linkJsonApiResource($entity);
                $headers['Content-Type'] = static::JSON_API_CONTENT_TYPE;
            }
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey ?? null))
                ->toArray(),
            RestResponse::HTTP_OK,
            $headers
        );
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function create(RestRequest $request, $entity)
    {
        $headers = [];
        $resourceKey = null;

        if ($entity instanceof JsonApiResource) {
            $resourceKey = $entity->getResourceKey();

            if ($request->isAcceptJsonApi()) {
                $headers['Location'] = $this->linkJsonApiResource($entity);
                $headers['Content-Type'] = static::JSON_API_CONTENT_TYPE;
            }
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey))
                ->toArray(),
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
    public function update(RestRequest $request, $entity)
    {
        $headers = [];
        $resourceKey = null;

        if ($entity instanceof JsonApiResource) {
            $resourceKey = $entity->getResourceKey();

            if ($request->isAcceptJsonApi()) {
                $headers['Location'] = $this->linkJsonApiResource($entity);
                $headers['Content-Type'] = static::JSON_API_CONTENT_TYPE;
            }
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey))
                ->toArray(),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return RestResponse
     */
    public function delete(RestRequest $request, $entity)
    {
        return $this->response(null, RestResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function notFound(RestRequest $request)
    {
        return $this->response(null, RestResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param \Error|\Exception|\Pz\Doctrine\Rest\RestException $exception
     *
     * @return RestResponse
     * @throws \Error|\Exception|RestException
     */
    public function exception($exception)
    {
        $message = $exception->getMessage();

        switch (true) {
            case ($exception instanceof RestException):
                $httpStatus = $exception->httpStatus();
                $errors = $exception->errors();
                break;

            default:
                throw $exception;
                // $httpStatus = RestResponse::HTTP_INTERNAL_SERVER_ERROR;
                // $errors = $exception->getTrace();
                break;
        }

        return $this->response(['message' => $message, 'errors' => $errors], $httpStatus);
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

        return $fractal;
    }

    /**
     * @param mixed $data
     * @param int   $httStatus
     * @param array $headers
     *
     * @return RestResponse
     */
    protected function response($data = null, $httStatus = RestResponse::HTTP_OK, array $headers = [])
    {
        return new RestResponse($data, $httStatus, $headers);
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
