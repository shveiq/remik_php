<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Controllers\AuthController;

use App\Middleware\ApiMiddleware;
use App\Middleware\DeviceMiddleware;
use App\Middleware\AuthMiddleware;

return function(App $app) {
	
   $app->add(new ApiMiddleware());

   $app->get('/auth/refresh', '\AuthController::refreshToken');
   
   $app->group('', function (RouteCollectorProxy $group) {
      $group->post('/auth/login', '\AuthController:login');

      $auth = new AuthController();
      $group->post('/auth/register', [$auth, 'register']);   
   })->add(new DeviceMiddleware);

   $app->group('', function (RouteCollectorProxy $group) {
      $auth = new AuthController();
      $group->get('/auth/logout', [$auth, 'logout'])->add(new AuthMiddleware());
   });
   
};
