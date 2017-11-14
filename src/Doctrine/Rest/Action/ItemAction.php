<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;

class ItemAction extends RestAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function handle(RestRequestContract $request)
    {
        $entity = $this->repository()->findByIdentifier($request);

        $this->authorize($request, $entity);

        $resource = new Item($entity, $this->transformer(), $this->repository()->getResourceKey());

        return $this->response()->resource($request, $resource);
    }
}
