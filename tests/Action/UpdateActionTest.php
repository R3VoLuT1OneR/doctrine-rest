<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\UpdateAction;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;

use Mockery as m;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateActionTest extends AbstractActionTest
{
    use UpdateAction;

    protected function updateEntity($request, $entity)
    {
        $this->assertInstanceOf(UpdateRequestInterface::class, $request);

        return $entity;
    }

    public function test_update_action_exception()
    {
        $request = m::mock(UpdateRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->andThrow(new RestException(403, 'not auth'))
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->assertInstanceOf(RestResponse::class, $response = $this->update($request));
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['message' => 'not auth', 'errors' => []], json_decode($response->getContent(), true));
    }

    public function test_update_action_not_found()
    {
        $request = m::mock(UpdateRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->update($request));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_action()
    {
        $request = m::mock(UpdateRequestInterface::class)
            ->shouldReceive('http')->andReturn($http = new Request())
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->with($this)
            ->getMock();

        $this->em->shouldReceive('find')->with(static::class, 1, null, null)->andReturn($this);
        $this->assertInstanceOf(RestResponse::class, $response = $this->update($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => ['test' => 'testData']], json_decode($response->getContent(), true));
    }

}
