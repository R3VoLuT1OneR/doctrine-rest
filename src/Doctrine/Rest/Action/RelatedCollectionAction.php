<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestRepository;

/**
* Action for providing collection (list or array) of data with API.
*/
class RelatedCollectionAction extends CollectionAction
{
	/**
	* Field that contains the related repository
	*
	* @var RestRepository
	*/
	protected $relatedrepository;

	/**
	* Field that contains the related resourse key
	*
	* @var string
	*/
	protected $relatedKey;

	public function __construct(RestRepository $repository, RestRepository $relatedRepository, $transformer, $relatedKey)
	{
		parent::__construct($repository, $transformer);
		$this->relatedRepository = $relatedRepository;
		$this->relatedKey = $relatedKey;
	}

	/**
	* @param RestRequestContract $request
	*
	* @return RestResponse
	*/
	protected function handle($request)
	{
		$resourceKey = $this->relatedRepository()->getResourceKey();
		$this->authorize($request, $this->repository()->getClassName());

		$entity = $this->repository()->findByIdentifier($request);
		$qb = $this->relatedRepository()->sourceQueryBuilder();
		$qb->andWhere($this->relatedRepository()->alias() . '.' . $this->relatedKey . ' = ' . ((int)$request->getId()));
		$this->applyPagination($request, $qb);
		$this->applyFilter($request, $qb);

		$paginator = new Paginator($qb, false);
		$collection = new Collection($paginator, $this->transformer(), $resourceKey);

		if ($qb->getMaxResults()) {
			$collection->setPaginator(
				new DoctrinePaginatorAdapter(
					$paginator,
					$this->paginatorUrlGenerator($request, $resourceKey)
				)
			);
		}

		return $this->response()->resource($request, $collection);
	}

	/**
	* @return RestRepository
	*/
	public function relatedRepository()
	{
	return $this->relatedRepository;
	}
}
