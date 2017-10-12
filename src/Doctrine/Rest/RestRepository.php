<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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

    /**
     * @param mixed $id
     * @param null  $lockMode
     * @param null  $lockVersion
     *
     * @return object
     * @throws EntityNotFoundException
     */
    public function findById($id, $lockMode = null, $lockVersion = null)
    {
        if (null === ($entity = $this->find($id, $lockMode, $lockVersion))) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }
}
