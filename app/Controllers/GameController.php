<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use App\Models\RemikTable;
use App\Models\WaitingUser;

use Psr\Log\LoggerInterface;
use Utils\PlayingCard;

class GameController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function joinToGame($request, $response) 
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

        $table = RemikTable::find($data['table_id']);
        if (!$table) {
            $this->logger->error("invalid request - table_id not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $waitingUser = new WaitingUser();
        $waitingUser->user_id = $user->id;
        $waitingUser->table_id = $table->id;
        $waitingUser->status = 'INIT';
        $waitingUser->save();
    
        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $waitingUser->id, 
            "status" => $waitingUser->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function checkJoinToGame($request, $response) 
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
        if (!isset($data['waiting_id']) || !is_numeric($data['waiting_id'])) {
            $this->logger->error("invalid request - without waiting_id");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $waitingUser = WaitingUser::find($data['waiting_id']);
        if (!$waitingUser) {
            $this->logger->error("invalid request - waiting_id not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        if ($waitingUser->status == 'ADDED') {
            $response->getBody()->write(json_encode([
                "current_time" => date('Y-m-d H:i:s'),
                "id" => $waitingUser->id, 
                "game_id" => $waitingUser->game_id,
                "status" => $waitingUser->status
            ]));
        } else {
            $response->getBody()->write(json_encode([
                "current_time" => date('Y-m-d H:i:s'),
                "id" => $waitingUser->id, 
                "status" => $waitingUser->status
            ]));
        }

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

    /*
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

        if ($game->status != "INIT") {
            $this->logger->error("invalid request - game invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        // TODO! Tu jakas logika wybierania zawodnikow
        $allAcceptPlayers = [];
        foreach (User::all() as $player) {
            $avatar = array();
            if (isset($player->avatar_id) && strlen($player->avatar_id) > 0) {
                $avatar = array("id" => $player->avatar_id);
            } else if (isset($player->avatar_url) && strlen($player->avatar_url) > 0) {
                $avatar = array("url" => $player->avatar_url);
            }

            $allAcceptPlayers[] = [
                "id" => $player->id,
                "nickname" => $player->nickname,
                "avatar" => $avatar
            ];
        }
        shuffle($allAcceptPlayers);

        $avatar = array();
        if (isset($user->avatar_id) && strlen($user->avatar_id) > 0) {
            $avatar = array("id" => $user->avatar_id);
        } else if (isset($user->avatar_url) && strlen($user->avatar_url) > 0) {
            $avatar = array("url" => $user->avatar_url);
        }
        $players = [
            [
                "id" => $user->id,
                "nickname" => $user->nickname,
                "avatar" => $avatar
            ]
        ];

        $gm = new GameUser();
        $gm->game_id = $game->id;
        $gm->user_id = $user->id;
        $gm->cards = "[]";
        $gm->save();

        for ($i=0; $i<$game->max_players-1; $i++) {
            $first = array_shift($allAcceptPlayers);
            $players[] = $first;

            $gm = new GameUser();
            $gm->game_id = $game->id;
            $gm->user_id = $first['id'];
            $gm->cards = "[]";
            $gm->save();
        }

        $game->status = "START";
        $game->save();

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $game->id, 
            "players" => $players,
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
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
        shuffle($cards);

        $mPlayers = $game->playerCards()->get();
        $max_players = count($mPlayers);

        $players = array();
        $jsonPlayers = array();
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
            $strCards = array();
            foreach ($players[$i] as $card) {
                $strCards[] = $card->toJSONString();
            };
            $playerCard->cards = json_encode($strCards);
            $playerCard->save();
            $i++;            

            $avatar = array();
            if (isset($playerCard->user->avatar_id) && strlen($playerCard->user->avatar_id) > 0) {
                $avatar = array("id" => $playerCard->user->avatar_id);
            } else if (isset($playerCard->user->avatar_url) && strlen($playerCard->user->avatar_url) > 0) {
                $avatar = array("url" => $playerCard->user->avatar_url);
            }
            $jsonPlayers[] = [
                "id" => $playerCard->user->id,
                "nickname" => $playerCard->user->nickname,
                "avatar" => $avatar,
                "cards" => $strCards
            ];
        }

        $strCards = array();
        foreach ($cards as $card) {
            $strCards[] = $card->toJSONString();
        }
        $game->cards = json_encode($strCards);
        $game->status = "SHUFFLED";
        $game->save();

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $game->id, 
            "players" => $jsonPlayers,  
            "cards" => $strCards,
            "draws" => array(),
            "melds" => array(),
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    } */

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
            $avatar = array();
            if (isset($player->avatar_id) && strlen($player->avatar_id) > 0) {
                $avatar = array("id" => $player->avatar_id);
            } else if (isset($player->avatar_url) && strlen($player->avatar_url) > 0) {
                $avatar = array("url" => $player->avatar_url);
            }

            $players[] = [
                "id" => $player->id,
                "nickname" => $player->nickname,
                "avatar" => $avatar
            ];
        }

        $mPlayerCards = $game->playerCards()->get();
        foreach ($mPlayerCards as $playerCard) {
            foreach ($players as &$player) {
                if ($playerCard->user_id == $player['id']) {
                    $player['cards'] = json_decode($playerCard->cards);
                    break;
                }
            }
        }

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "current_player_id" => $game->current_player_id,
            "players" => $players,
            "cards" => json_decode($game->cards),
            "draws" => json_decode($game->draws),
            "melds" => json_decode($game->melds),
            "status" => $game->status
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function startGame($request, $response) 
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

        if ($game->status == "SHUFFLED") {
            $number = random_int(0, $game->max_players-1);
            $mPlayers = $game->players()->get();
            
            if ($number < 0 || $number >= count($mPlayers)) {
                $this->logger->error("invalid request - random number invalid");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            }
            
            $mPlayer = $mPlayers[$number];
            $game->current_player_id = $mPlayer->id;
            $game->next_player_time = date("Y-m-d H:i:s", time() + 45);
            $game->status = "LOOP";
            $game->save();
        } else if ($game->status == "LOOP") {
            // OK nic nie rób
        } else {
            $this->logger->error("invalid request - game invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_state" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
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