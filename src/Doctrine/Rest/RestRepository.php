<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class RestRepository extends EntityRepository
{
    /**
     * @var string
     */
    protected $alias;

    protected $resourceKey;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function sourceQueryBuilder()
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
            if ($resourceKey = $this->getResourceKey()) {
                $this->alias = $resourceKey;
            } else {
                // Camel case to underscore-case
                $this->alias = strtolower(
                    preg_replace(
                        '/(?<!^)[A-Z]/', '_$0',
                        $this->getClassMetadata()->getReflectionClass()->getShortName()
                    )
                );
            }
        }

        return $this->alias;
    }

    /**
     * @param RestRequestContract $request
     *
     * @return null|object
     * @throws EntityNotFoundException
     */
    public function findByIdentifier(RestRequestContract $request)
    {
        if (null === ($entity = $this->find($request->getId()))) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->getClassName(),
                ['id' => $request->getId()]
            );
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
