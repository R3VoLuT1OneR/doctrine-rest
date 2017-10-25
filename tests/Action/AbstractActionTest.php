<?php namespace Tests\Action;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponseFactory;
use Doctrine\ORM\QueryBuilder;
use Mockery as m;

class TestTransformer extends TransformerAbstract
{
    public function transform()
    {
        return ['test' => 'testData'];
    }
}

abstract class AbstractActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RestRepository|m\Mock
     */
    protected $repository;

    /**
     * @var RestResponseFactory|m\Mock
     */
    protected $response;

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
        return $this->response;
    }

    public function setUp()
    {
        parent::setUp();

        $this->em = m::mock(EntityManager::class)->makePartial();

        $this->queryBuilder = m::mock(QueryBuilder::class, [$this->em])->makePartial()
            ->shouldReceive('getQuery')->andReturn(m::mock(AbstractQuery::class))
            ->shouldReceive('getRootAliases')->andReturn(['i'])
            ->getMock();

        $this->repository = m::mock(RestRepository::class, [$this->em, m::mock(ClassMetadata::class)])->makePartial();
        $this->repository->shouldReceive('getClassName')->andReturn(static::class)
            ->shouldReceive('createQueryBuilder')->with('i')->andReturn($this->queryBuilder)
            ->getMock();

        $this->response = m::mock(RestResponseFactory::class);
    }

}
