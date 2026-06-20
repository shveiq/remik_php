<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Log\LoggerInterface;

use App\Controllers\AuthController;
use App\Controllers\UserController;

use App\Middleware\ApiMiddleware;
use App\Middleware\DeviceMiddleware;
use App\Middleware\AuthMiddleware;

return function(App $app, LoggerInterface $logger) {
	
   $app->add(new ApiMiddleware($logger));
   $app->get('/auth/refresh',  AuthController::class . ':refreshToken');
   
   $app->group('', function (RouteCollectorProxy $group) {
      $group->post('/auth/login', AuthController::class . ':login');
      $group->post('/auth/register', AuthController::class .':register');   
   })->add(new DeviceMiddleware($logger));

   $app->group('', function (RouteCollectorProxy $group) {
      $group->get('/auth/logout', AuthController::class . ':logout');
      $group->get('/user/profile', UserController::class . ':logout');
   })->add(new DeviceMiddleware($logger))->add(new AuthMiddleware($logger));

};
