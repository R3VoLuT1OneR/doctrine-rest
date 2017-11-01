<?php namespace Pz\Doctrine\Rest\Response;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequestInterface;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;


class FractalResponse implements RestResponseFactory
{
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * FractalResponse constructor.
     *
     * @param TransformerAbstract $transformer
     */
    public function __construct(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param RestRequestInterface  $request
     * @param QueryBuilder          $qb
     *
     * @return RestResponse
     */
    public function index(RestRequestInterface $request, QueryBuilder $qb)
    {
        $resource = new Collection($paginator = new Paginator($qb), $this->transformer());
        $resource->setPaginator(new DoctrinePaginatorAdapter($paginator, $this->getPaginatorRouteGenerator()));

        return $this->response(
            $this->fractal($request)
                ->createData($resource)
                ->toArray()
        );
    }

    /**
     * @param RestRequestInterface  $request
     * @param                       $entity
     *
     * @return RestResponse
     */
    public function show(RestRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param RestRequestInterface  $request
     * @param                       $entity
     *
     * @return RestResponse
     */
    public function create(RestRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param RestRequestInterface  $request
     * @param                       $entity
     *
     * @return RestResponse
     */
    public function update(RestRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param RestRequestInterface  $request
     * @param                       $entity
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

        if (in_array(static::JSON_API_CONTENT_TYPE, $request->http()->getAcceptableContentTypes())) {
            $fractal->setSerializer(new JsonApiSerializer());
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
     * @return TransformerAbstract
     */
    protected function transformer()
    {
        return $this->transformer;
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
     * @return \Closure
     */
    protected function getPaginatorRouteGenerator()
    {
        return function() {
            return null;
        };
    }
}
