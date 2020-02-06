<?php namespace Pz\Doctrine\Rest;

use League\Fractal\Resource\ResourceInterface;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Fractal\JsonApiSerializer;
use Pz\Doctrine\Rest\Fractal\Manager;
use Pz\Doctrine\Rest\Fractal\ScopeFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestResponseFactory
{
    /**
     * @param RestRequestContract    $request
     * @param ResourceInterface|null $resource
     * @param int                    $httStatus
     * @param array                  $headers
     * @param int                    $jsonOptions
     *
     * @return RestResponse
     */
    static public function resource(
        RestRequestContract $request,
        ResourceInterface   $resource = null,
        int                 $httStatus = RestResponse::HTTP_OK,
        array               $headers = [],
        int                 $jsonOptions = JsonResponse::DEFAULT_ENCODING_OPTIONS
    ) {
        $data = $resource ? static::fractal($request)->createData($resource)->toArray() : null;

        $response = RestResponse::create($data, $httStatus, $headers);
        $response->setEncodingOptions($jsonOptions);

        return $response;
    }

    /**
     * @return RestResponse
     */
    static public function noContent()
    {
        return RestResponse::noContent();
    }

    /**
     * Return configured fractal by request format.
     *
     * @param RestRequestContract $request
     *
     * @return Manager
     */
    static protected function fractal(RestRequestContract $request)
    {
        $fractal = new Manager(new ScopeFactory(), $request);
        $fractal->setSerializer(new JsonApiSerializer($request));

        if ($includes = $request->getInclude()) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->getExclude()) {
            $fractal->parseExcludes($excludes);
        }

        if ($fields = $request->getFields()) {
            $fractal->parseFieldsets($fields);
        }

        return $fractal;
    }
}
