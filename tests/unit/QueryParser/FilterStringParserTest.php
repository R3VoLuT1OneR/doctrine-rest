<?php namespace Pz\Doctrine\Rest\Tests\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;
use Pz\Doctrine\Rest\QueryParser\StringFilterParser;

use Mockery as m;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestRequest;
use Symfony\Component\HttpFoundation\Request;

class FilterStringParserTest extends TestCase
{
    public function test_property_query_parser()
    {
        $request = new RestRequest(new Request(['filter' => 'queryString']));
        $parser = new StringFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }
}
