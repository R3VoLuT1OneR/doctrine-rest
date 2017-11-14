<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;

class CreateAction extends RestAction
{
    use CanHydrate;

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    protected function handle(RestRequestContract $request)
    {
        $headers = [];
        $this->authorize($request, $this->repository()->getClassName());

        $entity = $this->createEntity($request);

        $this->repository()->em()->persist($entity);
        $this->repository()->em()->flush();

        $resource = new Item($entity, $this->transformer(), $this->repository()->getResourceKey());

        if ($entity instanceof JsonApiResource) {
            $headers['Location'] = $this->repository()->linkJsonApiResource($request, $entity);
        }

        return $this->response()->resource($request, $resource, RestResponse::HTTP_CREATED, $headers);
    }

    /**
     * @param RestRequestContract $request
     *
     * @return object
     */
    protected function createEntity(RestRequestContract $request)
    {
        return $this->hydrate(
            $this->repository()->getClassName(),
            $this->repository()->em(),
            $request
        );
    }
}
