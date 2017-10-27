<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\IndexAction;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Mockery as m;
use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexActionTest extends AbstractActionTest
{
    use IndexAction;

    public function test_index_action_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test exception');

        $request = m::mock(IndexRequestInterface::class)
            ->shouldReceive('authorize')->andThrow(new \Exception('test exception'))
            ->getMock();

        $this->index($request);
    }

    public function test_index_action_rest_exception()
    {
        $request = m::mock(IndexRequestInterface::class)
            ->shouldReceive('authorize')->andThrow(new RestException(422, 'test exception'))
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->index($request));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArraySubset(['message' => 'test exception', 'errors' => []], json_decode($response->getContent(), true));
    }

    public function test_index_action_simple()
    {
        /** @var IndexRequestInterface $request */
        $request = m::mock(IndexRequestInterface::class)
            ->shouldReceive('http')->andReturn(new Request())
            ->shouldReceive('authorize')->withArgs([static::class])
            ->shouldReceive('getQuery')->andReturn('testQuery')
            ->shouldReceive('getLimit')->andReturn(1000)
            ->shouldReceive('getStart')->andReturn(2000)
            ->shouldReceive('getOrderBy')->andReturn(['field1' => 'asc', 'field2' => 'desc'])
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->index($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total' => 0,
                    'count' => 0,
                    'per_page' => 1000,
                    'current_page' => 3,
                    'total_pages' => 0,
                    'links' => [
                        'previous' => null,
                    ],
                ],
            ],
        ], json_decode($response->getContent(), true));
    }

    public function test_index_action()
    {
        $httpRequest =  new Request();
        $httpRequest->attributes->add([
            'include' => 'test',
            'exclude' => 'test'
        ]);

        $httpRequest->headers->set('Accept', FractalResponse::JSON_API_CONTENT_TYPE);

        /** @var IndexRequestInterface $request */
        $request = m::mock(IndexRequestInterface::class)
            ->shouldReceive('http')->andReturn($httpRequest)
            ->shouldReceive('authorize')->withArgs([static::class])
            ->shouldReceive('getQuery')->andReturn('testQuery')
            ->shouldReceive('getLimit')->andReturn(1000)
            ->shouldReceive('getStart')->andReturn(2000)
            ->shouldReceive('getOrderBy')->andReturn(['field1' => 'asc', 'field2' => 'desc'])
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->index($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total' => 0,
                    'count' => 0,
                    'per_page' => 1000,
                    'current_page' => 3,
                    'total_pages' => 0,
                ],
            ],
            'links' => [
                'self' => null,
                'first' => null,
                'prev' => null,
                'last' => null,
            ],
        ], json_decode($response->getContent(), true));
    }
}
