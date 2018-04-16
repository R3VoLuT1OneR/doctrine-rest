<?php
$loader = require __DIR__ . '/vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return [
    'migrations_namespace' => 'Pz\Doctrine\Rest\Tests\Migrations',
    'migrations_directory' => __DIR__.'/tests/migrations'
];
