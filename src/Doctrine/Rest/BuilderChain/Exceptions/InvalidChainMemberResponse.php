<?php namespace Pz\Doctrine\Rest\BuilderChain\Exceptions;

class InvalidChainMemberResponse extends \RuntimeException
{
    protected $message = 'Chain member returned not `QueryBuilder`';
}
