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
     * @param RestRequest  $request
     * @param QueryBuilder $qb
     *
     * @return Response
     * @throws \Exception
     */
    public function index(RestRequest $request, QueryBuilder $qb);

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function show(RestRequest$request, $entity);

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function create(RestRequest $request, $entity);

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function update(RestRequest $request, $entity);

    /**
     * @param RestRequest $request
     * @param object      $entity
     *
     * @return Response
     * @throws \Exception
     */
    public function delete(RestRequest $request, $entity);

    /**
     * @param RestRequest $request
     *
     * @return Response
     */
    public function notFound(RestRequest$request);

    /**
     * @param \Exception|\Error|RestException $exception
     *
     * @return Response
     */
    public function exception($exception);
}
