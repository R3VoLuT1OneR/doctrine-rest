<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestResponseFactory;

class ItemAction extends RestAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     * @throws RestException
     */
    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);

        $resource = new Item($entity, $this->transformer());

        return RestResponseFactory::resource($request, $resource);
    }
}
