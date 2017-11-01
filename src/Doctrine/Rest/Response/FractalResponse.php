<?php namespace Pz\Doctrine\Rest\Response;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
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
    public function index(IndexRequestInterface $request, QueryBuilder $qb)
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
     * @param ShowRequestInterface $request
     * @param             $entity
     *
     * @return RestResponse
     */
    public function show(ShowRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param CreateRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function create(CreateRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param UpdateRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function update(UpdateRequestInterface $request, $entity)
    {
        return $this->response(
            $this->fractal($request)
                ->createData(new Item($entity, $this->transformer()))
                ->toArray()
        );
    }

    /**
     * @param DeleteRequestInterface $request
     * @param               $entity
     *
     * @return RestResponse
     */
    public function delete(DeleteRequestInterface $request, $entity)
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
