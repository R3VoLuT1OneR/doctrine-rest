<?php namespace Pz\Doctrine\Rest\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class RestException extends \Exception
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param \Exception $exception
     * @param bool       $debug
     *
     * @return $this
     */
    public static function createFromException(\Exception $exception, $debug = false)
    {
        $extra = $debug ? ['trace' => $exception->getTrace()] : [];

        return static::create(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $exception)
            ->error('internal-error', [], $exception->getMessage(), $extra);
    }

    /**
     * @param string $message
     *
     * @return RestException
     */
    public static function createForbidden($message = 'Forbidden.')
    {
        return static::create(Response::HTTP_FORBIDDEN, $message);
    }

    /**
     * @param string $message
     *
     * @return RestException
     */
    public static function createUnprocessable($message = '')
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    /**
     * @param int|array $id
     * @param string    $resourceKey
     * @param string    $message
     *
     * @return $this
     */
    public static function createNotFound($id, $resourceKey, $message = '')
    {
        return static::create(Response::HTTP_NOT_FOUND, 'Entity not found.')
            ->error('entity-not-found', ['type' => $resourceKey, 'id' => $id], $message);
    }

    /**
     * @param array $source
     * @param       $message
     *
     * @return $this
     */
    public static function createFilterError(array $source, $message)
    {
        return static::create(Response::HTTP_BAD_REQUEST, 'Wrong filter input.')
            ->error('filter-input', $source, $message);
    }

    /**
     * @return static
     */
    public static function missingRootData()
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->error('missing-root-data', [], "Missing `data` Member at document top level.");
    }

    /**
     * @param string $pointer
     *
     * @return $this
     */
    public static function missingData($pointer)
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->error('missing-data', ['pointer' => $pointer], "Missing `data` member at pointer scope.");
    }

    /**
     * @param string $pointer
     *
     * @return $this
     */
    public static function missingAttributes($pointer)
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->error('missing-attributes', ['pointer' => $pointer], "Missing `data.attributes` at pointer level.");
    }

    /**
     * @param string $pointer
     *
     * @return $this
     */
    public static function unknownAttribute($pointer)
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->error('unknown-attribute', ['pointer' => $pointer], 'Unknown attribute, please see source.');
    }

    /**
     * @param string $pointer
     *
     * @return static
     */
    public static function unknownRelation($pointer)
    {
        return static::create(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->error('unknown-relation', ['pointer' => $pointer], 'Unknown relation, please see source.');
    }

    /**
     * @param int             $httpStatus
     * @param string          $message
     * @param \Exception|null $previous
     *
     * @return static
     */
    static public function create(
        $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR,
        $message = '',
        \Exception $previous = null
    ) {
        return new static($httpStatus, $message, $previous);
    }

    /**
     * RestException constructor.
     *
     * @param int             $httpStatus
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct(
        $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR,
        $message = '',
        \Exception  $previous = null
    ) {
        parent::__construct($message, $httpStatus, $previous);
    }

    /**
     * @param $attribute
     * @param $detail
     *
     * @return $this
     */
    public function errorAttribute($attribute, $detail)
    {
        return $this->error('attribute-error', ['attribute' => $attribute], $detail);
    }

    /**
     * @param $relation
     * @param $detail
     *
     * @return RestException
     */
    public function errorRelation($relation, $detail)
    {
        return $this->error('relation-error', ['relation' => $relation], $detail);
    }

    /**
     * @param string $applicationCode
     * @param array  $source
     * @param string $detail
     * @param array  $extra
     *
     * @return $this
     */
    public function error($applicationCode, $source, $detail, $extra = [])
    {
        $this->errors[] = array_merge(['code' => $applicationCode, 'source' => $source, 'detail' => $detail] + $extra);

        return $this;
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}
