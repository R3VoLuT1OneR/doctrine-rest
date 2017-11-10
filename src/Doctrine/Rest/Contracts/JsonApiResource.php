<?php namespace Doctrine\Rest\Contracts;

interface JsonApiResource
{
    /**
     * JSON API `id`
     *
     * @return int|mixed
     */
    public function getId();

    /**
     * JSON API `type`
     *
     * @return string
     */
    public function getType();
}
