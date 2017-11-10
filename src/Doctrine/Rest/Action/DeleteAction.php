<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class DeleteAction extends RestActionAbstract
{
    /**
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    public function handle(RestRequestAbstract $request)
    {
        $entity = $this->repository()->findByIdentifier($request);
        $request->authorize($entity);

        $this->repository()->em()->remove($entity);
        $this->repository()->em()->flush();

        return $this->response()->delete($request, $entity);
    }
}
