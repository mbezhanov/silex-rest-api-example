<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

$app = new Application();

$app->register(new DoctrineServiceProvider, [
    'db.options' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../db/sqlite.db',
    ],
]);

$app->register(new DoctrineOrmServiceProvider, [
    'orm.em.options' => [
        'mappings' => [
            [
                'type' => 'annotation',
                'namespace' => 'App\Entity',
                'path' => __DIR__ . '/App/Entity',
                'use_simple_annotation_reader' => false,
            ],
        ],
    ],
]);

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $app;
