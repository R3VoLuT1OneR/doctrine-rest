<?php namespace Pz\Doctrine\Rest\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;

trait CanHydrate
{
    /**
     * @return RestRepository
     */
    abstract public function repository();

    /**
     * @param string|object $entity
     * @param array         $data
     * @param string        $scope
     *
     * @return object
     * @throws RestException
     */
    public function hydrateEntity($entity, array $data, $scope = '')
    {
        $hydrated = false;
        $entity = is_object($entity) ? $entity : new $entity;

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $entity = $this->hydrateAttributes($entity, $data['attributes'], $scope);
            $hydrated = true;
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $entity = $this->hydrateRelationships($entity, $data['relationships'], $scope);
            $hydrated = true;
        }

        if (!$hydrated) {
            throw RestException::missingDataMembers($scope);
        }

        return $entity;
    }

    /**
     * @param        $entity
     * @param array  $attributes
     * @param string $scope
     *
     * @return mixed
     * @throws RestException
     */
    private function hydrateAttributes($entity, array $attributes, $scope = '')
    {
        $metadata = $this->repository()->getEntityManager()->getClassMetadata(ClassUtils::getClass($entity));

        foreach ($attributes as $name => $value) {
            if (!isset($metadata->reflFields[$name])) {
                throw RestException::unknownAttribute($scope.$name);
            }

            $this->setObjectProperty($entity, $name, $value);
        }

        return $entity;
    }

    /**
     * @param       $entity
     * @param array $relationships
     * @param       $scope
     *
     * @return mixed
     * @throws RestException
     */
    private function hydrateRelationships($entity, array $relationships, $scope)
    {
        $metadata = $this->repository()->getEntityManager()->getClassMetadata(ClassUtils::getClass($entity));

        foreach ($relationships as $name => $data) {
            if (!isset($metadata->associationMappings[$name])) {
                throw RestException::unknownRelation($scope.$name);
            }

            $mapping = $metadata->associationMappings[$name];

            if (!isset($data['data'])) {
                throw RestException::missingData($scope.$name);
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                $this->setObjectProperty($entity, $name,
                    $this->hydrateRelationData($mapping['targetEntity'], $data['data'], $scope.$name)
                );
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                $this->hydrateToManyRelation($entity, $name, $mapping['targetEntity'], $data['data'], $scope.$name);
            }
        }

        return $entity;
    }

    /**
     * Hydrate one relation.
     *
     * @param object $entity
     * @param string $name          Relation name
     * @param string $targetEntity  Doctrine relation class name
     * @param mixed  $data
     * @param string $scope
     *
     * @return object
     * @throws RestException
     */
    private function hydrateToManyRelation($entity, $name, $targetEntity, $data, $scope)
    {
        if (!is_array($data)) {
            throw RestException::missingData($scope);
        }

        $this->setObjectProperty($entity, $name,
            new ArrayCollection(array_map(
                function($item, $index) use ($targetEntity, $scope) {
                    return $this->hydrateRelationData($targetEntity, $item, $scope.'['.$index.']');
                },
                $data,
                array_keys($data)
            ))
        );
    }

    /**
     * @param string $class
     * @param mixed  $data
     * @param string $scope
     *
     * @return object
     * @throws RestException
     * @throws \Doctrine\ORM\ORMException
     */
    private function hydrateRelationData($class, $data, $scope)
    {
        if (is_object($data)) {
            return $data;
        }

        if (is_scalar($data)) {
            return $this->repository()->getEntityManager()->getReference($class, $data);
        }

        if (!is_array($data)) {
            throw RestException::missingData($scope);
        }

        if (isset($data['id']) && isset($data['type'])) {
            return $this->repository()->getEntityManager()->getReference($class, $data['id']);
        } else {
            return $this->hydrateEntity($class, $data, $scope.'.');
        }
    }

    /**
     * Set property on entity object.
     *
     * @param object $entity
     * @param string $name
     * @param mixed  $value
     *
     * @return object
     * @throws RestException
     */
    private function setObjectProperty($entity, $name, $value)
    {
        $setter = 'set'.ucfirst($name);

        if (!method_exists($entity, $setter)) {
            throw RestException::missingSetter($entity, $name, $setter);
        }

        return $entity->$setter($value);
    }
}
