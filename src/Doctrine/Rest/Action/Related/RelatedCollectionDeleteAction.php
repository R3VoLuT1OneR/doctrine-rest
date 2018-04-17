<?php namespace Pz\Doctrine\Rest\Action\Related;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Traits\RelatedAction;

class RelatedCollectionDeleteAction extends RestAction
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

        foreach ($request->getData() as $raw) {
            $item = $this->getRelatedEntity($raw);
            $this->removeRelationItem($entity, $this->field(), $item);
            $this->related()->getEntityManager()->remove($item);
        }

        $this->repository()->getEntityManager()->flush();

        return $this->response()->noContent();
    }
}
