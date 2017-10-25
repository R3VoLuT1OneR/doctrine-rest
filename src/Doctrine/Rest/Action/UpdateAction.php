<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestResponse;

trait UpdateAction
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
     * @param UpdateRequestInterface $request
     * @param                        $entity
     *
     * @return object
     */
    abstract protected function updateEntity($request, $entity);

    /**
     * @param UpdateRequestInterface $request
     *
     * @return RestResponse
     */
    public function update(UpdateRequestInterface $request)
    {
        try {
            if (null === ($entity = $this->repository()->find($request->getId()))) {
                return $this->response()->notFound($request);
            }

            $request->authorize($entity);

            $this->updateEntity($request, $entity);

            $this->repository()->em()->flush();

            return $this->response()->update($request, $entity);
        } catch (\Exception $e) {
            return $this->response()->exception($e);
        }
    }
}
