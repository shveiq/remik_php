<?php

use Slim\App;
use App\Controllers\TestController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

return function(App $app) {
	
   $auth = new AuthController();
   
   $app->get('/auth/login', [$auth, 'login']);
   $app->get('/auth/logout', [$auth, 'logout'])->add(new AuthMiddleware());

   $test = new TestController();

   $app->get('/test/info', [$test, 'info']);

};
