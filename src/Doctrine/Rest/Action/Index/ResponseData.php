<?php namespace Pz\Doctrine\Rest\Action\Index;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ResponseData implements ResponseDataInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * ResponseData constructor.
     *
     * @param QueryBuilder $qb
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->paginator = new Paginator($qb, false);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->paginator->count();
    }

    /**
     * @return array
     */
    public function data()
    {
        return iterator_to_array($this->paginator);
    }

    /**
     * @return int
     */
    public function limit()
    {
        return $this->paginator->getQuery()->getMaxResults();
    }

    /**
     * @return int
     */
    public function start()
    {
        return $this->paginator->getQuery()->getFirstResult();
    }
}
