<?php namespace Pz\Doctrine\Rest;

use Pz\Doctrine\Rest\Action\Index\ResponseDataInterface;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;

/**
 * Rest Response Interface
 *
 * Used as api for all rest responses.
 *
 * @package Pz\Doctrine\Rest
 */
interface RestResponseInterface
{
    /**
     * @param IndexRequestInterface $request
     * @param ResponseDataInterface $response
     *
     * @return array
     */
    public function index(IndexRequestInterface $request, ResponseDataInterface $response);

    /**
     * @param ShowRequestInterface $request
     * @param object               $entity
     *
     * @return array
     */
    public function show(ShowRequestInterface $request, $entity);

    /**
     * @param CreateRequestInterface $request
     * @param object                 $entity
     *
     * @return array
     */
    public function create(CreateRequestInterface $request, $entity);

    /**
     * @param UpdateRequestInterface $request
     * @param object                 $entity
     *
     * @return array
     */
    public function update(UpdateRequestInterface $request, $entity);

    /**
     * @param DeleteRequestInterface $request
     * @param object                 $entity
     *
     * @return array
     */
    public function delete(DeleteRequestInterface $request, $entity);

    /**
     * @param RestRequestInterface $request
     *
     * @return array
     */
    public function notFound(RestRequestInterface $request);

    /**
     * @param string    $message
     * @param array     $errors
     *
     * @return array
     */
    public function error($message, $errors);
}
