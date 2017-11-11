<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class ShowAction extends RestActionAbstract
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
        return $this->response()->show($request, $entity);
    }
}
