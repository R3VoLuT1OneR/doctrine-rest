<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\QueryParser\FilterableQueryParser;
use Pz\Doctrine\Rest\QueryParser\PropertyQueryParser;
use Pz\Doctrine\Rest\RestActionAbstract;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;

/**
 * Action for providing collection (list or array) of data with API.
 */
class CollectionAction extends RestActionAbstract
{
    /**
     * Field that can be filtered if filter is string.
     *
     * @var string
     */
    protected $filterProperty;

    /**
     * Get list of filterable entity fields.
     *
     * @var array
     */
    protected $filterable = [];

    /**
     * @param string $property
     *
     * @return $this
     */
    public function setFilterProperty($property)
    {
        $this->filterProperty = $property;
        return $this;
    }

    /**
     * @param array $filterable
     *
     * @return $this
     */
    public function setFilterable(array $filterable)
    {
        $this->filterable = $filterable;
        return $this;
    }

    /**
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    protected function handle(RestRequest $request)
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

        return $this->response()->collection($request, $qb);
    }

    /**
     * @param RestRequest $request
     *
     * @return array
     */
    protected function criteriaBuilders(RestRequest $request)
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
        return $this->filterProperty;
    }

    /**
     * Get list of filterable entity properties.
     *
     * @return array
     */
    protected function getFilterable()
    {
        return $this->filterable;
    }
}
