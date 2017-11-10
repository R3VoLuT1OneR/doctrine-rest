<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Rest\Action\CanHydrate;
use Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class CreateAction extends RestActionAbstract
{
    use CanHydrate;

    /**
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    public function handle(RestRequestAbstract $request)
    {
        $request->authorize($this->repository()->getClassName());

        $entity = $this->createEntity($request);

        $this->repository()->em()->persist($entity);
        $this->repository()->em()->flush();

        return $this->response()->create($request, $entity);
    }

    /**
     * @param RestRequestAbstract $request
     *
     * @return object
     */
    protected function createEntity(RestRequestAbstract $request)
    {
        return $this->hydrate(
            $this->repository()->getClassName(),
            $this->repository()->em(),
            $request
        );
    }
}
