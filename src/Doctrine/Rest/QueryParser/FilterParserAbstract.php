<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\BuilderChain\MemberInterface;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

abstract class FilterParserAbstract implements MemberInterface
{
    /**
     * @param Criteria $criteria
     * @param mixed    $filter
     *
     * @return Criteria
     */
    abstract public function applyFilter(Criteria $criteria, $filter);

    /**
     * @var mixed
     */
    protected $filter;

    /**
     * @param Criteria $object
     *
     * @return Criteria
     */
    public function __invoke($object)
    {
        return $this->applyFilter($object, $this->filter);
    }

    /**
     * IndexQueryParser constructor.
     *
     * @param RestRequestContract $request
     */
    public function __construct(RestRequestContract $request)
    {
        $this->filter = $request->getFilter();
    }
}
