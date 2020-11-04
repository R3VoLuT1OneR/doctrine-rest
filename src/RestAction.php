<?php namespace Doctrine\Rest;

//use League\Fractal\TransformerAbstract;
//use Doctrine\Rest\Contracts\JsonApiResource;
//use Doctrine\Rest\Contracts\RestRequestContract;
//use Doctrine\Rest\Exceptions\RestException;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Rest\Contracts\JsonApiResource;
use Doctrine\Rest\Exceptions\RestException;
use Doctrine\Rest\Resource\AbstractTransformer;
use League\Fractal\TransformerAbstract;

use Psr\Http\Server\RequestHandlerInterface;
use Closure;

abstract class RestAction implements RequestHandlerInterface
{
    protected ObjectRepository $repository;
    protected AbstractTransformer $transformer;

    public function __construct(
        ObjectRepository $repository,
        AbstractTransformer $transformer
    ) {
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    public function repository(): ObjectRepository
    {
        return $this->repository;
    }

    public function transformer(): AbstractTransformer
    {
        return $this->transformer;
    }

//    public function findById($id)
//    {
//        if (null === ($entity = $this->repository()->find($id))) {
//            throw RestException::createNotFound($id, $this->getResourceKey(), sprintf(
//                'Entity of type `%s` not found.', $this->getClassName()
//            ));
//        }
//
//        if (!$entity instanceof JsonApiResource) {
//            throw RestException::notJsonApiResource($entity);
//        }
//
//        return $entity;
//    }
//    /**
//     * @param JsonApiResource $entity
//     * @param string          $property
//     *
//     * @return mixed
//     * @throws RestException
//     */
//    protected function getProperty(JsonApiResource $entity, $property)
//    {
//        $getter = 'get' . ucfirst($property);
//
//        if (!method_exists($entity, $getter)) {
//            throw RestException::missingGetter($entity, $property, $getter);
//        }
//
//        return $entity->$getter();
//    }
//
//    /**
//     * @param JsonApiResource $entity
//     * @param string          $property
//     * @param mixed           $value
//     *
//     * @return mixed
//     * @throws RestException
//     */
//    protected function setProperty(JsonApiResource $entity, $property, $value)
//    {
//        $setter = 'set' . ucfirst($property);
//
//        if (!method_exists($entity, $setter)) {
//            throw RestException::missingSetter($entity, $property, $setter);
//        }
//
//        return $entity->$setter($value);
//    }
//
//    /**
//     * @param JsonApiResource $entity
//     * @param string          $field
//     * @param object          $item
//     *
//     * @return mixed
//     * @throws RestException
//     */
//    protected function addRelationItem(JsonApiResource $entity, $field, $item)
//    {
//        $adder = 'add' . ucfirst($field);
//
//        if (!method_exists($entity, $adder)) {
//            throw RestException::missingAdder($entity, $field, $adder);
//        }
//
//        return $entity->$adder($item);
//    }
//
//    /**
//     * @param JsonApiResource $entity
//     * @param string          $field
//     * @param object          $item
//     *
//     * @return mixed
//     * @throws RestException
//     */
//    protected function removeRelationItem(JsonApiResource $entity, $field, $item)
//    {
//        $remover = 'remove' . ucfirst($field);
//
//        if (!method_exists($entity, $remover)) {
//            throw RestException::missingRemover($entity, $field, $remover);
//        }
//
//        return $entity->$remover($item);
//    }
}
