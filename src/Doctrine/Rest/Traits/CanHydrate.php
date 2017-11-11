<?php namespace Pz\Doctrine\Rest\Traits;

use App\JsonApiHydrator;
use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;
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
        $hydrator = $request->isAcceptJsonApi() ? new JsonApiHydrator($em) : new ArrayHydrator($em);

        return $hydrator->hydrate($entity, $request->request->all());
    }
}
