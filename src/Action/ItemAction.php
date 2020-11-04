<?php namespace Doctrine\Rest\Action;

use Doctrine\Rest\RestAction;
//use Doctrine\Rest\Contracts\RestRequestContract;
//use Doctrine\Rest\RestResponse;
//use Doctrine\Rest\Resource\Item;
//use Doctrine\Rest\Exceptions\RestException;
//use Doctrine\Rest\RestResponseFactory;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemAction extends RestAction
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);

        $resource = new Item($entity, $this->transformer());

        return RestResponseFactory::resource($request, $resource);
    }
}
