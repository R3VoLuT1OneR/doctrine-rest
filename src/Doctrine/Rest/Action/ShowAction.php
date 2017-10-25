<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ShowAction
{
    /**
     * Doctrine repository from where get data.
     *
     * @return RestRepository
     */
    abstract protected function repository();

    /**
     * @return RestResponseInterface
     */
    abstract protected function response();

    /**
     * @param ShowRequestInterface $request
     *
     * @return array
     */
    public function show(ShowRequestInterface $request)
    {
        if (null === ($entity = $this->repository()->find($request->getId()))) {
            return $this->response()->notFound($request);
        }

        $request->authorize($entity);

        return $this->response()->show($request, $entity);
    }
}
