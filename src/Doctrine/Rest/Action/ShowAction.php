<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseInterface;

trait ShowAction
{
    /**
     * Doctrine repository from where get data.
     *
     * @return RestRepository
     */
    abstract protected function repository();

    /**
     * @return RestResponseInterface
     */
    abstract protected function response();

    /**
     * @param ShowRequestInterface $request
     *
     * @return array
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function show(ShowRequestInterface $request)
    {
        $entity = $this->repository()->findById($request->getId());

        $request->authorize($entity);

        return $this->response()->show($request, $entity);
    }
}
