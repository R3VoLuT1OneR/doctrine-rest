<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class ShowAction extends RestActionAbstract
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
        return $this->response()->show($request, $entity);
    }
}
