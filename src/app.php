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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

const APP_CACHE_DIR = __DIR__ . '/../var/cache';

$app = new Application();

$app['cache'] = function () {
    return new \Doctrine\Common\Cache\FilesystemCache(APP_CACHE_DIR);
};

$app->register(new DoctrineServiceProvider, [
        'db.options' => [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../db/sqlite.db',
        ],
    ])
    ->register(new DoctrineOrmServiceProvider, [
        'orm.cache.instances.default.query' => $app['cache'],
        'orm.cache.instances.default.result' => $app['cache'],
        'orm.cache.instances.default.metadata' => $app['cache'],
        'orm.cache.instances.default.hydration' => $app['cache'],
        'orm.proxies_dir' => APP_CACHE_DIR . '/proxy',
        'orm.auto_generate_proxies' => false,
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
        'validator.mapping.class_metadata_factory' => function ($app) {
            $loader = new AnnotationLoader(new AnnotationReader());
            $cacheDriver = new \Symfony\Component\Validator\Mapping\Cache\DoctrineCache($app['cache']);
            return new LazyLoadingMetadataFactory($loader, $cacheDriver);
        },
    ])
    ->register(new RouteAnnotationsProvider(), [
        'routing.cache_adapter' => function ($app) {
            return new \Symfony\Component\Cache\Adapter\DoctrineAdapter($app['cache']);
        },
        'routing.controller_dir' => __DIR__ . '/App/Controller',
    ]);

$app['serializer'] = function ($app) {
    return \Hateoas\HateoasBuilder::create()
        ->setUrlGenerator(null, new \Hateoas\UrlGenerator\SymfonyUrlGenerator($app['url_generator']))
        ->setCacheDir(APP_CACHE_DIR)
        ->build();
};

// register controllers
// @todo: extract this logic somewhere else

$resourceControllers = [
    'app.controller.diary_controller' => App\Controller\DiaryController::class,
    'app.controller.food_controller' => App\Controller\FoodController::class,
    'app.controller.manufacturer_controller' => App\Controller\ManufacturerController::class,
    'app.controller.profile_controller' => App\Controller\ProfileController::class,
];

foreach ($resourceControllers as $serviceId => $className) {
    $app[$serviceId] = function ($app) use ($className) {
        return new $className($app['orm.em'], $app['serializer'], $app['validator']);
    };
}

$app['debug'] = true;

$app->after(function(Request $request, Response $response) {
    $apiClientUrl = 'http://localhost:8080';
    $response->headers->set('Access-Control-Allow-Origin', $apiClientUrl);
});

return $app;
