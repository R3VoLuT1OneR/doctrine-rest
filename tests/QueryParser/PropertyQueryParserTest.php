<?php namespace Pz\Doctrine\Rest\Tests\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;
use Pz\Doctrine\Rest\QueryParser\PropertyQueryParser;
use Pz\Doctrine\Rest\Request\IndexRequestAbstract;

use Mockery as m;

class PropertyQueryParserTest extends TestCase
{
    public function test_property_query_parser()
    {
        $request = m::mock(IndexRequestAbstract::class)
            ->shouldReceive('getQuery')->andReturn('queryString')
            ->getMock();

        $parser = new PropertyQueryParser($request, 'testField');
        $criteria = Criteria::create();
        $parser->handle($criteria);

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }
}
