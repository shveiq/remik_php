<?php

declare(strict_types=1);

//composer autoload
require __DIR__ .'/../vendor/autoload.php';

//bootstrap
require __DIR__ .'/../bootstrap.php';

use Slim\Factory\AppFactory;
use App\Middleware\ApiMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add(new ApiMiddleware());

//load routes
(require __DIR__ .'/../routes.php')($app);

$app->run();
