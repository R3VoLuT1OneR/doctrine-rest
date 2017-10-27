<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Doctrine\ORM\EntityNotFoundException;
use Pz\Doctrine\Rest\Action\ShowAction;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;

use Mockery as m;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Request;

class ShowActionTest extends AbstractActionTest
{
    use ShowAction;

    public function test_show_action_exception()
    {
        $request = m::mock(ShowRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->andThrow(new RestException(403, 'not auth'))
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->assertInstanceOf(RestResponse::class, $response = $this->show($request));
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['message' => 'not auth', 'errors' => []], json_decode($response->getContent(), true));
    }

    public function test_show_action_not_found()
    {
        $request = m::mock(ShowRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->show($request));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_action()
    {
        $request = m::mock(ShowRequestInterface::class)
            ->shouldReceive('http')->andReturn($http = new Request())
            ->shouldReceive('authorize')->with($this)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->assertInstanceOf(RestResponse::class, $response = $this->show($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => ['test' => 'testData']], json_decode($response->getContent(), true));
    }
}
