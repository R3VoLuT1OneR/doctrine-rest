<?php namespace Pz\Doctrine\Rest\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Mockery as m;
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
}
