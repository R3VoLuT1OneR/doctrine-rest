<?php namespace Doctrine\Rest\Action;

use Doctrine\Rest\Resource\Item;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\Traits\CanHydrate;
use Doctrine\Rest\RestAction;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Traits\CanValidate;

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
     * @throws \Doctrine\Rest\Exceptions\RestException
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
            $this->callBeforeUpdate($entity, $changeSet);
        }

        $this->repository()
            ->getEntityManager()
            ->flush();

        if (isset($changeSet)) {
            $this->callAfterUpdate($entity, $changeSet);
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
     */
    protected function callBeforeUpdate($entity, array $changeSet)
    {
        foreach ($this->beforeUpdate as $beforeUpdate) {
            $beforeUpdate($entity, $changeSet);
        }
    }

    /**
     * @param object $entity
     * @param array $changeSet
     */
    protected function callAfterUpdate($entity, array $changeSet)
    {
        foreach ($this->afterUpdate as $afterUpdate) {
            $afterUpdate($entity, $changeSet);
        }
    }
}
