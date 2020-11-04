<?php namespace Doctrine\Rest\Exceptions;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Rest\ResourceInterface;
use Fig\Http\Message\StatusCodeInterface;
use Exception;

class RestException extends Exception
{
    protected array $errors = [];

    public static function createFromException(Exception $exception, $debug = false)
    {
        $extra = $debug ? ['trace' => $exception->getTrace()] : [];

        return static::create(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, 'Internal Server Error', $exception)
            ->error('internal-error', [], $exception->getMessage(), $extra);
    }

    public static function createForbidden($message = 'Forbidden.')
    {
        return static::create(StatusCodeInterface::STATUS_FORBIDDEN, $message);
    }

    public static function createUnprocessable($message = '')
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $message);
    }

    public static function createNotFound($id, $resourceKey, $message = '')
    {
        return static::create(StatusCodeInterface::STATUS_NOT_FOUND, 'Entity not found.')
            ->error('entity-not-found', ['type' => $resourceKey, 'id' => $id], $message);
    }

    public static function createFilterError(array $source, $message)
    {
        return static::create(StatusCodeInterface::STATUS_BAD_REQUEST, 'Wrong filter input.')
            ->error('filter-input', $source, $message);
    }

    public static function missingRootData()
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('missing-root-data', ['pointer' => ''], "Missing `data` member at document top level.");
    }

    public static function missingData($pointer)
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('missing-data', ['pointer' => $pointer], "Missing `data` member at pointer level.");
    }

    public static function missingDataMembers($pointer)
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('missing-data-members', ['pointer' => $pointer],
                'Missing or not array `data.attributes` or `data.relationships` at pointer level.'
            );
    }

    public static function unknownAttribute($pointer)
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('unknown-attribute', ['pointer' => $pointer], 'Unknown attribute.');
    }

    public static function unknownRelation($pointer)
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('unknown-relation', ['pointer' => $pointer], 'Unknown relation.');
    }

    public static function invalidInclude()
    {
        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
            ->error('invalid-include', ['pointer' => 'include'], 'Invalid `include` param.');
    }

    public static function notJsonApiResource($entity)
    {
        $source = [];
        $message = 'Got not JsonApiResource entity.';

        if (is_object($entity)) {
            $source['class'] = get_class($entity);
        }

        return static::create(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $message)
            ->error('invalid-entity', $source, $message);
    }

    public static function missingGetter($entity, $field, $getter)
    {
        $source = [
            'entity' => ClassUtils::getClass($entity),
            'getter' => $getter
        ];

        if ($entity instanceof ResourceInterface) {
            $source['pointer'] = sprintf('%s/%s', $entity->getResourceKey(), $field);
        }

        return static::createUnprocessable()
            ->error('missing-getter', $source, 'Missing field getter.');
    }

    public static function missingSetter($entity, $field, $setter)
    {
        $source = [
            'entity' => ClassUtils::getClass($entity),
            'setter' => $setter
        ];

        if ($entity instanceof ResourceInterface) {
            $source['pointer'] = sprintf('%s/%s', $entity->getResourceKey(), $field);
        }

        return static::createUnprocessable('Setter not found for entity')
            ->error('missing-setter', $source, 'Missing field setter.');
    }

    public static function missingAdder($entity, $field, $adder)
    {
        $source = [
            'entity' => ClassUtils::getClass($entity),
            'adder' => $adder
        ];

        if ($entity instanceof ResourceInterface) {
            $source['pointer'] = sprintf('%s/%s', $entity->getResourceKey(), $field);
        }

        return static::createUnprocessable('Missing remover method.')
            ->error('missing-adder', $source, 'Missing collection adder.');
    }

    public static function missingRemover($entity, $field, $remover)
    {
        $source = [
            'entity' => ClassUtils::getClass($entity),
            'remover' => $remover
        ];

        if ($entity instanceof ResourceInterface) {
            $source['pointer'] = sprintf('%s/%s', $entity->getResourceKey(), $field);
        }

        return static::createUnprocessable('Missing remover method.')
            ->error('missing-remover', $source, 'Missing collection remover.');
    }

    static public function create(
        $httpStatus = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
        $message = '',
        Exception $previous = null
    ) {
        return new static($httpStatus, $message, $previous);
    }

    public function __construct(
        $httpStatus = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
        $message = '',
        \Exception  $previous = null
    ) {
        parent::__construct($message, $httpStatus, $previous);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function errorValidation($pointer, $detail, array $extra = [])
    {
        return $this->error('validation', ['pointer' => $pointer], $detail, $extra);
    }

    public function error($applicationCode, $source, $detail, array $extra = [])
    {
        $this->errors[] = array_merge(['code' => $applicationCode, 'source' => $source, 'detail' => $detail] + $extra);

        return $this;
    }
}
