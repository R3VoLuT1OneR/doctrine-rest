<?php namespace Doctrine\Rest\Util;

use InvalidArgumentException;
use UnexpectedValueException;
use Doctrine\Rest\ResourceInterface;

class ResourceUtil
{
    const RESOURCE_TYPE_METHOD = 'getResourceType';

    /**
     * Get resource type by entity class.
     *
     * @param string $class
     * @return string
     */
    static public function resourceTypeByClass(string $class): string
    {
        static::verifyClassResource($class);

        return call_user_func(sprintf('%s::%s', $class, static::RESOURCE_TYPE_METHOD));
    }

    /**
     * Make sure class is implements resource interface.
     *
     * @param string $class
     */
    static public function verifyClassResource(string $class): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('%s - is not a class', $class));
        }

        if (!isset(class_implements($class)[ResourceInterface::class])) {
            throw static::notResourceException($class);
        }
    }

    /**
     * Build exception for wrong resource class.
     *
     * @param string $class
     * @return UnexpectedValueException
     */
    static public function notResourceException(string $class): UnexpectedValueException
    {
        return new UnexpectedValueException(sprintf('%s - not implements %s', $class, ResourceInterface::class));
    }
}