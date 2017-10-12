<?php namespace Tests\Action;

use Pz\Doctrine\Rest\Action\CreateAction;

use Mockery as m;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;

class CreateActionTest extends AbstractActionTest
{
    use CreateAction;

    protected $newEntity;

    public function setUp()
    {
        parent::setUp();
        $this->newEntity = null;
    }

    protected function createEntity($request)
    {
        $this->assertInstanceOf(CreateRequestInterface::class, $request);
        $this->newEntity = new \stdClass();

        return $this->newEntity;
    }

    public function test_create_action()
    {
        $request = m::mock(CreateRequestInterface::class)
            ->shouldReceive('authorize')->with(static::class)
            ->getMock();

        $this->em->shouldReceive('flush');
        $this->response->shouldReceive('create')
            ->with($request, m::on(function($entity) { return $this->newEntity === $entity; }))
            ->andReturn('create view');

        $this->assertEquals(['data' => ['test' => 'testData']], $this->create($request));
    }

}
