<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Mockery as m;
use Pz\Doctrine\Rest\Action\IndexAction;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRequestAbstract;
use Pz\Doctrine\Rest\RestResponse;

class IndexActionTest extends AbstractActionTest
{
    /** @var IndexAction */
    protected $action;

    public function setUp()
    {
        parent::setUp();
        $this->action = new IndexAction(
            $this->repository(),
            $this->response()
        );
    }

    public function test_index_action_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test exception');

        $request = m::mock(RestRequestAbstract::class)
            ->shouldReceive('authorize')->andThrow(new \Exception('test exception'))
            ->getMock();

        $this->action->handle($request);
    }

    public function test_index_action_rest_exception()
    {
        $this->markTestIncomplete('Implement exception handeling');

        $request = m::mock(RestRequestAbstract::class)
            ->shouldReceive('authorize')->andThrow(new RestException(422, 'test exception'))
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->action->handle($request));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArraySubset(['message' => 'test exception', 'errors' => []], json_decode($response->getContent(), true));
    }

    public function test_index_action_simple()
    {
        /** @var RestRequestAbstract $request */
        $request = m::mock(RestRequestAbstract::class)->makePartial();
        $request->shouldReceive('authorize')->withArgs([static::class]);
        $request->initialize([
            'filter' => 'testQuery',
            'sort' => 'field1,-field2',
            'page' => ['offset' => 2000, 'limit' => 1000],
        ]);

        $this->assertInstanceOf(RestResponse::class, $response = $this->action->handle($request));
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

    public function test_index_action_json_api()
    {
        /** @var RestRequestAbstract $request */
        $request = m::mock(RestRequestAbstract::class)->makePartial();
        $request->shouldReceive('authorize')->withArgs([static::class]);
        $request->initialize([
            'filter' => 'testQuery',
            'sort' => 'field1,-field2',
            'page' => ['offset' => 2000, 'limit' => 1000],
        ]);
        $request->headers->set('Accept', RestRequestAbstract::JSON_API_CONTENT_TYPE);

        $this->assertInstanceOf(RestResponse::class, $response = $this->action->handle($request));
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
