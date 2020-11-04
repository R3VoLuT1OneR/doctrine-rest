<?php namespace Doctrine\Rest\Action\Relationships;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\Resource\Item;
use Doctrine\Rest\RestAction;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\Traits\RelatedAction;

class RelationshipsItemUpdateAction extends RestAction
{
     use RelatedAction;

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

    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);

        $item = $this->getRelatedEntity($request->getData());

        $this->setProperty($entity, $this->field(), $item);

        $this->repository()->getEntityManager()->flush($entity);

        return (
            new RelationshipsItemAction(
                $this->repository(),
                $this->field(),
                $this->related(),
                $this->transformer()
            )
        )->dispatch($request);
    }
}
