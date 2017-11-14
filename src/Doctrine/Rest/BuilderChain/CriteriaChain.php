<?php namespace Pz\Doctrine\Rest\BuilderChain;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

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
    public function process($object = null)
    {
        if ($object === null) {
            $object = new Criteria();
        }

        return parent::process($object);
    }
}
