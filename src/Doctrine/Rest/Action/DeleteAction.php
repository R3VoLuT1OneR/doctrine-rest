<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class DeleteAction extends RestAction
{
    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function handle(RestRequest $request)
    {
        $entity = $this->repository()->findByIdentifier($request);
        $request->authorize($entity);

        $this->repository()->em()->remove($entity);
        $this->repository()->em()->flush();

        return RestResponse::noContent();
    }
}
