<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\BuilderChain\MemberInterface;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

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
     * @var RestRequestContract
     */
    protected $request;

    /**
     * @param Criteria $object
     *
     * @return Criteria
     */
    public function handle($object)
    {
        return $this->processQuery($object, $this->request->getFilter());
    }

    /**
     * IndexQueryParser constructor.
     *
     * @param RestRequestContract $request
     */
    public function __construct(RestRequestContract $request)
    {
        $this->request = $request;
    }
}
