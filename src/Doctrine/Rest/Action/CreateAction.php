<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\CanValidate;

class CreateAction extends RestAction
{
    use CanHydrate;
    use CanValidate;

    /**
     * @param RestRequestContract $request
     * @return RestResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Pz\Doctrine\Rest\Exceptions\RestException
     */
    protected function handle($request)
    {
        $headers = [];
        $class = $this->repository()->getClassName();

        $this->authorize($request, $class);
        $entity = $this->hydrateEntity($class, $request->getData());
        $this->validateEntity($entity);
        $this->repository()->getEntityManager()->persist($entity);
        $this->repository()->getEntityManager()->flush();

        if ($entity instanceof JsonApiResource) {
            $headers['Location'] = $this->repository()->linkJsonApiResource($request, $entity);
        }

        $resource = new Item($entity, $this->transformer());
        return RestResponseFactory::resource($request, $resource, RestResponse::HTTP_CREATED, $headers);
    }
}
