<?php namespace Doctrine\Rest\Action;

use Doctrine\Rest\Exceptions\EntityNotFoundException;
use Doctrine\Rest\RestAction;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemAction extends RestAction
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     *
     * @throws EntityNotFoundException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute($this->getAttributeId());
        $entity = $this->findById($id);

        return $this->buildResponseFromResource($entity, $request);
    }
}
