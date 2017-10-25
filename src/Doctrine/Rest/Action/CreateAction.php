<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseInterface;

trait CreateAction
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
     * @param CreateRequestInterface $request
     *
     * @return object
     */
    abstract protected function createEntity($request);

    /**
     * @param CreateRequestInterface $request
     *
     * @return array
     */
    public function create(CreateRequestInterface $request)
    {
        $request->authorize($this->repository()->getClassName());

        $entity = $this->createEntity($request);

        $this->repository()->em()->persist($entity);
        $this->repository()->em()->flush();

        return $this->response()->create($request, $entity);
    }
}
