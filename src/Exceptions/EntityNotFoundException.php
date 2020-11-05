<?php namespace Doctrine\Rest\Exceptions;

use Fig\Http\Message\StatusCodeInterface;
use Exception;

/**
 * Thrown when resource not found.
 */
class EntityNotFoundException extends RestException
{
    public function __construct(string $resourceType, $id, Exception $previous = null)
    {
        parent::__construct(StatusCodeInterface::STATUS_NOT_FOUND, 'Entity not found', $previous);

        $this->error('entity-not-found', ['type' => $resourceType, 'id' => $id], sprintf(
            'Resource "%s" for id "%s" was not found', $resourceType, $id
        ));
    }
}