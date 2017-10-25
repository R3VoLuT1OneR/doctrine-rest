<?php namespace Pz\Doctrine\Rest\Response;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\Action\Index\ResponseDataInterface;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequestInterface;
use Pz\Doctrine\Rest\RestResponseFactory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FractalResponse implements RestResponseFactory
{
    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @var Manager|null
     */
    protected $fractal;

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
     * @param IndexRequestInterface $request
     * @param ResponseDataInterface $response
     *
     * @return array
     */
    public function index(IndexRequestInterface $request, ResponseDataInterface $response)
    {
        $resource = new Collection($response->data(), $this->transformer());
        $resource->setMetaValue('count', $response->count());
        $resource->setMetaValue('limit', $response->limit());
        $resource->setMetaValue('start', $response->start());

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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return JsonResponse
     */
    public function delete(DeleteRequestInterface $request, $entity)
    {
        return $this->response();
    }

    /**
     * @param RestRequestInterface $request
     *
     * @return JsonResponse
     */
    public function notFound(RestRequestInterface $request)
    {
        return $this->response(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @param \Error|\Exception|\Pz\Doctrine\Rest\RestException $exception
     *
     * @return JsonResponse
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
                $httpStatus = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
                $errors = $exception->getTrace();
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
    protected function fractal(/** @scrutinizer ignore-unused */ RestRequestInterface $request)
    {
        if ($this->fractal === null) {
            $this->fractal = new Manager();
        }

        return $this->fractal;
    }

    /**
     * @return TransformerAbstract
     */
    protected function transformer()
    {
        return $this->transformer;
    }

    /**
     * @param null $data
     * @param int  $httStatus
     *
     * @return JsonResponse
     */
    protected function response($data = null, $httStatus = Response::HTTP_OK)
    {
        return new JsonResponse($data, $httStatus);
    }
}
