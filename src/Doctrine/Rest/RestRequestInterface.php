<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Request;

interface RestRequestInterface
{
    /**
     * Json API type.
     */
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Determine if it is json api.
     *
     * @return bool
     */
    public function isJsonApi();

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
     * Get jsonapi fieldsets.
     *
     * @return array
     */
    public function getFields();

    /**
     * @return Request
     */
    public function http();
}
