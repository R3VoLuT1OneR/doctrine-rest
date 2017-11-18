<?php namespace Pz\Doctrine\Rest;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class RestResponseFactory
{
    /**
     * Return configured fractal by request format.
     *
     * @param RestRequestContract $request
     *
     * @return Manager
     */
    protected static function fractal(RestRequestContract $request)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer($request->getBaseUrl()));

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

    /**
     * @param RestRequestContract    $request
     * @param ResourceInterface|null $resource
     * @param int                    $httStatus
     * @param array                  $headers
     *
     * @return RestResponse
     */
    public function resource(
        RestRequestContract         $request,
        ResourceInterface   $resource = null,
        int                 $httStatus = RestResponse::HTTP_OK,
        array               $headers = []
    ) {
        $data = $resource ? $this->fractal($request)->createData($resource)->toArray() : null;

        return RestResponse::create($data, $httStatus, $headers);
    }
}
