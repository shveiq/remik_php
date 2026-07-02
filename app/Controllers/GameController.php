<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\User;

use Psr\Log\LoggerInterface;
use Utils\PlayingCard;

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
            $this->logger->error("invalid request - invalid user");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['table_id']) || !is_numeric($data['table_id'])) {
            $this->logger->error("invalid request - without table_id");
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
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $this->logger->error("invalid request - invalid user");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }
        
        $data = $request->getAttribute('params') or [];
        if (!isset($data['game_id']) || !is_numeric($data['game_id'])) {
            $this->logger->error("invalid request - without game_id");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $game = Game::find($data['game_id']);
        if (!$game) {
            $this->logger->error("invalid request - game_id not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        if ($game->status != "START") {
            $this->logger->error("invalid request - game invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        // TODO Tu jakas logika wybierania zawodnikow
        $allAcceptPlayers = User::all();
        $allAcceptPlayers = shuffle($allAcceptPlayers);

        for ($i=0; $i<$game->max_players; $i++) {
            $first = array_shift($allAcceptPlayers);
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

    public function shuffleDeck($request, $response) 
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $this->logger->error("invalid request - invalid user");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }
        
        $data = $request->getAttribute('params') or [];
        if (!isset($data['game_id']) || !is_numeric($data['game_id'])) {
            $this->logger->error("invalid request - without game_id");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $game = Game::find($data['game_id']);
        if (!$game) {
            $this->logger->error("invalid request - game_id not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        if ($game->status != "START") {
            $this->logger->error("invalid request - game invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $cards = PlayingCard::generateDeck();
        $cards = shuffle($cards);

        $mPlayers = $game->playerCards()->get();
        $max_players = count($mPlayers);

        $players = array();
        for($j=0; $j<$max_players; $j++) {
            $players[] = [];
        }
        for ($i=0; $i<13; $i++) {
            for ($j=0; $j<$max_players; $j++) {
                $first = array_shift($cards);
                $players[$j][] = $first;
            }
        }

        $i=0;
        foreach ($mPlayers as $playerCard) {
            $playerCard->cards = $players[$i];
            $playerCard->save();
            $i++;            
        }

        $game->cards = $cards;
        $game->status = "SHUFFLED";
        $game->save();

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $game->id, 
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function statusGame($request, $response)
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $this->logger->error("invalid request - invalid user");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['game_id']) || !is_numeric($data['game_id'])) {
            $this->logger->error("invalid request - without game_id");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $game = Game::find($data['game_id']);
        if (!$game) {
            $this->logger->error("invalid request - game_id not in DB");
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