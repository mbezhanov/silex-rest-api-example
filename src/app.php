<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use Bezhanov\Silex\Routing\RouteAnnotationsProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;

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
    ->register(new ValidatorServiceProvider(), [
        'validator.mapping.class_metadata_factory' => function ($app) use ($cacheDriver) {
            $loader = new AnnotationLoader(new AnnotationReader());
            return new LazyLoadingMetadataFactory($loader /*, $cacheDriver */);
        },
    ])
    ->register(new RouteAnnotationsProvider(), [
//        'routing.cache_adapter' => new \Symfony\Component\Cache\Adapter\FilesystemAdapter('', 0, __DIR__ . '/../var/cache'),
        'routing.controller_dir' => __DIR__ . '/App/Controller',
    ]);

$app['serializer'] = function() {
    return \Hateoas\HateoasBuilder::create()->build();
};

$app['app.controller.manufacturer_controller'] = function ($app) {
    return new App\Controller\ManufacturerController($app['orm.em'], $app['serializer'], $app['validator']);
};

$app['debug'] = true;

return $app;
