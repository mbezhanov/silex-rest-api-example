<?php

namespace App\ServiceProvider;

use App\Service\JwtService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
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

        $app['jwt.parser'] = function () {
            return new Parser();
        };

        $app['jwt.signer'] = function () {
            return new Sha256();
        };

        $app['jwt.service'] = function ($app) {
            return new JwtService($app['jwt.builder'], $app['jwt.parser'], $app['jwt.signer'], $app['api_token_sign_key']);
        };
    }
}
