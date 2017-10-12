<?php namespace Pz\Doctrine\Rest\BuilderChain;

use Doctrine\Common\Collections\Criteria;

class CriteriaChain extends Chain
{
    /**
     * @return string
     */
    public function buildClass()
    {
        return Criteria::class;
    }

    /**
     * @param Criteria $object
     *
     * @return Criteria
     */
    public function process($object)
    {
        return parent::process($object);
    }
}
