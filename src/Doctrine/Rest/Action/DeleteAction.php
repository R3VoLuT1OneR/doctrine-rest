<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class DeleteAction extends RestActionAbstract
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

        return $this->response()->deleted($request, $entity);
    }
}
