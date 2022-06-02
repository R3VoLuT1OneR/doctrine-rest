<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanValidate;

class UpdateAction extends RestAction
{
    use CanHydrate;
    use CanValidate;

    /** @var array */
    protected $beforeUpdate = [];

    /** @var array */
    protected $afterUpdate = [];

    /**
     * @param RestRequestContract $request
     * @return RestResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Pz\Doctrine\Rest\Exceptions\RestException
     */
    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);
        $this->hydrateEntity($entity, $request->getData());
        $this->validateEntity($entity);

        if ($this->hasEvents()) {
            $this->repository()
                ->getEntityManager()
                ->getUnitOfWork()
                ->computeChangeSets();

            $changeSet = $this->repository()
                ->getEntityManager()
                ->getUnitOfWork()
                ->getEntityChangeSet($entity);
        }

        if (isset($changeSet)) {
            $this->callBeforeUpdate($entity, $changeSet, $request);
        }

        $this->repository()
            ->getEntityManager()
            ->flush();

        if (isset($changeSet)) {
            $this->callAfterUpdate($entity, $changeSet, $request);
        }

        return RestResponseFactory::resource($request,
            new Item($entity, $this->transformer())
        );
    }

    /**
     * @param callable $cb
     * @return $this
     */
    public function beforeUpdate(callable $cb)
    {
        $this->beforeUpdate[] = $cb;
        return $this;
    }

    /**
     * @param callable $cb
     * @return $this
     */
    public function afterUpdate(callable $cb)
    {
        $this->afterUpdate[] = $cb;
        return $this;
    }

    /**
     * @return bool
     */
    protected function hasEvents()
    {
        return !empty($this->beforeUpdate) || !empty($this->afterUpdate);
    }

    /**
     * @param object $entity
     * @param array $changeSet
     * @param RestRequestContract $request
     */
    protected function callBeforeUpdate($entity, array $changeSet, RestRequestContract $request)
    {
        foreach ($this->beforeUpdate as $beforeUpdate) {
            $beforeUpdate($entity, $changeSet, $request);
        }
    }

    /**
     * @param object $entity
     * @param array $changeSet
     * @param RestRequestContract $request
     */
    protected function callAfterUpdate($entity, array $changeSet, RestRequestContract $request)
    {
        foreach ($this->afterUpdate as $afterUpdate) {
            $afterUpdate($entity, $changeSet, $request);
        }
    }
}
