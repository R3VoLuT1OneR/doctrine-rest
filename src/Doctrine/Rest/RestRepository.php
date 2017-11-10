<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Pz\Doctrine\Rest\Contracts\HasResourceKey;

class RestRepository extends EntityRepository
{
    /**
     * @var string
     */
    protected $rootAlias;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }

    /**
     * Base root alias for queries.
     *
     * @return string
     */
    public function alias()
    {
        if ($this->rootAlias === null) {
            $reflectionClass = $this->getClassMetadata()->getReflectionClass();
            if ($reflectionClass->implementsInterface(HasResourceKey::class)) {
                $this->rootAlias = call_user_func($reflectionClass->getName(). '::getResourceKey');
            } else {
                // Camel case to underscore-case
                $this->rootAlias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $reflectionClass->getShortName()));
            }
        }

        return $this->rootAlias;
    }

    /**
     * @param RestRequestAbstract $request
     *
     * @return null|object
     * @throws EntityNotFoundException
     */
    public function findByIdentifier(RestRequestAbstract $request)
    {
        $id = array_map(
            function($idField) use ($request) {
                return $request->get($idField);
            },
            $this->getClassMetadata()->getIdentifier()
        );

        if (null === ($entity = $this->find($id))) {
            throw new EntityNotFoundException($this->getClassName(), $id);
        }

        return $entity;
    }
}
