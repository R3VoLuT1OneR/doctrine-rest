<?php namespace Pz\Doctrine\Rest\Action\Related;

use Pz\Doctrine\Rest\Action\CollectionAction as BaseCollectionAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Traits\RelatedAction;

/**
* Action for providing collection (list or array) of data with API.
*/
class RelatedCollectionAction extends BaseCollectionAction
{
    use RelatedAction;

    /**
     * RelatedRestAction constructor.
     *
     * @param RestRepository      $repository
     * @param string              $mappedBy
     * @param RestRepository      $related
     * @param TransformerAbstract $transformer
     */
    public function __construct(RestRepository $repository, $mappedBy, RestRepository $related, $transformer)
    {
        parent::__construct($repository, $transformer);
        $this->mappedBy = $mappedBy;
        $this->related = $related;
    }

    /**
     * Related repository used as default repository for collection queries.
     *
     * @return RestRepository
     */
    public function repository()
    {
        return $this->related;
    }

    /**
     * Base entity repository.
     *
     * @return RestRepository
     */
    public function base()
    {
        return $this->repository;
    }

    /**
     * Add filter by relation entity.
     *
     * @param RestRequestContract $request
     * @param QueryBuilder        $qb
     *
     * @return $this
     * @throws \Pz\Doctrine\Rest\Exceptions\RestException
     */
    protected function applyFilter(RestRequestContract $request, QueryBuilder $qb): self
    {
        $entity = $this->base()->findById($request->getId());

        $relateCriteria = Criteria::create();
        $relateCriteria->andWhere($relateCriteria->expr()->eq($this->mappedBy(), $entity->getId()));

        $qb->innerJoin($qb->getRootAliases()[0].'.'.$this->mappedBy(), $this->mappedBy());
        $qb->addCriteria($relateCriteria);

        return parent::applyFilter($request, $qb);
    }
}
