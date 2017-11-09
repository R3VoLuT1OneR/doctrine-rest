<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\QueryBuilder;
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
     * @param QueryBuilder          $qb
     *
     * @return Response
     * @throws \Exception
     */
    public function index(RestRequestInterface $request, QueryBuilder $qb);

    /**
     * @param ShowRequestInterface $request
     * @param object               $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function show(RestRequestInterface$request, $entity);

    /**
     * @param CreateRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function create(RestRequestInterface $request, $entity);

    /**
     * @param UpdateRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function update(RestRequestInterface $request, $entity);

    /**
     * @param DeleteRequestInterface $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function delete(RestRequestInterface $request, $entity);

    /**
     * @param RestRequestInterface $request
     *
     * @return Response
     */
    public function notFound(RestRequestInterface$request);

    /**
     * @param \Exception|\Error|RestException $exception
     *
     * @return Response
     */
    public function exception($exception);
}
