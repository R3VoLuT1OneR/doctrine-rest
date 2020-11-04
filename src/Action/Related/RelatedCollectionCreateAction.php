<?php namespace Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\Exceptions\RestException;
use Doctrine\Rest\RestAction;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Traits\CanHydrate;
use Doctrine\Rest\Traits\CanValidate;
use Doctrine\Rest\Traits\RelatedAction;

class RelatedCollectionCreateAction extends RestAction
{
    use RelatedAction;
    use CanHydrate;
    use CanValidate;

    /**
     * RelatedCollectionCreateAction constructor.
     *
     * @param RestRepository                               $repository
     * @param string                                       $field
     * @param                                              $mappedBy
     * @param RestRepository                               $related
     * @param \Closure|TransformerAbstract                 $transformer
     */
    public function __construct(RestRepository $repository, $field, $mappedBy, RestRepository $related, $transformer)
    {
        parent::__construct($repository, $transformer);
        $this->mappedBy = $mappedBy;
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

        foreach ($request->getData() as $raw) {
            $item = $this->hydrateEntity($this->related()->getClassName(), $raw);
            $this->addRelationItem($entity, $this->field(), $item);
            $this->validateEntity($item);
            $this->related()->getEntityManager()->persist($item);
        }

        $this->repository()->getEntityManager()->flush($entity);

        return (new RelatedCollectionAction(
            $this->repository(),
            $this->mappedBy(),
            $this->related(),
            $this->transformer()
        ))->dispatch($request)->setStatusCode(RestResponse::HTTP_CREATED);
    }
}
