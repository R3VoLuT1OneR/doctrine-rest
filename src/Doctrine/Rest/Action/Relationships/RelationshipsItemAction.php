<?php namespace Pz\Doctrine\Rest\Action\Relationships;

use Pz\Doctrine\Rest\Action\Related\RelatedItemAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;

class RelationshipsItemAction extends RelatedItemAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    public function handle($request)
    {
        $request->isRelationships(true);
        return parent::handle($request);
    }
}
