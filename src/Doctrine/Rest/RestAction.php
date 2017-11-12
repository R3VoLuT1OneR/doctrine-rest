<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityNotFoundException;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
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

        } catch (EntityNotFoundException $e) {
            return RestResponse::notFound($e->getMessage());
        } catch (RestException $e) {
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

    /**
     * @param RestRequestContract $request
     * @param JsonApiResource     $resource
     *
     * @return string|null
     */
    protected function linkJsonApiResource(RestRequestContract $request, JsonApiResource $resource)
    {
        return sprintf('%s/%s/%s', $request->http()->getBaseUrl(), $resource->getResourceKey(), $resource->getId());
    }

    /**
     * @param $entity
     *
     * @return null|string
     */
    protected function getResourceKey($entity)
    {
        if (is_string($entity) && isset(class_implements($entity)[JsonApiResource::class])) {
            return call_user_func("$entity::getResourceKey");
        }

        if (is_object($entity) && $entity instanceof JsonApiResource) {
            return $entity->getResourceKey();
        }

        return null;
    }
}
