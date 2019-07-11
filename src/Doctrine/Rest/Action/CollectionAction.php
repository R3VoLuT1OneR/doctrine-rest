<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\QueryParser\ArrayFilterParser;
use Pz\Doctrine\Rest\QueryParser\FilterParserAbstract;
use Pz\Doctrine\Rest\QueryParser\SearchFilterParser;
use Pz\Doctrine\Rest\Resource\Collection;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;

/**
 * Action for providing collection (list or array) of data with API.
 */
class CollectionAction extends RestAction
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
     * Param that can be filtered if query is string.
     *
     * @return null|string
     */
    public function getStringFilterField()
    {
        return $this->filterProperty;
    }

    /**
     * Get list of filterable entity properties.
     *
     * @return array
     */
    public function getArrayFilterFields()
    {
        return $this->filterable;
    }

    /**
     * @param RestRequestContract $request
     * @return RestResponse
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function handle($request)
    {
        $this->authorize($request, $this->repository()->getClassName());

        $qb = $this->repository()->sourceQueryBuilder($request);
        $this->applyPagination($request, $qb);
        $this->applyFilter($request, $qb);

        return RestResponseFactory::resource($request, $this->prepareCollection($request, $qb));
    }

    /**
     * @param RestRequestContract   $request
     * @param QueryBuilder          $qb
     * @return Collection
     */
    protected function prepareCollection($request, QueryBuilder $qb)
    {
        $paginator = new Paginator($qb, false);
        $collection = new Collection($paginator, $this->transformer(), $this->repository()->getResourceKey());

        if ($qb->getMaxResults()) {
            $collection->setPaginator(
                new DoctrinePaginatorAdapter(
                    $paginator,
                    function(int $page) use ($request) {
                        // return !$resourceKey ? null : "{$request->getBaseUrl()}/$resourceKey?".http_build_query([
                        return $request->getBasePath().'?'.http_build_query([
                            'page' => [
                                'number'    => $page,
                                'size'      => $request->getLimit()
                            ]
                        ]);
                    }
                )
            );
        }

        return $collection;
    }

    /**
     * @param RestRequestContract $request
     * @param QueryBuilder        $qb
     *
     * @throws \Doctrine\ORM\Query\QueryException
     * @return $this
     */
    protected function applyPagination(RestRequestContract $request, QueryBuilder $qb)
    {
        $qb->addCriteria(
            new Criteria(null,
                $request->getOrderBy(),
                $request->getStart(),
                $request->getLimit()
            )
        );

        return $this;
    }

    /**
     * @param RestRequestContract $request
     * @param QueryBuilder        $qb
     *
     * @throws \Doctrine\ORM\Query\QueryException
     * @return $this
     */
    protected function applyFilter(RestRequestContract $request, QueryBuilder $qb)
    {
        $qb->addCriteria(
            CriteriaChain::create($this->filterParsers($request))->process()
        );

        return $this;
    }

    /**
     * @param RestRequestContract $request
     *
     * @return array|FilterParserAbstract[]
     */
    protected function filterParsers(RestRequestContract $request)
    {
        return [
            new SearchFilterParser($request, $this->getStringFilterField()),
            new ArrayFilterParser($request, $this->getArrayFilterFields()),
        ];
    }
}
