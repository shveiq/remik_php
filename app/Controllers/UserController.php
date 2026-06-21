<?php

namespace App\Controllers;

use Psr\Log\LoggerInterface;

class UserController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function profile($request, $response)
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $response->getBody()->write(json_encode([
            "nickname" => $user->nickname,
            "coins" => $user->coins_amount,
            "diamonds" => $user->diamonds_amount,
            "level" => $user->level_id,
            //"league" => $user->league->name,
            //"league_logo" => $user->league->type->logo,
            "lp" => $user->level_points,
            "lgp" => $user->league_points
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}