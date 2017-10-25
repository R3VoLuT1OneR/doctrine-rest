<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityRepository;

class RestRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }
}
