<?php namespace Pz\Doctrine\Rest\Contracts;

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
     * Provide base API url
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * @return bool
     */
    public function isAcceptJsonApi();

    /**
     * @return bool
     */
    public function isContentJsonApi();

    /**
     * Return parser JSON body request.
     *
     * @return array
     */
    public function all();

    /**
     * JSON API id ( got from query sting for GET and PUT and from data id for POST
     *
     * @return int|array
     */
    public function getId();

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
