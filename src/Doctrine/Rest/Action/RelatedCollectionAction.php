<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;
use Symfony\Component\HttpFoundation\Response;

/**
* Action for providing collection (list or array) of data with API.
*/
class RelatedCollectionAction extends CollectionAction
{
    /**
     * Repository of basic class.
     *
     * @var RestRepository
     */
    protected $base;

	/**
	* Field that contains the related resourse key
	*
	* @var string
	*/
	protected $relatedField;

    /**
     * RelatedCollectionAction constructor.
     *
     * @param RestRepository      $base
     * @param string              $relatedField Relation property on related entity
     * @param RestRepository      $repository
     * @param TransformerAbstract $transformer
     */
	public function __construct(RestRepository $base, $relatedField, RestRepository $repository, $transformer)
	{
		parent::__construct($repository, $transformer);
		$this->relatedField = $relatedField;
        $this->base = $base;
	}

    /**
     * @return RestRepository
     */
    protected function base()
    {
        return $this->base;
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
        $entity = $this->base()->findByIdentifier($request);

        $relateCriteria = Criteria::create();
        $relateCriteria->andWhere($relateCriteria->expr()->eq($this->relatedField, $entity->getId()));

        $qb->innerJoin($qb->getRootAliases()[0].'.'.$this->relatedField, $this->relatedField);
        $qb->addCriteria($relateCriteria);

        parent::applyFilter($request, $qb);
    }
}
