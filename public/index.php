<?php

declare(strict_types=1);

//composer autoload
require __DIR__ .'/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use App\Middleware\RequestResponseLoggerMiddleware;
use App\Middleware\ApiMiddleware;

$logger = new Logger('app');
$logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../logs/app.log', 7));

//bootstrap
require __DIR__ .'/../bootstrap.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add(new RequestResponseLoggerMiddleware($logger));
$app->add(new ApiMiddleware($logger));

//load routes
(require __DIR__ .'/../routes.php')($app, $logger);

$app->run();
