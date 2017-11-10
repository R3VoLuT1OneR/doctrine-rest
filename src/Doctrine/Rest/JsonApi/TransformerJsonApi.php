<?php namespace Doctrine\Rest\JsonApi;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use League\Fractal\TransformerAbstract;

class TransformerJsonApi extends TransformerAbstract
{
    /**
     * JSON API `data`
     *
     * @param JsonApiResource $entity
     *
     * @return array
     */
    public function transform(JsonApiResource $entity)
    {
        return [
            'id'        => $entity->getId(),
            'type'      => $entity->getType(),
        ];
    }
}
