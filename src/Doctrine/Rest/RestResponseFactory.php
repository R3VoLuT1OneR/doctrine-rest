<?php namespace Pz\Doctrine\Rest;

use Pz\Doctrine\Rest\Action\Index\ResponseDataInterface;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestResponse as Response;

/**
 * Rest Response Interface
 *
 * Used as api for all rest responses.
 *
 * @package Pz\Doctrine\Rest
 */
interface RestResponseFactory
{
    /**
     * @param IndexRequestInterface $request
     * @param ResponseDataInterface $response
     *
     * @return Response
     * @throws \Exception
     */
    public function index(IndexRequestInterface $request, ResponseDataInterface $response);

    /**
     * @param ShowRequestInterface $request
     * @param object               $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function show(ShowRequestInterface $request, $entity);

    /**
     * @param CreateRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function create(CreateRequestInterface $request, $entity);

    /**
     * @param UpdateRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function update(UpdateRequestInterface $request, $entity);

    /**
     * @param DeleteRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function delete(DeleteRequestInterface $request, $entity);

    /**
     * @param RestRequestInterface $request
     *
     * @return Response
     */
    public function notFound(RestRequestInterface $request);

    /**
     * @param \Exception|\Error|RestException $exception
     *
     * @return Response
     */
    public function exception($exception);
}
