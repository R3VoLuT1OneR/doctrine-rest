<?php namespace Pz\Doctrine\Rest\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;

class ScopeFactory extends \League\Fractal\ScopeFactory
{
    /**
     * @param \Pz\Doctrine\Rest\Fractal\Manager|Manager $manager
     * @param ResourceInterface                         $resource
     * @param null                                      $scopeIdentifier
     *
     * @return Scope
     */
    public function createScopeFor(Manager $manager, ResourceInterface $resource, $scopeIdentifier = null): \League\Fractal\Scope
    {
        $scope = new Scope($manager, $resource, $scopeIdentifier);
        $scope->isRelationships($manager->request()->isRelationships());

        return $scope;
    }
}
