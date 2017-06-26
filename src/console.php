<?php

set_time_limit(0);

use Bezhanov\Silex\AliceDataFixtures\FixturesServiceProvider;
use Doctrine\DBAL\Tools\Console\ConsoleRunner as DoctrineDBAL;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineORM;
use Kurl\Silex\Provider\DoctrineMigrationsProvider;
use Symfony\Component\Console\Application;

$app = require __DIR__.'/../src/app.php';

$console = new Application('REST API', '0.1.0');
$console->setDispatcher($app['dispatcher']);

$app->register(
    new DoctrineMigrationsProvider($console),
    [
        'migrations.directory' => __DIR__ . '/../migrations',
        'migrations.namespace' => 'App\Migrations',
    ]
);

$app->register(new FixturesServiceProvider($console));

$app->boot();

$helperSet = $console->getHelperSet();
$helperSet->set($helperSet->get('connection'), 'db');

DoctrineDBAL::addCommands($console);
DoctrineORM::addCommands($console);

$console->run();
