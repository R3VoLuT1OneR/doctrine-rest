<?php namespace Pz\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use pmill\Doctrine\Hydrator\JsonApiHydrator;
use Pz\Doctrine\Rest\RestRequest;

trait CanHydrate
{
    /**
     * @param string|object $entity
     * @param EntityManager $em
     * @param RestRequest   $request
     *
     * @return object
     * @throws \Exception
     */
    protected function hydrate($entity, EntityManager $em, RestRequest $request)
    {
        if ($request->isContentJsonApi()) {
            return (new JsonApiHydrator($em))->hydrate($entity, $request->http()->request->get('data'));
        }

        return (new ArrayHydrator($em))->hydrate($entity, $request->http()->request->all());
    }
}
