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
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequestAbstract;
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
     * @param RestRequestAbstract   $request
     * @param QueryBuilder          $qb
     *
     * @return RestResponse
     */
    public function index(RestRequestAbstract $request, QueryBuilder $qb)
    {
        $paginator = new Paginator($qb, false);
        $resource = new Collection($paginator, $this->transformer(), $this->getIndexResourceKey($qb));
        $resource->setPaginator(new DoctrinePaginatorAdapter($paginator, $this->getPaginatorRouteGenerator($request)));

        return $this->response(
            $this->fractal($request)
                ->parseFieldsets($request->getFields())
                ->createData($resource)
                ->toArray()
        );
    }

    /**
     * @param RestRequestAbstract   $request
     * @param object                $entity
     *
     * @return RestResponse
     */
    public function show(RestRequestAbstract $request, $entity)
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
     * @param RestRequestAbstract   $request
     * @param object                $entity
     *
     * @return RestResponse
     */
    public function create(RestRequestAbstract $request, $entity)
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
     * @param RestRequestAbstract $request
     * @param object              $entity
     *
     * @return RestResponse
     */
    public function update(RestRequestAbstract $request, $entity)
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
     * @param RestRequestAbstract $request
     * @param object              $entity
     *
     * @return RestResponse
     */
    public function delete(RestRequestAbstract $request, $entity)
    {
        return $this->response();
    }

    /**
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    public function notFound(RestRequestAbstract $request)
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
     * @param RestRequestAbstract $request
     *
     * @return Manager
     */
    protected function fractal(RestRequestAbstract $request)
    {
        $fractal = new Manager();

        if (in_array(static::JSON_API_CONTENT_TYPE, $request->getAcceptableContentTypes())) {
            $fractal->setSerializer(new JsonApiSerializer($this->baseUrl));
        }

        if ($includes = $request->get('include')) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->get('exclude')) {
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
     * @param RestRequestAbstract $request
     *
     * @return \Closure
     */
    protected function getPaginatorRouteGenerator(RestRequestAbstract $request)
    {
        return function(int $page) {
            return null;
        };
    }
}
