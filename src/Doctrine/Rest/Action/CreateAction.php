<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\CanValidate;

class CreateAction extends RestAction
{
    use CanHydrate;
    use CanValidate;

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    protected function handle($request)
    {
        $headers = [];
        $this->authorize($request, $this->repository()->getClassName());

        $entity = $this->hydrateRelationData($this->repository()->getClassName(), $request->getData());

        $this->validateEntity($entity);
        $this->repository()->getEntityManager()->persist($entity);
        $this->repository()->getEntityManager()->flush();

        $resource = new Item($entity, $this->transformer(), $this->repository()->getResourceKey());

        if ($entity instanceof JsonApiResource) {
            $headers['Location'] = $this->repository()->linkJsonApiResource($request, $entity);
        }

        return $this->response()->resource($request, $resource, RestResponse::HTTP_CREATED, $headers);
    }
}
