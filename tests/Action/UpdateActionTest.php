<?php namespace Tests\Action;

use Pz\Doctrine\Rest\Action\UpdateAction;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;

use Mockery as m;

class UpdateActionTest extends AbstractActionTest
{
    use UpdateAction;

    protected function updateEntity($request, $entity)
    {
        $this->assertInstanceOf(UpdateRequestInterface::class, $request);

        return $entity;
    }

    public function test_update_action()
    {
        $entity = new \stdClass();
        $request = m::mock(UpdateRequestInterface::class)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('authorize')->with($entity)
            ->getMock();

        $this->repository->shouldReceive('find')->with(1, null, null)->andReturn($entity);
        $this->response->shouldReceive('update')->with($request, $entity)->andReturn('update view');
        $this->em->shouldReceive('flush');

        $this->assertEquals(['data' => ['test' => 'testData']], $this->update($request));
    }

}
