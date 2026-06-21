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

        $avatar = array();
        if (isset($user->avatar_id) && strlen($user->avatar_id) > 0) {
            $avatar = array("id" => $user->avatar_id);
        } else if (isset($user->avatar_url) && strlen($user->avatar_url) > 0) {
            $avatar = array("url" => $user->avatar_url);
        }

        $response->getBody()->write(json_encode([
            "current_time" => date("Y-m-d h:i:s", time()),
            "nickname" => $user->nickname,
            "avatar" => $avatar,
            "coins" => $user->coins_amount,
            "diamonds" => $user->diamonds_amount,
            "level" => $user->level_id,
            "league" => $user->league->name,
            "league_percentage" => $user->league->type->percentage,
            "league_logo" => $user->league->type->logo,
            "league_start_date" => $user->league->start_date,
            "league_end_date" => $user->league->end_date,
            "lp" => $user->level_points,
            "lgp" => $user->league_points
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function league($request, $response)
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $league = $user->league;

        $players = array();
        foreach ($league->users as $user) {
            $avatar = array();
            if (isset($user->avatar_id) && strlen($user->avatar_id) > 0) {
                $avatar = array("id" => $user->avatar_id);
            } else if (isset($user->avatar_url) && strlen($user->avatar_url) > 0) {
                $avatar = array("url" => $user->avatar_url);
            }

            $players[] = array(
                "nickname" => $user->nickname,
                "avatar" => $avatar,
                "lgp" => $user->league_points
            );
        }

        $response->getBody()->write(json_encode([
            "name" => $league->name, 
            "logo" => $league->type->logo,
            "promoted" => $league->type->count_promoted,
            "dropouts" => $league->type->count_dropouts,
            "start_date" => $league->start_date, 
            "end_date" => $league->end_date, 
            "players" => $players
        ]));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}