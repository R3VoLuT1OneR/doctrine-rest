<?php namespace Doctrine\Rest\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Doctrine\Rest\Action\ItemAction;
use Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Doctrine\Rest\Action\Related\RelatedCollectionCreateAction;
use Doctrine\Rest\Action\Related\RelatedCollectionDeleteAction;
use Doctrine\Rest\Action\Related\RelatedItemAction;
use Doctrine\Rest\Action\Related\RelatedItemCreateAction;
use Doctrine\Rest\Action\Related\RelatedItemDeleteAction;
use Doctrine\Rest\Action\Relationships\RelationshipsItemAction;
use Doctrine\Rest\Action\Relationships\RelationshipsItemDeleteAction;
use Doctrine\Rest\Action\Relationships\RelationshipsItemUpdateAction;

use Doctrine\ORM\EntityManager;

use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Tests\Entities\Blog;
use Doctrine\Rest\Tests\Entities\Role;
use Doctrine\Rest\Tests\Entities\Transformers\BlogTransformer;
use Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Doctrine\Rest\Tests\Entities\User;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;

abstract class TestCase extends PHPUnitTestCase
{
    private EntityManager $em;
    static private Connection $connection;
    static private Application $console;

    public static function connection()
    {
        if (!isset(static::$connection)) {
            static::$connection = DriverManager::getConnection([
                'driver'=>'pdo_sqlite',
                'dbname'=>':memory:',
            ]);
        }

        return static::$connection;
    }

    public static function console(): Application
    {
        if (!isset(static::$console)) {
            $helperSet = ConsoleRunner::createHelperSet(static::generateEntityManager());
            static::$console = ConsoleRunner::createApplication($helperSet);
            static::$console->setAutoExit(false);
        }

        return static::$console;
    }

    public static function generateEntityManager(): EntityManager
    {
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            ['tests/entities/'], false, getcwd().'/tests/tmp', new ArrayCache(), false
        );

        $doctrineConfig->setAutoGenerateProxyClasses(true);
        return EntityManager::create(static::connection(), $doctrineConfig);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->em = static::generateEntityManager();
        static::console()->run(new StringInput('migrations:migrate --quiet'));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        static::console()->run(new StringInput('migrations:migrate first --quiet'));
    }

    protected function assertResponseContent($subset, RestResponse $response)
    {
        $response->getBody()->rewind();
        $content = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals($subset, $content);
        return $this;
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
