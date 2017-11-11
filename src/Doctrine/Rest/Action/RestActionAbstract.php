<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;

abstract class RestActionAbstract
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
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    abstract protected function handle(RestRequest $request);

    /**
     * RestActionAbstract constructor.
     *
     * @param RestRepository      $repository
     * @param RestResponseFactory $response
     */
    public function __construct(RestRepository $repository, RestResponseFactory $response)
    {
        $this->repository = $repository;
        $this->response = $response;
    }

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function dispatch(RestRequest $request)
    {
        try {
            return $this->handle($request);
        } catch (\Exception $e) {
            return $this->response()->exception($e);
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
}
