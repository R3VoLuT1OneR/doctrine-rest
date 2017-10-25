<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Response;

class RestException extends \HttpResponseException
{
    /**
     * @var int
     */
    protected $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * RestException constructor.
     *
     * @param int             $httpStatus
     * @param string          $message
     * @param array           $errors
     * @param \Exception|null $previous
     */
    public function __construct($httpStatus, $message, array $errors = [], \Exception $previous = null)
    {
        parent::__construct($message, $httpStatus, $previous);
        $this->httpStatus = $httpStatus;
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function httpStatus()
    {
        return $this->httpStatus;
    }
}
