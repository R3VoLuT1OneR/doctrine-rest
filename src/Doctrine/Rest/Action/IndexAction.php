<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Rest\Action\RestActionAbstract;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\QueryParser\FilterableQueryParser;
use Pz\Doctrine\Rest\QueryParser\PropertyQueryParser;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class IndexAction extends RestActionAbstract
{
    /**
     * Entity alias.
     *
     * @var string
     */
    protected $rootAlias;

    /**
     * @param RestRequestAbstract $request
     *
     * @return RestResponse
     */
    public function handle(RestRequestAbstract $request)
    {
        $request->authorize($this->repository()->getClassName());
        $chain = CriteriaChain::create($this->criteriaBuilders($request));

        $criteria = new Criteria(null,
            $request->getOrderBy(),
            $request->getStart(),
            $request->getLimit()
        );

        $qb = $this->repository()
            ->createQueryBuilder($this->repository()->alias())
            ->addCriteria($chain->process($criteria));

        return $this->response()->index($request, $qb);
    }

    /**
     * @param RestRequestAbstract $request
     *
     * @return array
     */
    protected function criteriaBuilders(RestRequestAbstract $request)
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
