<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Doctrine\ORM\Mapping\ClassMetadata;
use Pz\Doctrine\Rest\Action\CreateAction;

use Mockery as m;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Mocks\ClassMetadataMock;
use Symfony\Component\HttpFoundation\Request;

class CreateActionTest extends AbstractActionTest
{
    use CreateAction;

    protected $newEntity;

    public function setUp()
    {
        parent::setUp();
        $this->newEntity = null;
        $metadata = new ClassMetadataMock(\stdClass::class);
        $metadata->setIdGeneratorType(ClassMetadataMock::GENERATOR_TYPE_AUTO);
        $this->em->getMetadataFactory()->setMetadataFor(\stdClass::class, $metadata);
    }

    protected function createEntity($request)
    {
        $this->assertInstanceOf(CreateRequestInterface::class, $request);
        $this->newEntity = new \stdClass();

        return $this->newEntity;
    }

    public function test_create_action_exception()
    {
        $errors = ['testing', 'fasdfasdfas'];
        $request = m::mock(CreateRequestInterface::class)
            ->shouldReceive('authorize')->andThrow(new RestException(400, 'testing exception', $errors))
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->create($request));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            ['message' => 'testing exception', 'errors' => $errors],
            json_decode($response->getContent(), true)
        );
    }

    public function test_create_action()
    {
        $this->em->shouldReceive('persist')->withArgs([m::on(function($e) { return $e === $this->newEntity; })]);

        $request = m::mock(CreateRequestInterface::class)
            ->shouldReceive('http')->andReturn($http = new Request())
            ->shouldReceive('authorize')->with(static::class)
            ->getMock();

        $this->assertInstanceOf(RestResponse::class, $response = $this->create($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => ['test' => 'testData']], json_decode($response->getContent(), true));
    }
}
