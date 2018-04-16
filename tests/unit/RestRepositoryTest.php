<?php namespace Pz\Doctrine\Rest\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;

use Mockery as m;
use Mockery\MockInterface;
use Pz\Doctrine\Rest\RestRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestRepositoryTest extends TestCase
{

    public function test_find_by_identifier_invalid_entity()
    {
        $this->expectException(RestException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage('Got not JsonApiResource entity.');

        $id = 777;

        /** @var EntityManager|MockInterface $emMock */
        $emMock = m::mock(EntityManager::class);
        $emMock->shouldReceive('find')
            ->withArgs([\stdClass::class, $id, null, null])
            ->andReturn(new \stdClass());

        /** @var ClassMetadata|MockInterface $classMetadata */
        $classMetadata = m::mock(ClassMetadata::class);
        $classMetadata->name = \stdClass::class;

        $request = new Request(['id' => $id]);

        $repository = new RestRepository($emMock, $classMetadata);
        $this->assertEquals('', $repository->getResourceKey());
        $repository->findById((new RestRequest($request))->getId());
    }
}
