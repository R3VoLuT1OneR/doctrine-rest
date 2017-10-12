<?php namespace Pz\Doctrine\Rest\Request;

use Pz\Doctrine\Rest\RestRequestInterface;

interface ShowRequestInterface extends RestRequestInterface
{
    /**
     * @return int|mixed
     */
    public function getId();
}
