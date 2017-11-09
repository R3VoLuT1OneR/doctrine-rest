<?php namespace Pz\Doctrine\Rest\Response;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\Contracts\HasResourceKey;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequestInterface;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Symfony\Component\HttpFoundation\Response;


class FractalResponse implements RestResponseFactory
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
     * @param null $baseUrl
     */
    public function __construct($baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param TransformerAbstract|null $transformer
     *
     * @return TransformerAbstract
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
     * @param IndexRequestInterface $request
     * @param QueryBuilder          $qb
     *
     * @return RestResponse
     */
    public function index(RestRequestInterface $request, QueryBuilder $qb)
    {
        $paginator = new Paginator($qb, false);
        $generator = $this->getPaginatorRouteGenerator($request);
        $resource = new Collection($paginator, $this->transformer(), $this->getIndexResourceKey($qb));
        $resource->setPaginator(new DoctrinePaginatorAdapter($paginator, $generator));

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData($resource)
                ->toArray()
        );
    }

    /**
     * @param ShowRequestInterface $request
     * @param             $entity
     *
     * @return RestResponse
     */
    public function show(RestRequestInterface $request, $entity)
    {
        if ($entity instanceof HasResourceKey) {
            $resourceKey = $entity->getResourceKey();
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey ?? null))
                ->toArray()
        );
    }

    /**
     * @param CreateRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function create(RestRequestInterface $request, $entity)
    {
        if ($entity instanceof HasResourceKey) {
            $resourceKey = $entity->getResourceKey();
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey ?? null))
                ->toArray(),
            Response::HTTP_CREATED
        );
    }

    /**
     * @param UpdateRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function update(RestRequestInterface $request, $entity)
    {
        if ($entity instanceof HasResourceKey) {
            $resourceKey = $entity->getResourceKey();
        }

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData(new Item($entity, $this->transformer(), $resourceKey ?? null))
                ->toArray()
        );
    }

    /**
     * @param DeleteRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function delete(RestRequestInterface $request, $entity)
    {
        return $this->response();
    }

    /**
     * @param RestRequestInterface $request
     *
     * @return RestResponse
     */
    public function notFound(RestRequestInterface $request)
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
     * @param RestRequestInterface $request
     *
     * @return Manager
     */
    protected function fractal(RestRequestInterface $request)
    {
        $fractal = new Manager();

        if ($request->isJsonApi()) {
            $fractal->setSerializer(new JsonApiSerializer($this->baseUrl));
        }

        if ($includes = $request->http()->get('include')) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->http()->get('exclude')) {
            $fractal->parseExcludes($excludes);
        }

        return $fractal;
    }

    /**
     * @param mixed $data
     * @param int   $httStatus
     *
     * @return RestResponse
     */
    protected function response($data = null, $httStatus = RestResponse::HTTP_OK)
    {
        return new RestResponse($data, $httStatus);
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return string
     */
    protected function getIndexResourceKey(QueryBuilder $qb)
    {
        $class = $qb->getRootEntities()[0];
        if (isset(class_implements($class)[HasResourceKey::class])) {
            return call_user_func($class . '::getResourceKey');
        }

        return $qb->getRootAliases()[0];
    }


    /**
     * @param RestRequestInterface $request
     *
     * @return \Closure
     */
    protected function getPaginatorRouteGenerator($request)
    {
        return function(int $page) {
            return null;
        };
    }
}
