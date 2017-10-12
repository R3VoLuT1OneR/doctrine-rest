<?php namespace Pz\Doctrine\Rest\Request;

use Pz\Doctrine\Rest\RestRequestInterface;

interface IndexRequestInterface extends RestRequestInterface
{
    const ACTION = 'index';

    /**
     * Get sql limit.
     *
     * @return null|int
     */
    public function getLimit();

    /**
     * Get sql limit start.
     *
     * @return null|int
     */
    public function getStart();

    /**
     * Get sql order by.
     * 
     * @return null|array
     */
    public function getOrderBy();

    /**
     * Get filters query.
     *
     * @return mixed
     */
    public function getQuery();
}
