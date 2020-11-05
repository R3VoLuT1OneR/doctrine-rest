<?php namespace Doctrine\Rest;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Rest\Exceptions\EntityNotFoundException;
use Doctrine\Rest\Fractal\BaseManagerFactory;
use Doctrine\Rest\Fractal\ManagerFactoryInterface;
use Doctrine\Rest\Resource\AbstractTransformer;
use Doctrine\Rest\Util\ResourceUtil;
use Doctrine\Rest\Resource\Item;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class RestAction implements RequestHandlerInterface
{
    const ATTR_ID = 'id';

    protected ObjectRepository $repository;
    protected AbstractTransformer $transformer;

    protected ManagerFactoryInterface $managerFactory;
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamInterface;

    /**
     * Attribute name that will save the parsed ID from path.
     */
    protected string $attributeId = self::ATTR_ID;

    static public function create(ObjectRepository $repository, AbstractTransformer $transformer): self
    {
        return new static($repository, $transformer);
    }

    public function __construct(ObjectRepository $repository, AbstractTransformer $transformer)
    {
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

    public function setAttributeId(string $attributeId): self
    {
        $this->attributeId = $attributeId;
        return $this;
    }

    public function getAttributeId(): string
    {
        return $this->attributeId;
    }

    public function setManagerFactory(ManagerFactoryInterface $factory): self
    {
        $this->managerFactory = $factory;
        return $this;
    }

    public function getManagerFactory(): ManagerFactoryInterface
    {
        if (!isset($this->managerFactory)) {
            $this->managerFactory = new BaseManagerFactory();
        }

        return $this->managerFactory;
    }

    public function setResponseFactory(ResponseFactoryInterface $factory): self
    {
        $this->responseFactory = $factory;
        return $this;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    public function setStreamInterface(StreamFactoryInterface $streamInterface): self
    {
        $this->streamInterface = $streamInterface;
        return $this;
    }

    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamInterface;
    }

    /**
     * @param mixed $id
     * @return ResourceInterface
     * @throws EntityNotFoundException
     */
    public function findById($id): ResourceInterface
    {
        if (null === ($entity = $this->repository()->find($id))) {
            $class = $this->repository()->getClassName();
            $type = ResourceUtil::resourceTypeByClass($class);
            throw new EntityNotFoundException($type, $id);
        }

        if (!$entity instanceof ResourceInterface) {
            throw ResourceUtil::notResourceException(get_class($entity));
        }

        return $entity;
    }

    public function buildResponseFromResource(
        ResourceInterface $resource,
        ServerRequestInterface $request,
        int $status = 200,
        string $phrase = 'OK'
    ): ResponseInterface
    {
        return $this->getResponseFactory()
            ->createResponse($status, $phrase)
            ->withBody(
                $this->getStreamFactory()
                    ->createStream(
                        $this->getManagerFactory()
                            ->createManager($request)
                            ->createData(new Item($resource, $this->transformer()))
                            ->toJson()
                    )
            );
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
