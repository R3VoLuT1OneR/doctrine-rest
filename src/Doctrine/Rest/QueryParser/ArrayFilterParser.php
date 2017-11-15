<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;

class ArrayFilterParser extends FilterParserAbstract
{
    /**
     * @var bool|array
     */
    protected $filterable;

    /**
     * StringParser constructor.
     *
     * @param RestRequestContract $request
     * @param bool|array          $filterable
     */
    public function __construct(RestRequestContract $request, array $filterable)
    {
        parent::__construct($request);
        $this->filterable = $filterable;
    }

    /**
     * @param Criteria $criteria
     * @param          $filter
     *
     * @return Criteria
     */
    public function applyFilter(Criteria $criteria, $filter)
    {
        if (!is_array($filter) || !is_array($this->filterable)) {
            return $criteria;
        }

        foreach ($this->filterable as $field) {
            if (array_key_exists($field, $filter)) {
                $this->processEqualFilter($criteria, $field, $filter[$field]);
                $this->processBetweenFilter($criteria, $field, $filter[$field]);
                $this->processOperatorFilter($criteria, $field, $filter[$field]);
            }
        }

        return $criteria;
    }

    /**
     * @param Criteria $criteria
     * @param          $field
     * @param          $value
     *
     * @return $this
     */
    protected function processEqualFilter(Criteria $criteria, $field, $value)
    {
        if (is_string($value)) {
            $criteria->andWhere(
                $criteria->expr()->eq($field, $value)
            );
        }

        return $this;
    }

    /**
     * @param Criteria $criteria
     * @param          $field
     * @param          $value
     *
     * @return $this
     */
    protected function processBetweenFilter(Criteria $criteria, $field, $value)
    {
        if (is_array($value) && isset($value['start']) && isset($value['end'])) {

            $criteria->andWhere($criteria->expr()->andX(
                $criteria->expr()->gte($field, $value['start']),
                $criteria->expr()->lt($field, $value['end'])
            ));

        }

        return $this;
    }

    /**
     * @param Criteria $criteria
     * @param          $field
     * @param          $value
     *
     * @return $this
     */
    protected function processOperatorFilter(Criteria $criteria, $field, $value)
    {
        if (is_array($value) && isset($value['operator']) && isset($value['value'])) {
            $operator = $value['operator'];

            if (!method_exists($criteria->expr(), $operator)) {
                throw RestException::createFilterError(['field' => $field, 'filter' => $value], 'Unknown operator.');
            }

            $criteria->andWhere(
                $criteria->expr()->$operator($field, $value['value'])
            );
        }

        return $this;
    }
}
