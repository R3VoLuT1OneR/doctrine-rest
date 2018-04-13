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
    public function handle($request)
    {
        $entity = $this->repository()->findByIdentifier($request);

        $this->authorize($request, $entity);

        $this->repository()->getEntityManager()->remove($entity);
        $this->repository()->getEntityManager()->flush();

        return RestResponse::noContent();
    }
}
