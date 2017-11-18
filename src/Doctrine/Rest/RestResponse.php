<?php namespace Pz\Doctrine\Rest;

use Pz\Doctrine\Rest\Contracts\RestRequestContract;
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
     * @return static
     */
    public static function createForbidden()
    {
        return static::create(null, static::HTTP_FORBIDDEN);
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

    /**
     * RestResponse constructor.
     *
     * @param mixed|null $data
     * @param int        $status
     * @param array      $headers
     */
    public function __construct($data, $status, array $headers = [])
    {
        $headers['Content-Type'] = RestRequestContract::JSON_API_CONTENT_TYPE;

        parent::__construct($data, $status, $headers, false);
    }
}
