<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Pz\Doctrine\Rest\Tests\TestCase;

// replace with file to your own project bootstrap
require_once './vendor/autoload.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = TestCase::generateEntityManager();
return ConsoleRunner::createHelperSet($entityManager);

