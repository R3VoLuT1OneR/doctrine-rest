<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;

class DeleteAction extends RestAction
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

        $this->repository()->em()->remove($entity);
        $this->repository()->em()->flush();

        return RestResponse::noContent();
    }
}
