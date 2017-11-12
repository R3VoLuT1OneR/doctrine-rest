<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Response;

class RestException extends \RuntimeException
{
    /**
     * @var int
     */
    protected $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(
        array       $errors = [],
        int         $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR,
        \Exception  $previous = null
    )
    {
        parent::__construct('Rest exception', 0, $previous);
        $this->errors = $errors;
        $this->httpStatus = $httpStatus;
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
