<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;

class CreateAction extends RestActionAbstract
{
    use CanHydrate;

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function handle(RestRequest $request)
    {
        $request->authorize($this->repository()->getClassName());

        $entity = $this->createEntity($request);

        $this->repository()->em()->persist($entity);
        $this->repository()->em()->flush();

        return $this->response()->created($request, $entity);
    }

    /**
     * @param RestRequest $request
     *
     * @return object
     */
    protected function createEntity(RestRequest $request)
    {
        return $this->hydrate(
            $this->repository()->getClassName(),
            $this->repository()->em(),
            $request
        );
    }
}
