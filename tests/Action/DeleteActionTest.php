<?php namespace Tests\Action;

use Pz\Doctrine\Rest\Action\DeleteAction;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;

use Mockery as m;

class DeleteActionTest extends AbstractActionTest
{
    use DeleteAction;

    public function test_delete_action_not_found()
    {
        $request = m::mock(DeleteRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->getMock();

        $this->repository->shouldReceive('find')->with(1)->andReturn(null);
        $this->assertNull($this->delete($request));
    }

    public function test_delete_action()
    {
        $entity = new \stdClass();
        $request = m::mock(DeleteRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->with($entity)
            ->getMock();

        $this->repository->shouldReceive('find')->with(1)->andReturn($entity);
        $this->em->shouldReceive('remove')->with($entity);
        $this->em->shouldReceive('flush');

        $this->assertEquals(null, $this->delete($request));
    }
}
