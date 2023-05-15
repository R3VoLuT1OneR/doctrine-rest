<?php namespace Pz\Doctrine\Rest\Fractal;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\Serializer;

class Scope extends \League\Fractal\Scope
{
    /**
     * @var bool
     */
    protected $isRelationships = false;

    /**
     * @param null|bool $value
     *
     * @return bool|null
     */
    public function isRelationships($value = null)
    {
        if ($value !== null) {
            $this->isRelationships = $value;
        }

        return $this->isRelationships;
    }

    /**
     * @param JsonApiSerializer|SerializerAbstract $serializer
     * @param mixed                                $data
     *
     * @return array
     */
    protected function serializeResource(Serializer $serializer, $data): array|null
    {
        $includeAttributes = true;
        $resourceKey = $this->resource->getResourceKey();
        if ($this->isRelationships() && $this->isRootScope()) {
            $includeAttributes = false;
        }

        if ($this->resource instanceof Collection) {
            return $serializer->collection($resourceKey, $data, $includeAttributes);
        }

        if ($this->resource instanceof Item) {
            return $serializer->item($resourceKey, $data, $includeAttributes);
        }

        return $serializer->null();
    }
}
