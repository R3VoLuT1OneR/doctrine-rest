<?php namespace Pz\Doctrine\Rest;

use Pz\Doctrine\Rest\Exceptions\RestException;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestResponse extends JsonResponse
{
    /**
     * @return static
     */
    public static function noContent()
    {
        return static::create(null, static::HTTP_NO_CONTENT);
    }

    /**
     * @param \Error|\Exception|RestException $exception
     *
     * @return RestResponse
     * @throws \Error|\Exception|RestException
     */
    public static function exception(\Exception $exception)
    {
        if (!$exception instanceof RestException) {
            $exception = RestException::createFromException($exception);
        }

        return RestResponse::create(['errors' => $exception->errors()], $exception->getCode());
    }
}
