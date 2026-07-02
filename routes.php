<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Log\LoggerInterface;

use App\Controllers\AuthController;
//use App\Controllers\TechnicalController;
use App\Controllers\TableController;
use App\Controllers\GameController;
use App\Controllers\UserController;

use App\Middleware\DeviceMiddleware;
use App\Middleware\AuthMiddleware;

return function(App $app, LoggerInterface $logger) {
   $auth = new AuthController($logger);
   $user = new UserController($logger);
   $table = new TableController($logger);
   $game = new GameController($logger);
   //$technical = new TechnicalController($logger);
	
//   $app->get('/technical/leagues', [$technical, 'leagues']);
//   $app->get('/technical/users', [$technical, 'users']);
   $app->get('/auth/refresh',  [$auth, 'refreshToken']);
   
   $app->group('', function (RouteCollectorProxy $group) use ($auth) {
      $group->post('/auth/login', [$auth, 'login']);
      $group->post('/auth/register', [$auth, 'register']);   
      $group->post('/auth/guest', [$auth, 'guest']);   
   })
      ->add(new DeviceMiddleware($logger));

   $app->group('', function (RouteCollectorProxy $group) use ($auth, $user, $table, $game) {
      $group->get('/auth/logout', [$auth, 'logout']);
      $group->get('/user/profile', [$user, 'profile']);
      $group->get('/user/league', [$user, 'league']);
      $group->get('/user/give_me_bonus', [$user, 'getBonus']);
      $group->get('/tables', [$table, 'getAll']);
      $group->put('/game', [$game, 'initGame']);
      $group->get('/game', [$game, 'statusGame']);
      $group->post('/game', [$game, 'startGame']);
      $group->get('/game/players', [$game, 'getPlayers']);
      $group->post('/game/shuffle', [$game, 'shuffleDeck']);
      $group->put('/game/next', [$game, 'nextPlayer']);
      $group->get('/game/summary', [$game, 'summaryGame']);
   })
      ->add(new AuthMiddleware($logger))
      ->add(new DeviceMiddleware($logger));

};
