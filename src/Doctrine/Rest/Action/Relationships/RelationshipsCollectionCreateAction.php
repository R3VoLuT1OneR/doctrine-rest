<?php namespace Pz\Doctrine\Rest\Action\Relationships;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\CanHydrate;
use Pz\Doctrine\Rest\Traits\RelatedAction;

class RelationshipsCollectionCreateAction extends RestAction
{
    use RelatedAction;
    use CanHydrate;

    /**
     * RelatedRestAction constructor.
     *
     * @param RestRepository      $repository
     * @param string              $field
     * @param string              $mappedBy
     * @param RestRepository      $related
     * @param TransformerAbstract $transformer
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
            $item = $this->getRelatedEntity($raw);
            $this->addRelationItem($entity, $this->field(), $item);
        }

        $this->repository()->getEntityManager()->flush($entity);

        return (
            new RelatedCollectionAction(
                $this->repository(),
                $this->mappedBy(),
                $this->related(),
                $this->transformer()
            )
        )->dispatch($request);
    }
}
