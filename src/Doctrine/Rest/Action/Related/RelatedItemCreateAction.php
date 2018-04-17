<?php namespace Pz\Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\CanValidate;
use Pz\Doctrine\Rest\Traits\RelatedAction;

class RelatedItemCreateAction extends RestAction
{
    use RelatedAction;
    use CanHydrate;
    use CanValidate;

    /**
     * RelatedCollectionCreateAction constructor.
     *
     * @param RestRepository                               $repository
     * @param string                                       $field
     * @param RestRepository                               $related
     * @param \Closure|TransformerAbstract                 $transformer
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
        $this->authorize($request, $entity);

        $item = $this->hydrateEntity($this->related()->getClassName(), $request->getData());
        $this->validateEntity($item);
        $this->related()->getEntityManager()->persist($item);
        $this->setProperty($entity, $this->field(), $item);

        $this->related()->getEntityManager()->flush($entity);

        return $this->response()->resource($request,
            new Item($item, $this->transformer(), $this->related()->getResourceKey()),
            RestResponse::HTTP_CREATED
        );
    }
}
