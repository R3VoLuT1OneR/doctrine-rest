<?php namespace Pz\Doctrine\Rest;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;

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
    abstract protected function handle($request);

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
     * @param JsonApiResource $entity
     * @param string          $property
     *
     * @return mixed
     * @throws RestException
     */
    protected function getProperty(JsonApiResource $entity, $property)
    {
        $getter = 'get' . ucfirst($property);

        if (!method_exists($entity, $getter)) {
            throw RestException::missingGetter($entity, $property, $getter);
        }

        return $entity->$getter();
    }

    /**
     * @param JsonApiResource $entity
     * @param string          $property
     * @param mixed           $value
     *
     * @return mixed
     * @throws RestException
     */
    protected function setProperty(JsonApiResource $entity, $property, $value)
    {
        $setter = 'set' . ucfirst($property);

        if (!method_exists($entity, $setter)) {
            throw RestException::missingSetter($entity, $property, $setter);
        }

        return $entity->$setter($value);
    }

    /**
     * @param JsonApiResource $entity
     * @param string          $field
     * @param object          $item
     *
     * @return mixed
     * @throws RestException
     */
    protected function addRelationItem(JsonApiResource $entity, $field, $item)
    {
        $adder = 'add' . ucfirst($field);

        if (!method_exists($entity, $adder)) {
            throw RestException::missingAdder($entity, $field, $adder);
        }

        return $entity->$adder($item);
    }

    /**
     * @param JsonApiResource $entity
     * @param string          $field
     * @param object          $item
     *
     * @return mixed
     * @throws RestException
     */
    protected function removeRelationItem(JsonApiResource $entity, $field, $item)
    {
        $remover = 'remove' . ucfirst($field);

        if (!method_exists($entity, $remover)) {
            throw RestException::missingRemover($entity, $field, $remover);
        }

        return $entity->$remover($item);
    }
}
