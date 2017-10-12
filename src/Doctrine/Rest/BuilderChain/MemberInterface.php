<?php namespace Pz\Doctrine\Rest\BuilderChain;

interface MemberInterface
{
    /**
     * Chain member should process object and return it.
     *
     * @param object $object
     *
     * @return object
     */
    public function handle($object);
}
