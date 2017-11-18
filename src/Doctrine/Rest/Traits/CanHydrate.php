<?php namespace Pz\Doctrine\Rest\Traits;

use Doctrine\Common\Collections\ArrayCollection;
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
    public function hydrateEntity($entity, array $data, $scope = 'root')
    {
        $entity = is_object($entity) ? $entity : new $entity;

        if (!isset($data['attributes']) || !is_array($data['attributes'])) {
            throw RestException::missingAttributes($scope);
        }

        $entity = $this->hydrateAttributes($entity, $data['attributes'], $scope);

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $entity = $this->hydrateRelationships($entity, $data['relationships'], $scope);
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
    private function hydrateAttributes($entity, array $attributes, $scope = 'root')
    {
        $metadata = $this->repository()->getClassMetadata();
        foreach ($attributes as $name => $value) {
            if (!isset($metadata->reflFields[$name])) {
                throw RestException::unknownAttribute(sprintf('%s.attribute.%s', $scope, $name));
            }

            $this->setProperty($entity, $name, $value);
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
        $metadata = $this->repository()->getClassMetadata();

        foreach ($relationships as $name => $data) {
            $relationScope = sprintf('%s.relation.%s', $scope, $name);

            if (!isset($metadata->associationMappings[$name])) {
                throw RestException::unknownRelation($relationScope);
            }

            $mapping = $metadata->associationMappings[$name];

            if (!isset($data['data'])) {
                throw RestException::missingData($relationScope);
            }

            $this->hydrateRelation($entity, $name, $mapping, $data['data'], $relationScope);
        }

        return $entity;
    }

    /**
     * Hydrate one relation.
     *
     * @param object $entity
     * @param string $name      Relation name
     * @param array  $mapping   Doctrine relation target mapping
     * @param mixed  $data
     * @param string $scope
     *
     * @return object
     * @throws RestException
     */
    private function hydrateRelation($entity, $name, $mapping, $data, $scope)
    {
        $mappingClass = $mapping['targetEntity'];

        if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
            $this->setProperty($entity, $name,
                $this->hydrateRelationData($mappingClass, $data, $scope)
            );
        }

        if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
            if (!is_array($data)) {
                throw RestException::missingData($scope);
            }

            $this->setProperty($entity, $name,
                new ArrayCollection(array_map(
                    function($data, $index) use ($mappingClass, $scope) {
                        return $this->hydrateRelationData(
                            $mappingClass, $data, sprintf('%s.%s', $scope, $index)
                        );
                    },
                    $data
                ))
            );
        }

        return $entity;
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
        if (is_scalar($data)) {
            return $this->repository()->getEntityManager()->getReference($class, $data);
        }

        if (!is_array($data)) {
            throw RestException::missingData($scope);
        }

        if (isset($data['id']) && isset($data['type'])) {
            return $this->repository()->getEntityManager()->getReference($class, $data['id']);
        } else {
            return $this->hydrateEntity($class, $data, $scope);
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
    private function setProperty($entity, $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (!method_exists($entity, $setter)) {
            throw RestException::createUnprocessable(sprintf('Setter not found for entity'));
        }

        return $entity->$setter($value);
    }
}
