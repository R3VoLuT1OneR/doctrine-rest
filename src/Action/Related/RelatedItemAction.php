<?php namespace Doctrine\Rest\Action\Related;

use Doctrine\Rest\Action\ItemAction as BaseItemAction;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\Resource\Item;
use Doctrine\Rest\Resource\NullResource;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\Traits\RelatedAction;

use League\Fractal\TransformerAbstract;

class RelatedItemAction extends BaseItemAction
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

    /**
	* @param RestRequestContract $request
	*
	* @return RestResponse
	*/
	public function handle($request)
	{
		$entity = $this->repository()->findById($request->getId());

        $this->authorize($request, $entity);

        if ($relatedEntity = $this->getProperty($entity, $this->field())) {
            return RestResponseFactory::resource($request,
                new Item($relatedEntity, $this->transformer())
            );
        }

        return RestResponseFactory::resource($request, new NullResource());
	}
}
