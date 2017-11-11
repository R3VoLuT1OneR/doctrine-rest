<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class ItemAction extends RestActionAbstract
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
        return $this->response()->item($request, $entity);
    }
}
