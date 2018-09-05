<?php namespace Pz\Doctrine\Rest\Tests\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;
use Pz\Doctrine\Rest\QueryParser\SearchFilterParser;

use Mockery as m;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestRequest;
use Symfony\Component\HttpFoundation\Request;

class FilterStringParserTest extends TestCase
{
    public function test_array_key_search()
    {
        $request = new RestRequest(new Request(['filter' => ['search' => 'queryString']]));
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }

    public function test_property_query_parser()
    {
        $request = new RestRequest(new Request(['filter' => 'queryString']));
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }
}
