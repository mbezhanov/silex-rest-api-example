<?php

namespace App;

use App\Controller\DiaryController;
use App\Controller\FoodController;
use App\Controller\ManufacturerController;
use App\Controller\ProfileController;
use App\ServiceProvider\SerializerServiceProvider;
use Bezhanov\Silex\Routing\RouteAnnotationsProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FileCache;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class Application extends \Silex\Application
{
    public function __construct(FileCache $cache, array $values = [])
    {
        parent::__construct($values);

        $this['cache'] = function () use ($cache) {
            return $cache;
        };
        $this->registerServiceProviders();
        $this->registerControllers();
    }

    private function registerServiceProviders()
    {
        $app = $this;

        $app->register(new DoctrineServiceProvider(), [
                'db.options' => [
                    'driver' => 'pdo_sqlite',
                    'path' => __DIR__ . '/../../db/sqlite.db',
                ],
            ])
            ->register(new DoctrineOrmServiceProvider(), [
                'orm.cache.instances.default.query' => $app['cache'],
                'orm.cache.instances.default.result' => $app['cache'],
                'orm.cache.instances.default.metadata' => $app['cache'],
                'orm.cache.instances.default.hydration' => $app['cache'],
                'orm.proxies_dir' => $app['cache']->getDirectory() . '/proxy',
                'orm.auto_generate_proxies' => false,
                'orm.em.options' => [
                    'mappings' => [
                        [
                            'type' => 'annotation',
                            'namespace' => 'App\Entity',
                            'path' => __DIR__ . '/Entity',
                            'use_simple_annotation_reader' => false,
                        ],
                    ],
                ],
            ])
            ->register(new ServiceControllerServiceProvider())
            ->register(new ValidatorServiceProvider(), [
                'validator.mapping.class_metadata_factory' => function ($app) {
                    $loader = new AnnotationLoader(new AnnotationReader());
                    $cacheDriver = new DoctrineCache($app['cache']);
                    return new LazyLoadingMetadataFactory($loader, $cacheDriver);
                },
            ])
            ->register(new RouteAnnotationsProvider(), [
                'routing.cache_adapter' => function ($app) {
                    return new DoctrineAdapter($app['cache']);
                },
                'routing.controller_dir' => __DIR__ . '/Controller',
            ])
            ->register(new SerializerServiceProvider(), [
                'serializer.cache_dir' => $app['cache']->getDirectory()
            ]);
    }

    private function registerControllers()
    {
        $app = $this;

        $resourceControllers = [
            'app.controller.diary_controller' => DiaryController::class,
            'app.controller.food_controller' => FoodController::class,
            'app.controller.manufacturer_controller' => ManufacturerController::class,
            'app.controller.profile_controller' => ProfileController::class,
        ];

        foreach ($resourceControllers as $serviceId => $className) {
            $app[$serviceId] = function ($app) use ($className) {
                return new $className($app['orm.em'], $app['serializer'], $app['validator']);
            };
        }
    }
}