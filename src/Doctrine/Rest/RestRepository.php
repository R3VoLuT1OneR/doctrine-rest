<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;

class RestRepository extends EntityRepository
{
    /**
     * @var string
     */
    protected $alias;

    protected $resourceKey;

    /**
     * @param EntityManager $em
     * @param string        $class
     *
     * @return RestRepository
     */
    public static function create(EntityManager $em, $class)
    {
        return new RestRepository($em, $em->getClassMetadata($class));
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

    /**
     * @param RestRequestContract $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function sourceQueryBuilder(RestRequestContract $request)
    {
        return $this->createQueryBuilder($this->alias());
    }

    /**
     * @return string|null
     */
    public function getResourceKey()
    {
        if (isset(class_implements($this->getClassName())[JsonApiResource::class])) {
            return call_user_func("{$this->getClassName()}::getResourceKey");
        }

        return null;
    }

    /**
     * Base root alias for queries.
     *
     * @return string
     */
    public function alias()
    {
        if ($this->alias === null) {
            $this->alias = strtolower(
                    preg_replace(
                    '/(?<!^)[A-Z]/', '_$0',
                    $this->getClassMetadata()->getReflectionClass()->getShortName()
                )
            );
        }

        return $this->alias;
    }

    /**
     * @param mixed $id
     *
     * @return JsonApiResource
     * @throws RestException
     */
    public function findById($id)
    {
        if (null === ($entity = $this->find($id))) {
            throw RestException::createNotFound($id, $this->getResourceKey(), sprintf(
                'Entity of type `%s` not found.', $this->getClassName()
            ));
        }

        if (!$entity instanceof JsonApiResource) {
            throw RestException::notJsonApiResource($entity);
        }

        return $entity;
    }

    /**
     * @param RestRequestContract $request
     * @param JsonApiResource     $resource
     *
     * @return string
     */
    public function linkJsonApiResource(RestRequestContract $request, JsonApiResource $resource)
    {
        return sprintf('%s/%s/%s', $request->getBaseUrl(), $resource->getResourceKey(), $resource->getId());
    }
}
