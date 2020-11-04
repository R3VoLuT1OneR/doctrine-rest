<?php namespace Doctrine\Rest;

use Exception;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\Exceptions\RestException;
use Psr\Http\Message\ResponseInterface;

interface RestResponse extends ResponseInterface
{
    /**
     * Return no content response.
     */
    public static function noContent(): self;

    /**
     * Return forbidden response.
     */
    public static function createForbidden(): self;

    /**
     * Generate JSON:API response from exception.
     *
     * @param Exception $exception
     * @return static
     */
    public static function exception(Exception $exception): self;
}
