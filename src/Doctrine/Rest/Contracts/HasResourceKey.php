<?php namespace Pz\Doctrine\Rest\Contracts;

interface HasResourceKey
{
    /**
     * @return string
     */
    public static function getResourceKey();
}
