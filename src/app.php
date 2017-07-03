<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use Bezhanov\Silex\Routing\RouteAnnotationsProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$app = new Application();

$app->register(new DoctrineServiceProvider, [
        'db.options' => [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../db/sqlite.db',
        ],
    ])
    ->register(new DoctrineOrmServiceProvider, [
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
    ])
    ->register(new ServiceControllerServiceProvider())
    ->register(new RouteAnnotationsProvider(), [
        'routing.cache_adapter' => new \Symfony\Component\Cache\Adapter\FilesystemAdapter('', 0, __DIR__ . '/../var/cache'),
        'routing.controller_dir' => __DIR__ . '/App/Controller',
    ]);

$app['manufacturer.controller'] = function() {
    return new \App\Controller\ManufacturerController();
};

$app->get('/hoy', 'manufacturer.controller:indexAction');
//$app->post('/manufacturers', 'manufacturer.controller:createAction');
//$app->get('/manufacturers/{id}', 'manufacturer.controller:readAction');
//$app->match('/manufacturers/{id}', 'manufacturer.controller:updateAction')->method('PUT|PATCH');
//$app->delete('/manufacturers/{id}', 'manufacturer.controller:deleteAction');

return $app;
