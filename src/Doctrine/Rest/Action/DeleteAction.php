<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseInterface;

trait DeleteAction
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
     * @param DeleteRequestInterface $request
     *
     * @return array
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function delete(DeleteRequestInterface $request)
    {
        $entity = $this->repository()->findById($request->getId());

        $request->authorize($entity);

        $this->repository()->em()->remove($entity);
        $this->repository()->em()->flush();

        return $this->response()->delete($request, $entity);
    }
}
