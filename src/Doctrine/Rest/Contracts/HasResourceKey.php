<?php namespace Pz\Doctrine\Rest\Contracts;

interface HasResourceKey
{
    /**
     * Get fractal resource key.
     *
     * @return string
     */
    public static function getResourceKey();
}
