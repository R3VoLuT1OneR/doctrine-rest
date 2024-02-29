<?php namespace Pz\Doctrine\Rest\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\ConsoleRunner;
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

use Mockery as m;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\BlogTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;

abstract class TestCase extends PHPUnitTestCase
{
    use ArraySubsetAsserts;

    /**
     * @var EntityManager|m\Mock
     */
    protected $em;

    static public Connection $connection;

    static public Application $migrations;

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public static function generateEntityManager(): EntityManager
    {
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            paths: ['tests/entities/'],
            proxyDir: __DIR__.'/../tmp',
            useSimpleAnnotationReader: false
        );

        $doctrineConfig->setAutoGenerateProxyClasses(true);

        return new EntityManager(static::connection(), $doctrineConfig);
    }

    public static function connection(): Connection
    {
        if (!isset(static::$connection)) {
            static::$connection = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'path' => ':memory:'
            ]);
        }

        return static::$connection;
    }

    public static function migrations(): Application
    {
        // TODO: Disabled for now, as it's not working
        //       https://github.com/doctrine/migrations/issues/1402
        if (!isset(static::$migrations) || true) {
            $configuration = new Configuration();
            $configuration->addMigrationsDirectory(
                'Pz\Doctrine\Rest\Tests\Migrations',
                __DIR__ . '/../migrations'
            );

            $dependencyFactory = DependencyFactory::fromConnection(
                configurationLoader: new ExistingConfiguration($configuration),
                connectionLoader: new ExistingConnection(static::connection()),
            );

            $migrationsApplication = ConsoleRunner::createApplication(
                dependencyFactory: $dependencyFactory
            );

            $migrationsApplication->setAutoExit(false);

            static::$migrations = $migrationsApplication;
        }

        return static::$migrations;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->em = static::generateEntityManager();
        static::migrations()->run(new StringInput('migrations:migrate --quiet'));
    }

    public function tearDown(): void
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
