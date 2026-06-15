<?php

declare(strict_types=1);

//composer autoload
require __DIR__ .'/../vendor/autoload.php';

//bootstrap
require __DIR__ .'/../bootstrap.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

//load routes
(require __DIR__ .'/../routes.php')($app);

$app->rum();
