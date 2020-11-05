<?php namespace Doctrine\Rest\Fractal;

use League\Fractal\Manager;
use Psr\Http\Message\ServerRequestInterface;

interface ManagerFactoryInterface
{
    /**
     * Generate fractal manager.
     *
     * @param ServerRequestInterface $request
     * @return Manager
     */
    public function createManager(ServerRequestInterface $request): Manager;
}