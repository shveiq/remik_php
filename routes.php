<?php

use Slim\App;
use App\Controllers\AuthController;

return function(App $app) {
	
   $auth = new AuthController();
   
   $app->post('/auth/login', [$auth, 'login']);
   $app->post('/auth/logout', [$auth, 'logout']);

};
