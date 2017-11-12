<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

class UpdateAction extends RestAction
{
    use CanHydrate;

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    public function handle(RestRequest $request)
    {
        $entity = $this->repository()->findByIdentifier($request);
        $this->authorize($request, $entity);

        $this->updateEntity($request, $entity);
        $this->repository()->em()->flush();

        $resource = new Item($entity, $this->transformer(), $this->getResourceKey($entity));

        return $this->response()->resource($request, $resource);
    }

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return object
     */
    protected function updateEntity($request, $entity)
    {
        return $this->hydrate($entity, $this->repository()->em(), $request);
    }
}
