<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use App\Application;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$cache = new FilesystemCache(__DIR__ . '/../var/cache');

$app = new Application([
    'cache' => function () use ($cache) {
        return $cache;
    },
]);

$app['cors-enabled']($app);

$app->before(function(Request $request) {
    if (!$request->headers->get('Authorization') && $request->getPathInfo() !== '/login' && !$request->isMethod(Request::METHOD_OPTIONS)) {
        throw new \Symfony\Component\HttpKernel\Exception\HttpException(Response::HTTP_FORBIDDEN);
    }
});

return $app;
