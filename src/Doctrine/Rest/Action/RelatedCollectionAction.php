<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestRepository;

/**
* Action for providing collection (list or array) of data with API.
*/
class RelatedCollectionAction extends CollectionAction
{
	/**
	* Field that contains the related resourse key
	*
	* @var string
	*/
	protected $relatedField;

    /**
     * RelatedCollectionAction constructor.
     *
     * @param string              $relatedField Relation property on related entity
     * @param RestRepository      $repository
     * @param TransformerAbstract $transformer
     */
	public function __construct($relatedField, RestRepository $repository, $transformer)
	{
		parent::__construct($repository, $transformer);
		$this->relatedField = $relatedField;
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
    protected function applyFilter(RestRequestContract $request, QueryBuilder $qb)
    {
        $entity = $this->repository()->findByIdentifier($request);

        $relateCriteria = Criteria::create();
        $relateCriteria->andWhere($relateCriteria->expr()->eq($this->relatedField, $entity));

        return parent::applyFilter($request, $qb->addCriteria($relateCriteria));
    }
}
