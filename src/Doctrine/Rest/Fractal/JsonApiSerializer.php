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
    public function collection($resourceKey, array $data, $includeAttributes = true): array
    {
        $resources = [];

        foreach ($data as $resource) {

            /**
             * Add option to override resource key with data.
             */
            if (is_array($resource) && array_key_exists('_resource_key', $resource)) {
                $resourceKey = $resource['_resource_key'];
                unset($resource['_resource_key']);
            }

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
    public function item($resourceKey, array $data, $includeAttributes = true): array
    {
        $item = parent::item($resourceKey, $data);
        if (!$includeAttributes) {
            unset($item['data']['attributes']);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function injectAvailableIncludeData(array $data, array $availableIncludes): array
    {
        if (!$this->shouldIncludeLinks()) {
            return $data;
        }

        if ($this->isCollection($data)) {
            $data['data'] = array_map(function ($resource) use ($availableIncludes) {
                foreach ($availableIncludes as $relationshipKey) {
                    $resource = $this->addRelationshipLinks($resource, $relationshipKey);
                }
                return $resource;
            }, $data['data']);
        } else {
            foreach ($availableIncludes as $relationshipKey) {
                $data['data'] = $this->addRelationshipLinks($data['data'], $relationshipKey);
            }
        }

        return $data;
    }

    /**
     * Do not include links if there are no results.
     */
    private function addRelationshipLinks(array $resource, string $relationshipKey): array
    {
        if (isset($resource['relationships'][$relationshipKey])) {
            $resource['relationships'][$relationshipKey] = array_merge(
                [
                    'links' => [
                        'self' => "{$this->baseUrl}/{$resource['type']}/{$resource['id']}/relationships/{$relationshipKey}",
                        'related' => "{$this->baseUrl}/{$resource['type']}/{$resource['id']}/{$relationshipKey}",
                    ]
                ],
                $resource['relationships'][$relationshipKey]
            );
        }

        return $resource;
    }
}
