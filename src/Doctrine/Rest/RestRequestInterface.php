<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Request;

interface RestRequestInterface
{
    /**
     * Authorize rest request.
     * Entity will be object for get,update,delete actions.
     * Entity will be string for index,create action.
     *
     * @param object|string $entity
     *
     * @return mixed
     */
    public function authorize($entity);

    /**
     * @return Request
     */
    public function http();
}
