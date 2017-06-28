<?php

set_time_limit(0);

use Bezhanov\Faker\ProviderCollectionHelper;
use Bezhanov\Silex\AliceDataFixtures\FixturesServiceProvider;
use Doctrine\DBAL\Tools\Console\ConsoleRunner as DoctrineDBAL;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineORM;
use Faker\Factory;
use Kurl\Silex\Provider\DoctrineMigrationsProvider;
use Nelmio\Alice\Faker\Provider\AliceProvider;
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

$faker = Factory::create();
$faker->addProvider(new AliceProvider($faker));
ProviderCollectionHelper::addAllProvidersTo($faker);

$app->register(new FixturesServiceProvider($console), [
    'fixtures.faker_generator' => $faker,
]);

$app->boot();

$helperSet = $console->getHelperSet();
$helperSet->set($helperSet->get('connection'), 'db');

DoctrineDBAL::addCommands($console);
DoctrineORM::addCommands($console);

$console->run();
