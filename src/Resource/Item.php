<?php namespace Doctrine\Rest\Resource;

use Doctrine\Rest\ResourceInterface;

class Item extends \League\Fractal\Resource\Item
{
    public function __construct(ResourceInterface $resource, $transformer)
    {
        parent::__construct($resource, $transformer, $resource->getResourceType());
    }
}
