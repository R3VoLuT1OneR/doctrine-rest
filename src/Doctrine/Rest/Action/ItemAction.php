<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Resource\Item;

class ItemAction extends RestAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function handle($request)
    {
        $entity = $this->repository()->findByIdentifier($request);

        $this->authorize($request, $entity);

        $resource = new Item($entity, $this->transformer(), $entity->getResourceKey());

        return $this->response()->resource($request, $resource);
    }
}
