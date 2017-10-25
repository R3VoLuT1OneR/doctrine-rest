<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pz\Doctrine\Rest\Action\Index\ResponseData;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\QueryParser\FilterableQueryParser;
use Pz\Doctrine\Rest\QueryParser\PropertyQueryParser;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;

trait IndexAction
{
    /**
     * Entity alias.
     *
     * @var string
     */
    protected $rootAlias;

    /**
     * Doctrine repository from where get data.
     *
     * @return RestRepository
     */
    abstract protected function repository();

    /**
     * @return RestResponseFactory
     */
    abstract protected function response();

    /**
     * @param IndexRequestInterface $request
     *
     * @return RestResponse
     */
    public function index(IndexRequestInterface $request)
    {
        try {

            $request->authorize($this->repository()->getClassName());
            $chain = CriteriaChain::create($this->criteriaBuilders($request));

            $criteria = new Criteria(null,
                $request->getOrderBy(),
                $request->getStart(),
                $request->getLimit()
            );

            $qb = $this->repository()
                ->createQueryBuilder($this->alias())
                ->addCriteria($chain->process($criteria));

            return $this->response()->index($request, $this->buildResponseData($qb));
        } catch (\Exception $e) {
            return $this->response()->exception($e);
        }
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return ResponseData
     */
    protected function buildResponseData(QueryBuilder $qb)
    {
        return new ResponseData($qb);
    }

    /**
     * @return string
     */
    protected function alias()
    {
        if ($this->rootAlias === null) {
            $reflectionClass = new \ReflectionClass($this->repository()->getClassName());
            $this->rootAlias = strtolower($reflectionClass->getShortName()[0]);
        }

        return $this->rootAlias;
    }

    /**
     * @param IndexRequestInterface $request
     *
     * @return array
     */
    protected function criteriaBuilders(IndexRequestInterface $request)
    {
        return [
            new PropertyQueryParser($request, $this->getQueryProperty()),
            new FilterableQueryParser($request, $this->getFilterable()),
        ];
    }

    /**
     * Param that can be filtered if query is string.
     *
     * @return null|string
     */
    protected function getQueryProperty()
    {
        return null;
    }

    /**
     * Get list of filterable entity properties.
     *
     * @return array
     */
    protected function getFilterable()
    {
        return [];
    }
}
