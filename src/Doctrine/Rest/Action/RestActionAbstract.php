<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequestAbstract;
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
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    abstract public function handle(RestRequestAbstract $request);

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
