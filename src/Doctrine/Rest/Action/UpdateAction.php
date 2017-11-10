<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CanHydrate;
use Pz\Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class UpdateAction extends RestActionAbstract
{
    use CanHydrate;

    /**
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    public function handle(RestRequestAbstract $request)
    {
        $entity = $this->repository()->findByIdentifier($request);
        $request->authorize($entity);

        $this->updateEntity($request, $entity);
        $this->repository()->em()->flush();

        return $this->response()->update($request, $entity);
    }

    /**
     * @param RestRequestAbstract $request
     * @param object              $entity
     *
     * @return object
     */
    protected function updateEntity($request, $entity)
    {
        return $this->hydrate($entity, $this->repository()->em(), $request);
    }
}
