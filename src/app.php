<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use App\Application;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$cache = new FilesystemCache(__DIR__ . '/../var/cache');

$app = new Application($cache);

$app['debug'] = true;

$app->after(function(Request $request, Response $response) {
    $apiClientUrl = 'http://localhost:8080';
    $response->headers->set('Access-Control-Allow-Origin', $apiClientUrl);
});

return $app;
