<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Pz\Doctrine\Rest\RestRequestAbstract;

class FilterableQueryParser extends IndexQueryParser
{
    /**
     * @var bool|array
     */
    protected $filterable;

    /**
     * StringParser constructor.
     *
     * @param RestRequestAbstract $request
     * @param bool|array           $filterable
     */
    public function __construct(RestRequestAbstract $request, array $filterable)
    {
        parent::__construct($request);
        $this->filterable = $filterable;
    }

    /**
     * @param Criteria $criteria
     * @param          $query
     *
     * @return Criteria
     */
    public function processQuery(Criteria $criteria, $query)
    {
        if (!is_array($query) || !is_array($this->filterable)) {
            return $criteria;
        }

        foreach ($this->filterable as $field) {
            if (array_key_exists($field, $query)) {
                $this->processEqualFilter($criteria, $field, $query[$field]);
                $this->processBetweenFilter($criteria, $field, $query[$field]);
                $this->processOperatorFilter($criteria, $field, $query[$field]);
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
                throw new \InvalidArgumentException(sprintf('Unknown operator: %s', $value['operator']));
            }

            $criteria->andWhere(
                $criteria->expr()->$operator($field, $value['value'])
            );
        }

        return $this;
    }
}
