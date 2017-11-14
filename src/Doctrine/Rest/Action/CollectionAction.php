<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\QueryParser\ArrayFilterParser;
use Pz\Doctrine\Rest\QueryParser\FilterParserAbstract;
use Pz\Doctrine\Rest\QueryParser\StringFilterParser;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;

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
     *
     * @return RestResponse
     */
    protected function handle(RestRequestContract $request)
    {
        $resourceKey = $this->repository()->getResourceKey();
        $this->authorize($request, $this->repository()->getClassName());

        $qb = $this->repository()->sourceQueryBuilder();
        $this->applyPagination($request, $qb);
        $this->applyFilter($request, $qb);

         $paginator = new Paginator($qb, false);
         $collection = new Collection($paginator, $this->transformer(), $resourceKey);

        if ($qb->getMaxResults()) {
            $collection->setPaginator(
                new DoctrinePaginatorAdapter(
                    $paginator,
                    $this->paginatorUrlGenerator($request, $resourceKey)
                )
            );
        }

        return $this->response()->resource($request, $collection);
    }

    /**
     * @param RestRequestContract $request
     * @param QueryBuilder        $qb
     *
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
            new StringFilterParser($request, $this->getStringFilterField()),
            new ArrayFilterParser($request, $this->getArrayFilterFields()),
        ];
    }

    /**
     * @param RestRequestContract $request
     * @param             $resourceKey
     *
     * @return \Closure
     */
    protected function paginatorUrlGenerator(RestRequestContract $request, $resourceKey)
    {
        return function(int $page) use ($resourceKey, $request) {
            return !$resourceKey ? null : "{$request->getBaseUrl()}/$resourceKey?".http_build_query([
                'page' => [
                    'number'    => $page,
                    'size'      => $request->getLimit()
                ]
            ]);
        };
    }
}
