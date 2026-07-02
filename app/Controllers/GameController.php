<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\User;

use Psr\Log\LoggerInterface;

class GameController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function initGame($request, $response)
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['table_id']) || !is_numeric($data['table_id'])) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $game = Game::create([
            'user_id' => $user->id,
            'table_id' => $data['table_id'],
            'max_players' => 4,
            'status' => 'INIT',
        ]);

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $game->id, 
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function nextPlayer($request, $response) 
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

    public function getPlayers($request, $response) 
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

    public function shuffleDeck($request, $response) 
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

    public function statusGame($request, $response)
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['game_id']) || !is_numeric($data['game_id'])) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $game = Game::find($data['game_id']);
        if (!$game) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $mPlayers = $game->players()->get();
        $players = [];
        foreach ($mPlayers as $player) {
            $players[] = [
                "id" => $player->id,
                "nickname" => $player->nickname,
                "avatar_id" => $player->avatar_id,
                "avatar_url" => $player->avatar_url,
            ];
        }

        $mPlayerCards = $game->playerCards()->get();
        foreach ($mPlayerCards as $playerCard) {
            foreach ($players as &$player) {
                if ($playerCard->user_id == $player['id']) {
                    $player['cards'] = $playerCard->cards;
                    break;
                }
            }
        }

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "current_player_id" => $game->current_player_id,
            "players" => $players,
            "cards" => $game->cards,
            "draws" => $game->draws,
            "melds" => $game->melds,
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function summaryGame($request, $response) 
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
}