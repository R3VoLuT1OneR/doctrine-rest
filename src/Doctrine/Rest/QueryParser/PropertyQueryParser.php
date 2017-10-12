<?php namespace Pz\Doctrine\Rest\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;

class PropertyQueryParser extends IndexQueryParser
{
    const PARAM_PREFIX = '';

    /**
     * @var string|bool
     */
    protected $property;

    /**
     * StringParser constructor.
     *
     * @param IndexRequestInterface $request
     * @param string                $property Property name that will be filtered by query.
     */
    public function __construct(IndexRequestInterface $request, $property)
    {
        parent::__construct($request);
        $this->property = $property;
    }

    /**
     * Assign LIKE operator on property if query is string.
     *
     * @param Criteria $criteria
     * @param          $query
     *
     * @return Criteria
     */
    public function processQuery(Criteria $criteria, $query)
    {
        if (is_string($query) && is_string($this->property)) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $query)
            );
        }

        return $criteria;
    }
}
