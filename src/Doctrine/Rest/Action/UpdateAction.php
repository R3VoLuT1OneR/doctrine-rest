<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait UpdateAction
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
     * @param UpdateRequestInterface $request
     * @param                        $entity
     *
     * @return object
     */
    abstract protected function updateEntity($request, $entity);

    /**
     * @param UpdateRequestInterface $request
     *
     * @return array
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function update(UpdateRequestInterface $request)
    {
        if (null === ($entity = $this->repository()->find($request->getId()))) {
            return $this->response()->notFound($request);
        }

        $request->authorize($entity);

        $this->updateEntity($request, $entity);

        $this->repository()->em()->flush();

        return $this->response()->update($request, $entity);
    }
}
