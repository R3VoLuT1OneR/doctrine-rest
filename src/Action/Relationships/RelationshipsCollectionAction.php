<?php namespace Doctrine\Rest\Action\Relationships;

use Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\RestResponse;

class RelationshipsCollectionAction extends RelatedCollectionAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    protected function handle($request)
    {
        $request->isRelationships(true);
        return parent::handle($request);
    }
}
