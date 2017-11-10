<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\QueryBuilder;
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
     * @param RestRequestAbstract $request
     * @param QueryBuilder         $qb
     *
     * @return Response
     * @throws \Exception
     */
    public function index(RestRequestAbstract $request, QueryBuilder $qb);

    /**
     * @param RestRequestAbstract $request
     * @param object              $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function show(RestRequestAbstract$request, $entity);

    /**
     * @param RestRequestAbstract   $request
     * @param object                $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function create(RestRequestAbstract $request, $entity);

    /**
     * @param RestRequestAbstract    $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function update(RestRequestAbstract $request, $entity);

    /**
     * @param RestRequestAbstract    $request
     * @param object                 $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function delete(RestRequestAbstract $request, $entity);

    /**
     * @param RestRequestAbstract $request
     *
     * @return Response
     */
    public function notFound(RestRequestAbstract$request);

    /**
     * @param \Exception|\Error|RestException $exception
     *
     * @return Response
     */
    public function exception($exception);
}
