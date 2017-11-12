<?php namespace Pz\Doctrine\Rest\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use Pz\Doctrine\Rest\BuilderChain\CriteriaChain;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\QueryParser\FilterableQueryParser;
use Pz\Doctrine\Rest\QueryParser\PropertyQueryParser;
use Pz\Doctrine\Rest\RestAction;
use Pz\Doctrine\Rest\RestRequest;
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
     * @param RestRequest $request
     *
     * @return RestResponse
     */
    protected function handle(RestRequest $request)
    {
        $class = $this->repository()->getClassName();
        $resourceKey = $this->getResourceKey($class);
        $request->authorize($class);
        $chain = CriteriaChain::create($this->criteriaBuilders($request));

        $criteria = new Criteria(null,
            $request->getOrderBy(),
            $request->getStart(),
            $request->getLimit()
        );

        $qb = $this->repository()
            ->createQueryBuilder($this->repository()->alias())
            ->addCriteria($chain->process($criteria));

         $paginator = new Paginator($qb, false);
         $collection = new Collection($paginator, $this->transformer(), $resourceKey);

        if ($request->getLimit() !== null) {
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

    /**
     * @param RestRequest $request
     * @param             $resourceKey
     *
     * @return \Closure
     */
    protected function paginatorUrlGenerator(RestRequest $request, $resourceKey)
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
