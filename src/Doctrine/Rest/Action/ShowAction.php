<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestResponse;

trait ShowAction
{
    /**
     * Doctrine repository from where get data.
     *
     * @return RestRepository
     */
    abstract protected function repository();

    /**
     * @return RestResponseFactory
     */
    abstract protected function response();

    /**
     * @param ShowRequestInterface $request
     *
     * @return RestResponse
     */
    public function show(ShowRequestInterface $request)
    {
        try {

            if (null === ($entity = $this->repository()->find($request->getId()))) {
                return $this->response()->notFound($request);
            }

            $request->authorize($entity);

            return $this->response()->show($request, $entity);
        } catch (\Exception $e) {
            return $this->response()->exception($e);
        }
    }
}
