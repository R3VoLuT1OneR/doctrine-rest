<?php namespace Pz\Doctrine\Rest\Contracts;

use Symfony\Component\HttpFoundation\Request;

interface RestRequestContract
{
    /**
     * Json API type.
     */
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Default limit for list.
     */
    const DEFAULT_LIMIT = 1000;

    /**
     * @return Request
     */
    public function http();

    /**
     * The base URL for the api.
     *
     * Example:
     *   https://example.domain.com/jsonapi
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * JSON API id ( got from query sting for GET and PUT and from data id for POST
     *
     * @return int|array
     */
    public function getId();

    /**
     * Set or get identifier that request is relationships type and shouln't return data.
     *
     * @param bool $value
     *
     * @return bool
     */
    public function isRelationships($value = null);

    /**
     * JSON API data
     *
     * @return array
     */
    public function getData();

    /**
     * Get jsonapi fieldsets.
     *
     * @return array
     */
    public function getFields();

    /**
     * @return mixed|null
     */
    public function getFilter();

    /**
     * @return array|null
     */
    public function getOrderBy();

    /**
     * @return int|null
     */
    public function getStart();

    /**
     * @return int|null
     */
    public function getLimit();

    /**
     * @return array|string|null
     */
    public function getInclude();

    /**
     * @return array|string|null
     */
    public function getExclude();
}
