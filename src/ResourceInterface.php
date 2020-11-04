<?php namespace Doctrine\Rest;

interface ResourceInterface
{
    /**
     * Get fractal resource key.
     * JSON API `type`
     *
     * @return string
     */
    public static function getResourceType();

    /**
     * JSON API `id`
     *
     * @return int|mixed
     */
    public function getId();
}