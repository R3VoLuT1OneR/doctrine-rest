<?php namespace Pz\Doctrine\Rest\Action\Index;

interface ResponseDataInterface
{
    /**
     * @return array
     */
    public function data();

    /**
     * @return int
     */
    public function count();

    /**
     * @return int
     */
    public function limit();

    /**
     * @return int
     */
    public function start();
}
