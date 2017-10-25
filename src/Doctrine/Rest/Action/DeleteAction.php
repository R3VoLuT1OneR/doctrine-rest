<?php namespace Pz\Doctrine\Rest\Action;

use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestResponse;

trait DeleteAction
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
     * @param DeleteRequestInterface $request
     *
     * @return RestResponse
     */
    public function delete(DeleteRequestInterface $request)
    {
        try {
            if (null === ($entity = $this->repository()->find($request->getId()))) {
                return $this->response()->notFound($request);
            }

            $request->authorize($entity);

            $this->repository()->em()->remove($entity);
            $this->repository()->em()->flush();

            return $this->response()->delete($request, $entity);
        } catch (\Exception $e) {
            return $this->response()->exception($e);
        }
    }
}
