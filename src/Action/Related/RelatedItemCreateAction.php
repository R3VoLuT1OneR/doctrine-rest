<?php namespace Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\Exceptions\RestException;
use Doctrine\Rest\Resource\Item;
use Doctrine\Rest\RestAction;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\Traits\CanHydrate;
use Doctrine\Rest\Traits\CanValidate;
use Doctrine\Rest\Traits\RelatedAction;

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

        return RestResponseFactory::resource($request,
            new Item($item, $this->transformer(), $this->related()->getResourceKey()),
            RestResponse::HTTP_CREATED
        );
    }
}
