<?php namespace Pz\Doctrine\Rest\Action;

use League\Fractal\Resource\Item;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestRepository;

class RelatedItemAction extends ItemAction
{
	/**
	* Field that contains the related resourse key
	*
	* @var string
	*/
	protected $relatedKey;

	public function __construct(RestRepository $repository, $transformer, $relatedKey)
	{
		parent::__construct($repository, $transformer);
		$this->relatedKey = $relatedKey;
	}

	/**
	* @param RestRequestContract $request
	*
	* @return RestResponse
	*/
	public function handle($request)
	{
		$entity = $this->repository()->findByIdentifier($request);
		$method = 'get' . ucfirst($this->relatedKey);
		$relatedEntity = $entity->{$method}();

		$this->authorize($request, $relatedEntity);

		$resource = new Item($relatedEntity, $this->transformer(), $this->relatedKey);

		return $this->response()->resource($request, $resource);
	}
}
