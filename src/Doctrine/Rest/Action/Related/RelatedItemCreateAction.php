<?php namespace Pz\Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\RelatedAction;

class RelatedItemCreateAction extends RestAction
{
    use RelatedAction;
    use CanHydrate;

    /**
     * RelatedRestAction constructor.
     *
     * @param RestRepository      $repository
     * @param string              $field
     * @param RestRepository      $related
     * @param TransformerAbstract $transformer
     */
    public function __construct(RestRepository $repository, $field, RestRepository $related, $transformer)
    {
        parent::__construct($repository, $transformer);
        $this->related = $related;
        $this->field = $field;
    }

    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     * @throws RestException
     */
    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());
        $data = $request->getData();
        $this->authorize($request, $entity);

        if (isset($data['id']) && isset($data['type'])) {
            $item = $this->getRelatedEntity($data);
        } else {
            $scope = $entity->getResourceKey().'.'.$this->field();
            /** @var JsonApiResource $item */
            $item = $this->hydrateEntity($this->related()->getClassName(), $data, $scope);
        }

        $this->setProperty($entity, $this->field(), $item);
        $this->repository()->getEntityManager()->flush($entity);

        return $this->response()->resource($request, new Item($item, $this->transformer()));
    }
}
