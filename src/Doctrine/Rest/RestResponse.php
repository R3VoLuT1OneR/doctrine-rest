<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;

class RestResponse extends JsonResponse
{
    /**
     * @param string $message
     *
     * @return static
     */
    public static function notFound($message)
    {
        return static::create($message, static::HTTP_NOT_FOUND);
    }

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
    public static function exception(RestException $exception)
    {
        return RestResponse::create(['errors' => $exception->errors()], $exception->httpStatus());
    }
}
