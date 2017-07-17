<?php

namespace App\ServiceProvider;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class JwtServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['jwt.builder'] = function ($app) {
            return (new Builder())->setIssuer($app['api_url'])
                ->setAudience($app['api_client_url']);
        };

        $app['jwt.parser'] = function ($app) {
            return new Parser();
        };
    }
}
