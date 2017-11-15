<?php namespace Pz\Doctrine\Rest\BuilderChain\Exceptions;

class InvalidChainMemberResponse extends \RuntimeException
{
    /**
     * InvalidChainMemberResponse constructor.
     *
     * @param string     $class
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($class, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Chain member returned not `%s`', $class), $code, $previous);
    }
}
