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
   $auth = new AuthController($logger);
   $user = new UserController($logger);
	
   $app->add(new ApiMiddleware($logger));
   $app->get('/auth/refresh',  [$auth, 'refreshToken']);
   
   $app->group('', function (RouteCollectorProxy $group) use ($auth) {
      $group->post('/auth/login', [$auth, 'login']);
      $group->post('/auth/register', [$auth, 'register']);   
      $group->post('/auth/guest', [$auth, 'guest']);   
   })->add(new DeviceMiddleware($logger));

   $app->group('', function (RouteCollectorProxy $group) use ($auth, $user) {
      $group->get('/auth/logout', [$auth, 'logout']);
      $group->get('/user/profile', [$user, 'profile']);
   })->add(new DeviceMiddleware($logger))->add(new AuthMiddleware($logger));

};
