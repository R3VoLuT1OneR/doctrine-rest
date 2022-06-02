<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\CanValidate;

class CreateAction extends RestAction
{
    use CanHydrate;
    use CanValidate;

    /** @var array|callable[] */
    protected $beforeCreate = [];

    /** @var array|callable[] */
    protected $afterCreated = [];

    /**
     * @param RestRequestContract $request
     * @return RestResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Pz\Doctrine\Rest\Exceptions\RestException
     */
    public function handle($request)
    {
        $headers = [];
        $class = $this->repository()->getClassName();

        $this->authorize($request, $class);
        $entity = $this->hydrateEntity($class, $request->getData());
        $this->validateEntity($entity);

        $this->callBeforeCreate($entity, $request);

        $this->repository()->getEntityManager()->persist($entity);
        $this->repository()->getEntityManager()->flush();

        $this->callAfterCreated($entity, $request);

        if ($entity instanceof JsonApiResource) {
            $headers['Location'] = $this->repository()->linkJsonApiResource($request, $entity);
        }

        $resource = new Item($entity, $this->transformer());
        return RestResponseFactory::resource($request, $resource, RestResponse::HTTP_CREATED, $headers);
    }

    public function beforeCreate(callable $cb): self
    {
        $this->beforeCreate[] = $cb;
        return $this;
    }

    public function afterCreated(callable $cb): self
    {
        $this->afterCreated[] = $cb;
        return $this;
    }

    public function callBeforeCreate($entity, RestRequestContract $request): self
    {
        foreach ($this->beforeCreate as $cb) {
            $cb($entity, $request);
        }

        return $this;
    }

    public function callAfterCreated($entity, RestRequestContract $request): self
    {
        foreach ($this->afterCreated as $cb) {
            $cb($entity, $request);
        }

        return $this;
    }
}
