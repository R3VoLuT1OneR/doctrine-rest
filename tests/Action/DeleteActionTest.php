<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\DeleteAction;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;

use Mockery as m;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestResponse;

class DeleteActionTest extends AbstractActionTest
{
    use DeleteAction;

    public function test_delete_action_exception()
    {
        $request = m::mock(DeleteRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->andThrow(new RestException(403, 'not auth'))
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->assertInstanceOf(RestResponse::class, $response = $this->delete($request));
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['message' => 'not auth', 'errors' => []], json_decode($response->getContent(), true));
    }

    public function test_delete_action_not_found()
    {
        $request = m::mock(DeleteRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->delete($request));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_action()
    {
        $request = m::mock(DeleteRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->with($this)
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->em->shouldReceive('remove')->with($this);

        $this->assertInstanceOf(RestResponse::class, $response = $this->delete($request));
        $this->assertEquals(200, $response->getStatusCode());
    }
}
