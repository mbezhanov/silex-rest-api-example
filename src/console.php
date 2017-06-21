<?php

require_once __DIR__.'/../vendor/autoload.php';

set_time_limit(0);

use Symfony\Component\Console\Application;

$app = require __DIR__.'/../src/app.php';

$console = new Application('REST API', '0.1.0');

$console->setDispatcher($app['dispatcher']);

$console->run();
