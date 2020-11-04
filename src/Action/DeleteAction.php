<?php namespace Doctrine\Rest\Action;

use Doctrine\Rest\RestAction;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\RestResponse;

class DeleteAction extends RestAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);

        $this->repository()->getEntityManager()->remove($entity);
        $this->repository()->getEntityManager()->flush();

        return RestResponse::noContent();
    }
}
