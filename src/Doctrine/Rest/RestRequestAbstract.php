<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Request;

abstract class RestRequestAbstract extends Request
{
    /**
     * Json API type.
     */
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Authorize rest request.
     * Entity will be object for get,update,delete actions.
     * Entity will be string for index,create action.
     *
     * @param object|string $entity
     *
     * @return mixed
     */
    abstract public function authorize($entity);

    /**
     * Get jsonapi fieldsets.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->get('filter');
    }

    public function getOrderBy()
    {
        return $this->get('');
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return 0;
    }
}
