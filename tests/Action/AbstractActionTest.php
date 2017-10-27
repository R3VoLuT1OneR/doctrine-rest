<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseFactory;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;

use League\Fractal\TransformerAbstract;
use Mockery as m;
use Pz\Doctrine\Rest\Tests\DoctrineTest;

class TestTransformer extends TransformerAbstract
{
    public function transform()
    {
        return ['test' => 'testData'];
    }
}

abstract class AbstractActionTest extends DoctrineTest
{
    /**
     * @var RestRepository|m\Mock
     */
    protected $repository;

    /**
     * @var QueryBuilder|m\Mock
     */
    protected $queryBuilder;

    /**
     * @var EntityManager|m\Mock
     */
    protected $em;

    public function repository()
    {
        return $this->repository;
    }

    public function response()
    {
        return new FractalResponse(new TestTransformer());
    }

    public function setUp()
    {
        parent::setUp();

        $metadata = new ClassMetadata(static::class);
        $metadata->mapField(['fieldName' => 'field1', 'type' => 'integer']);
        $metadata->mapField(['fieldName' => 'field2', 'type' => 'integer']);
        $metadata->setIdentifier(['field1']);

        $this->em = m::mock($this->_getTestEntityManager())->makePartial();
        $this->em->getMetadataFactory()->setMetadataFor(static::class, $metadata);
        $this->repository = new RestRepository($this->em, $metadata);

//        $this->queryBuilder = m::mock(QueryBuilder::class, [$this->em])->makePartial()
//            ->shouldReceive('getQuery')->andReturn(m::mock(AbstractQuery::class))
//            ->shouldReceive('getRootAliases')->andReturn(['i'])
//            ->getMock();

//        $this->repository = m::mock(RestRepository::class, [$this->em, $metadata])->makePartial();

//        $this->repository->shouldReceive('getClassName')->andReturn(static::class)
//            ->shouldReceive('createQueryBuilder')->with('i')->andReturn($this->queryBuilder)
//            ->getMock();
    }
}
