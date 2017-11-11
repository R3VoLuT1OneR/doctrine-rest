<?php namespace Pz\Doctrine\Rest\Tests\QueryParser;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use PHPUnit\Framework\TestCase;
use Pz\Doctrine\Rest\QueryParser\FilterableQueryParser;

use Mockery as m;
use Pz\Doctrine\Rest\RestRequest;

class FilterableQueryParserTest extends TestCase
{

    public function test_filterable_query_parser_operator_filter_exception()
    {
        $this->expectException(\InvalidArgumentException::class);

        $request = m::mock(RestRequest::class)
            ->shouldReceive('getFilter')->andReturn([
                'field1' => ['operator' => 'not', 'value' => false],
                'field2' => ['operator' => 'eq', 'value' => 'test2'],
            ])
            ->getMock();

        $parser = new FilterableQueryParser($request, ['field1', 'field2']);
        $parser->handle(Criteria::create());
    }

    public function test_filterable_query_parser_operator_filter()
    {
        $request = m::mock(RestRequest::class)
            ->shouldReceive('getFilter')->andReturn([
                'field1' => ['operator' => 'neq', 'value' => false],
                'field2' => ['operator' => 'eq', 'value' => 'test2'],
            ])
            ->getMock();

        $parser = new FilterableQueryParser($request, ['field1', 'field2']);
        $criteria = Criteria::create();
        $parser->handle($criteria);

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::NEQ, $expressions[0]->getOperator());
        $this->assertEquals(false, $expressions[0]->getValue()->getValue());
        $this->assertEquals('field2', $expressions[1]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[1]->getOperator());
        $this->assertEquals('test2', $expressions[1]->getValue()->getValue());
    }

    public function test_filterable_query_parser_between_filter()
    {
        $request = m::mock(RestRequest::class)
            ->shouldReceive('getFilter')->andReturn(['field1' => ['start' => 1, 'end' => 10]])
            ->getMock();

        $parser = new FilterableQueryParser($request, ['field1', 'field2']);
        $criteria = Criteria::create();
        $parser->handle($criteria);

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::GTE, $expressions[0]->getOperator());
        $this->assertEquals(1, $expressions[0]->getValue()->getValue());
        $this->assertEquals('field1', $expressions[1]->getField());
        $this->assertEquals(Comparison::LT, $expressions[1]->getOperator());
        $this->assertEquals(10, $expressions[1]->getValue()->getValue());
    }

    public function test_filterable_query_parser_equal_filter()
    {
        $request = m::mock(RestRequest::class)
            ->shouldReceive('getFilter')->andReturn(['field1' => 'test1', 'field2' => 'test2', 'field3' => 'test3'])
            ->getMock();

        $parser = new FilterableQueryParser($request, ['field1', 'field2']);
        $criteria = Criteria::create();
        $parser->handle($criteria);

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[0]->getOperator());
        $this->assertEquals('test1', $expressions[0]->getValue()->getValue());
        $this->assertEquals('field2', $expressions[1]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[1]->getOperator());
        $this->assertEquals('test2', $expressions[1]->getValue()->getValue());
    }
}
