<?php namespace Pz\Doctrine\Rest\Fractal;

use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class JsonApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    /**
     * @var RestRequestContract
     */
    protected $request;

    /**
     * JsonApiSerializer constructor.
     *
     * @param RestRequestContract $request
     */
    public function __construct(RestRequestContract $request)
    {
        parent::__construct($request->getBaseUrl());
        $this->request = $request;
    }

    /**
     * @param string $resourceKey
     * @param array  $data
     * @param bool   $includeAttributes
     *
     * @return array
     */
    public function collection($resourceKey, array $data, $includeAttributes = true)
    {
        $resources = [];

        foreach ($data as $resource) {
            $resources[] = $this->item($resourceKey, $resource, $includeAttributes)['data'];
        }

        return ['data' => $resources];
    }

    /**
     * @param string $resourceKey
     * @param array  $data
     * @param bool   $includeAttributes
     *
     * @return array
     */
    public function item($resourceKey, array $data, $includeAttributes = true)
    {
        $item = parent::item($resourceKey, $data);
        if (!$includeAttributes) {
            unset($item['data']['attributes']);
        }

        return $item;
    }
}
