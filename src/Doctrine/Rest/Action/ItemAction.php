<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class ItemAction extends RestAction
{
    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function handle(RestRequest $request)
    {
        $entity = $this->repository()->findByIdentifier($request);

        $this->authorize($request, $entity);

        $resource = new Item($entity, $this->transformer(), $this->getResourceKey($entity));

        return $this->response()->resource($request, $resource);
    }
}
