<?php namespace Tests\Action;

use Doctrine\ORM\EntityNotFoundException;
use Pz\Doctrine\Rest\Action\ShowAction;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;

use Mockery as m;

class ShowActionTest extends AbstractActionTest
{
    use ShowAction;

    public function test_show_action_not_found()
    {
        $this->expectException(EntityNotFoundException::class);

        $request = m::mock(ShowRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->repository->shouldReceive('find')->with(1, null, null)->andReturn(null);
        $this->show($request);
    }

    public function test_show_action()
    {
        $entity = new \stdClass();
        $request = m::mock(ShowRequestInterface::class)
            ->shouldReceive('authorize')->with($entity)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->repository->shouldReceive('find')->with(1, null, null)->andReturn($entity);
        $this->response->shouldReceive('show')->with($request, $entity)->andReturn('show view');

        $this->assertEquals(['data' => ['test' => 'testData']], $this->show($request));
    }
}
