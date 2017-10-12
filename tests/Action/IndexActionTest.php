<?php namespace Tests\Action;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Pz\Doctrine\Rest\Action\Index\ResponseData;
use Pz\Doctrine\Rest\Action\Index\ResponseDataInterface;
use Pz\Doctrine\Rest\Action\IndexAction;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Doctrine\ORM\Query\Expr\OrderBy;
use Mockery as m;

class IndexActionTest extends AbstractActionTest
{
    use IndexAction {
        buildResponseData as traitBuildResponseData;
    }

    public function buildResponseData(QueryBuilder $qb)
    {
        $reflectionClass = new \ReflectionClass(ResponseData::class);
        $property = $reflectionClass->getProperty('paginator');
        $property->setAccessible(true);

        $responseData = $this->traitBuildResponseData($qb);

        $paginator = m::mock(Paginator::class)
            ->shouldReceive('count')->andReturn(3000)
            ->shouldReceive('getIterator')->andReturn(new \ArrayIterator([1,2,3]))
            ->shouldReceive('getQuery')->andReturn(
                m::mock()
                    ->shouldReceive('getMaxResults')->andReturn(1000)
                    ->shouldReceive('getFirstResult')->andReturn(2000)
                    ->getMock()
            )
            ->getMock();

        $property->setValue($responseData, $paginator);

        return $responseData;
    }

    public function test_index_action()
    {
        /** @var IndexRequestInterface $indexRequest */
        $indexRequest = m::mock(IndexRequestInterface::class)
            ->shouldReceive('authorize')->withArgs([static::class])
            ->shouldReceive('getQuery')->andReturn('testQuery')
            ->shouldReceive('getLimit')->andReturn(1000)
            ->shouldReceive('getStart')->andReturn(2000)
            ->shouldReceive('getOrderBy')->andReturn(['field1' => 'asc', 'field2' => 'desc'])
            ->getMock();

        $this->assertEquals([
            'data' => [
                ['test' => 'testData'],
                ['test' => 'testData'],
                ['test' => 'testData'],
            ],
            'meta' => [
                'count' => 3000,
                'limit' => 1000,
                'start' => 2000,
            ],
        ], $this->index($indexRequest));

        $this->assertEquals(1000, $this->queryBuilder->getMaxResults());
        $this->assertEquals(2000, $this->queryBuilder->getFirstResult());

        /** @var OrderBy $orderBy */
        $orderBy = $this->queryBuilder->getDQLPart('orderBy');
        $this->assertCount(2, $orderBy);
        $this->assertEquals('i.field1 ASC', $orderBy[0]);
        $this->assertEquals('i.field2 DESC', $orderBy[1]);
    }
}
