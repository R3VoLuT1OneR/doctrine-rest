<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;

class CreateAction extends RestAction
{
    use CanHydrate;

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    protected function handle(RestRequest $request)
    {
        $headers = [];
        $this->authorize($request, $this->repository()->getClassName());

        $entity = $this->createEntity($request);

        $this->repository()->em()->persist($entity);
        $this->repository()->em()->flush();

        $resource = new Item($entity, $this->transformer(), $this->getResourceKey($entity));

        if ($entity instanceof JsonApiResource) {
            $headers['Location'] = $this->linkJsonApiResource($request, $entity);
        }

        return $this->response()->resource($request, $resource, RestResponse::HTTP_CREATED, $headers);
    }

    /**
     * @param RestRequest $request
     *
     * @return object
     */
    protected function createEntity(RestRequest $request)
    {
        return $this->hydrate(
            $this->repository()->getClassName(),
            $this->repository()->em(),
            $request
        );
    }
}
