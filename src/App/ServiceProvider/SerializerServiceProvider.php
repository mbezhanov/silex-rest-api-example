<?php

namespace App\ServiceProvider;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SerializerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['serializer'] = function ($app) {
            return HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($app['url_generator']))
                ->setCacheDir($app['serializer.cache_dir'])
                ->build();
        };
    }
}
