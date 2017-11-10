<?php namespace Doctrine\Rest\Action;

use App\JsonApiHydrator;
use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use Pz\Doctrine\Rest\RestRequestAbstract;

trait CanHydrate
{
    /**
     * @param string|object       $entity
     * @param EntityManager       $em
     * @param RestRequestAbstract $request
     *
     * @return object
     * @throws \Exception
     */
    protected function hydrate($entity, EntityManager $em, RestRequestAbstract $request)
    {
        $hydrator = $request->getContentType() === RestRequestAbstract::JSON_API_CONTENT_TYPE ?
            new JsonApiHydrator($em) : new ArrayHydrator($em);

        return $hydrator->hydrate($entity, $request->request->all());
    }
}
