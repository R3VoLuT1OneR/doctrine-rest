<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class SearchFilterParser extends FilterParserAbstract
{
    const PARAM_PREFIX = '';

    const SEARCH_KEY = 'search';

    /**
     * @var string|bool
     */
    protected $property;

    /**
     * @var string
     */
    protected $searchKey;

    /**
     * StringParser constructor.
     *
     * @param RestRequestContract $request
     * @param string              $property Property name that will be filtered by query.
     * @param string              $searchKey
     */
    public function __construct(RestRequestContract $request, $property, $searchKey = self::SEARCH_KEY)
    {
        parent::__construct($request);
        $this->property = $property;
        $this->searchKey = $searchKey;
    }

    /**
     * Assign LIKE operator on property if query is string.
     *
     * @param Criteria $criteria
     * @param          $filter
     *
     * @return Criteria
     */
    public function applyFilter(Criteria $criteria, $filter)
    {
        if (is_string($filter) && is_string($this->property)) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter)
            );
        }

        if (is_array($filter) && isset($filter[$this->searchKey])) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter[$this->searchKey])
            );
        }

        return $criteria;
    }
}
