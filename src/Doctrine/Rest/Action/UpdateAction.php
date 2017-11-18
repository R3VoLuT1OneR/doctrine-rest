<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanValidate;

class UpdateAction extends RestAction
{
    use CanHydrate;
    use CanValidate;

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function handle($request)
    {
        $entity = $this->repository()->findByIdentifier($request);
        $this->authorize($request, $entity);

        $entity = $this->hydrateRelationData($entity, $request->getData());

        $this->validateEntity($entity);
        $this->repository()->getEntityManager()->flush();

        $resource = new Item($entity, $this->transformer(), $this->repository()->getResourceKey());

        return $this->response()->resource($request, $resource);
    }
}
