<?php namespace Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\Contracts\RestRequestContract;
use Doctrine\Rest\Exceptions\RestException;
use Doctrine\Rest\RestAction;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\Traits\RelatedAction;

class RelatedItemDeleteAction extends RestAction
{
    use RelatedAction;

    /**
     * RelatedCollectionCreateAction constructor.
     *
     * @param RestRepository                               $repository
     * @param string                                       $field
     * @param RestRepository                               $related
     * @param \Closure|TransformerAbstract                 $transformer
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
     * @throws RestException
     */
    public function handle($request)
    {
        $entity = $this->repository()->findById($request->getId());
        $this->authorize($request, $entity);

        $item = $this->getProperty($entity, $this->field());
        $this->related()->getEntityManager()->remove($item);
        $this->setProperty($entity, $this->field(), null);

        $this->repository()->getEntityManager()->flush();

        return RestResponseFactory::noContent();
    }
}
