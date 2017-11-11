<?php namespace Pz\Doctrine\Rest\Contracts;

interface JsonApiResource
{
    /**
     * Get fractal resource key.
     * JSON API `type`
     *
     * @return string
     */
    public static function getResourceKey();

    /**
     * JSON API `id`
     *
     * @return int|mixed
     */
    public function getId();
}
