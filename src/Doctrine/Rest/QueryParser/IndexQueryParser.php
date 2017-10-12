<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\BuilderChain\MemberInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;

abstract class IndexQueryParser implements MemberInterface
{
    /**
     * @param Criteria $criteria
     * @param          $query
     *
     * @return mixed
     */
    abstract public function processQuery(Criteria $criteria, $query);

    /**
     * @var IndexRequestInterface
     */
    protected $request;

    /**
     * @param Criteria $object
     *
     * @return Criteria
     */
    public function handle($object)
    {
        return $this->processQuery($object, $this->request->getQuery());
    }

    /**
     * IndexQueryParser constructor.
     *
     * @param IndexRequestInterface $request
     */
    public function __construct(IndexRequestInterface $request)
    {
        $this->request = $request;
    }
}
