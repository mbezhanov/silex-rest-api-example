<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use App\Application;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$cache = new FilesystemCache(__DIR__ . '/../var/cache');

$app = new Application([
    'cache' => function () use ($cache) {
        return $cache;
    },
]);

$app['cors-enabled']($app);

$app->before(function(Request $request, Application $app) {
    if (in_array($request->getPathInfo(), ['/login', '/login/renew']) || $request->isMethod(Request::METHOD_OPTIONS)) {
        // do not require authorization on "/login" page and for OPTIONS requests
        return;
    }
    if (!$request->headers->get('Authorization')) {
        throw new HttpException(Response::HTTP_UNAUTHORIZED);
    } else {
        $jwtService = $app['jwt.service'];
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        if (!$jwtService->validateToken($token)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }
    }
});

$app->error(function (\App\Exception\ApiProblemException $e, Request $request, $code) {
    return new \Symfony\Component\HttpFoundation\JsonResponse($e->toArray(), $e->getStatusCode(), [
        'Content-Type' => 'application/problem+json'
    ]);
});

return $app;
