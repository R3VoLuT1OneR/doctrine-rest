<?php namespace Pz\Doctrine\Rest\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Related\RelatedItemAction;
use Pz\Doctrine\Rest\Action\Related\RelatedItemCreateAction;
use Pz\Doctrine\Rest\Action\Related\RelatedItemDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemUpdateAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Mockery as m;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\BlogTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var EntityManager|m\Mock
     */
    protected $em;

    static public $connection;

    /**
     * @var Application
     */
    static public $migrations;

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public static function generateEntityManager()
    {
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            ['tests/entities/'], false, getcwd().'/tests/tmp', new ArrayCache(), false
        );

        $doctrineConfig->setAutoGenerateProxyClasses(true);
        return EntityManager::create(static::connection(), $doctrineConfig);
    }

    public static function connection()
    {
        if (static::$connection === null) {
            static::$connection = DriverManager::getConnection([
                'driver'=>'pdo_sqlite',
                'dbname'=>':memory:',
            ]);
        }

        return static::$connection;
    }

    public static function migrations()
    {
        if (static::$migrations === null) {
            $helperSet = include(__DIR__ . '/../../cli-config.php');
            static::$migrations = ConsoleRunner::createApplication($helperSet);
            static::$migrations->setAutoExit(false);
        }

        return static::$migrations;
    }

    public function setUp()
    {
        parent::setUp();
        $this->em = static::generateEntityManager();
        static::migrations()->run(new StringInput('migrations:migrate --quiet'));
    }

    public function tearDown()
    {
        parent::tearDown();
        static::migrations()->run(new StringInput('migrations:migrate first --quiet'));
    }

    public function assertResponseContent($subset, RestResponse $response)
    {
        $this->assertArraySubset($subset, $this->decodeResponseJson($response));
        return $this;
    }

    public function decodeResponseJson(RestResponse $response)
    {
        $decodedResponse = json_decode($response->getContent(), true);

        if (is_null($decodedResponse) || $decodedResponse === false) {
            if ($this->exception) {
                throw $this->exception;
            } else {
                PHPUnitTestCase::fail('Invalid JSON was returned from the route.');
            }
        }

        return $decodedResponse;
    }

    protected function getUserRelationshipsRoleUpdateAction()
    {
        return new RelationshipsItemUpdateAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelationshipsRoleItemDeleteAction()
    {
        return new RelationshipsItemDeleteAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelationshipsRoleItemAction()
    {
        return new RelationshipsItemAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelatedRoleItemAction()
    {
        return new RelatedItemAction(
            RestRepository::create($this->em, User::class), Role::getResourceKey(),
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelatedRoleItemCreateAction()
    {
        return new RelatedItemCreateAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelatedRoleItemDeleteAction()
    {
        return new RelatedItemDeleteAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    /**
     * @return RelatedCollectionAction
     */
    protected function getUserRelatedBlogCollectionAction()
    {
        return new RelatedCollectionAction(
            RestRepository::create($this->em, User::class), 'user',
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    protected function getUserRelatedBlogCollectionCreateAction()
    {
        return new RelatedCollectionCreateAction(
            RestRepository::create($this->em, User::class), 'blogs', 'user',
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    protected function getUserRelatedBlogCollectionDeleteAction()
    {
        return new RelatedCollectionDeleteAction(
            RestRepository::create($this->em, User::class), 'blogs',
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    protected function getBlogItemAction()
    {
        return new ItemAction(
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    /**
     * Role controller
     */
    protected function getRoleItemAction()
    {
        return new ItemAction(
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }
}
