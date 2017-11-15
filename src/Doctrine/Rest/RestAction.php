<?php namespace Pz\Doctrine\Rest;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

abstract class RestAction
{
    /**
     * @var RestRepository
     */
    protected $repository;

    /**
     * @var RestResponseFactory
     */
    protected $response;

    /**
     * @var TransformerAbstract|\Closure
     */
    protected $transformer;

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    abstract protected function handle(RestRequestContract $request);

    /**
     * RestActionAbstract constructor.
     *
     * @param RestRepository               $repository
     * @param TransformerAbstract|\Closure $transformer
     */
    public function __construct(RestRepository $repository, $transformer)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->response = new RestResponseFactory();
    }

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function dispatch(RestRequestContract $request)
    {
        try {

            return $this->handle($request);

        } catch (\Exception $e) {
            return RestResponse::exception($e);
        }
    }

    /**
     * @return RestRepository
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return RestResponseFactory
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @return TransformerAbstract|\Closure
     */
    public function transformer()
    {
        return $this->transformer;
    }

    /**
     * Authorize rest request.
     * Entity will be object for get,update,delete actions.
     * Entity will be string for index,create action.
     *
     * @param RestRequestContract $request
     * @param object|string       $entity
     *
     * @return mixed
     */
    public function authorize(/** @scrutinizer ignore-unused */$request, /** @scrutinizer ignore-unused */$entity)
    {
        return true;
    }
}
