<?php namespace Pz\Doctrine\Rest\Resource;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;

class Item extends \League\Fractal\Resource\Item
{
    /**
     * Item constructor.
     *
     * @param JsonApiResource                                   $resource
     * @param callable|\League\Fractal\TransformerAbstract|null $transformer
     */
    public function __construct(JsonApiResource $resource, $transformer)
    {
        parent::__construct($resource, $transformer, $resource->getResourceKey());
    }
}
