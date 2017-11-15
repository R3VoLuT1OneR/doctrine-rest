<?php namespace Pz\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use pmill\Doctrine\Hydrator\JsonApiHydrator;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Symfony\Component\HttpFoundation\Response;

trait CanHydrate
{
    /**
     * @param string|object       $entity
     * @param EntityManager       $em
     * @param RestRequestContract $request
     *
     * @return object
     * @throws \Exception
     */
    protected function hydrate($entity, EntityManager $em, RestRequestContract $request)
    {
        $all = $request->all();

        if ($request->isContentJsonApi()) {
            if (!isset($all['data']) || !is_array($all['data'])) {
                throw RestException::missingRootData();
            }

            return (new JsonApiHydrator($em))->hydrate($entity, $all['data']);
        }

        return (new ArrayHydrator($em))->hydrate($entity, $all);
    }
}
