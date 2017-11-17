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
    protected function hydrateData($entity, array $data, $scope = 'root')
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
    protected function hydrateAttributes($entity, array $attributes, $scope = 'root')
    {
        $metadata = $this->repository()->getClassMetadata();
        foreach ($attributes as $name => $value) {
            if (!isset($metadata->columnNames[$name])) {
                throw RestException::unknownAttribute(sprintf('%s.attribute.%s', $scope, $name));
            }

            $metadata->reflFields[$name]->setValue($entity, $value);
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
    protected function hydrateRelationships($entity, array $relationships, $scope)
    {
        $metadata = $this->repository()->getClassMetadata();

        foreach ($relationships as $name => $data) {
            $relationScope = sprintf('%s.relation.%s', $scope, $name);

            if (!isset($metadata->associationMappings[$name])) {
                throw RestException::unknownRelation($relationScope);
            }

            $mapping = $metadata->associationMappings[$name];
            $mappingClass = $mapping['targetEntity'];

            if (!isset($data['data'])) {
                throw RestException::missingData($relationScope);
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                $metadata->reflFields[$name]->setValue($entity,
                    $this->hydrateRelationData($mappingClass, $data['data'], $relationScope)
                );
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                if (!is_array($data['data'])) {
                    throw RestException::missingData($relationScope);
                }

                $metadata->reflFields[$name]->setValue($entity,
                    new ArrayCollection(array_map(
                        function($data, $index) use ($mappingClass, $relationScope) {
                            return $this->hydrateRelationData(
                                $mappingClass, $data, sprintf('%s.%s', $relationScope, $index)
                            );
                        },
                        $data['data']
                    ))
                );
            }
        }

        return $entity;
    }

    /**
     * @param        $class
     * @param        $data
     * @param string $scope
     *
     * @return object
     * @throws RestException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function hydrateRelationData($class, $data, $scope = 'root')
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
            return $this->hydrateData($class, $data, $scope);
        }
    }
}
