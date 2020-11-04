<?php namespace Doctrine\Rest\Action\Relationships;

use Doctrine\Rest\Action\Related\RelatedItemAction;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\RestResponse;

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
