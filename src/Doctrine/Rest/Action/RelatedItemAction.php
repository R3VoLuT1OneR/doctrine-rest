<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Resource\Item;
use Pz\Doctrine\Rest\Resource\NullResource;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestRepository;

class RelatedItemAction extends ItemAction
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
	* @param RestRequestContract $request
	*
	* @return RestResponse
	*/
	public function handle($request)
	{
		$entity = $this->repository()->findByIdentifier($request);

        $this->authorize($request, $entity);

		$method = 'get' . ucfirst($this->relatedField);

		if ($relatedEntity = $entity->{$method}()) {
            $resource = new Item($relatedEntity, $this->transformer());
            return $this->response()->resource($request, $resource);
        }

        return $this->response()->resource($request, new NullResource());
	}
}
