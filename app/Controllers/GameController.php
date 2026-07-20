<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\RemikTable;
use App\Models\WaitingUser;

use DateTime;
use Psr\Log\LoggerInterface;
use Utils\ArrayUtils;
use Utils\CardUtils;

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

        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $this->logger->error("invalid request - invalid user");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        /* 
        możliwe zdarzenia 1 z 4 lub wszystkie:

        - undraw null/true
        - draw null/PlayingCard
        - card null/true
        - meld list<PlayingCard>
        - addToMeld int, List<PlayingCard>
        
        */
        $foundElement = false;
        if (isset($data["undraw"])) {
            if (!is_bool($data["undraw"])) {
                $this->logger->error("invalid request - undraw is not bool");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            } else {
                $foundElement = true;
            }
        }
        if (isset($data["draw"])) {
            if (!is_string($data["draw"])) {
                $this->logger->error("invalid request - draw is string");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            } else {
                $foundElement = true;
            }
        }
        if (isset($data["card"])) {
            if (!is_bool($data["card"])) {
                $this->logger->error("invalid request - card is not bool");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            } else {
                $foundElement = true;
            }
        }
        if (isset($data["undraw"]) && isset($data["card"])) {
            $this->logger->error("invalid request - undraw and card cannot be in the same time");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }
        if (isset($data["meld"])) {
            if (!is_array($data["meld"])) {
                $this->logger->error("invalid request - meld is not array");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            } else {
                $is_correct = true;
                foreach ($data["meld"] as $meld) {
                    if (!CardUtils::isValidMeld($meld)) {
                        $is_correct = false;
                        break;
                    }
                }
                if (!$is_correct) {
                    $this->logger->error("invalid request - set of melds is not valid");
                    $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);  
                } else {
                    $foundElement = true;
                }
            }
        }
        if (isset($data["add_to_meld"])) {
            if (!is_array($data["add_to_meld"])) {
                $this->logger->error("invalid request - add_to_meld is not hash");
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            } else {
                // TODO ADD VALIDATION FOR ADD_TO_MELD
                $foundElement = true;
            }
        }
        if ($foundElement == false) {
            $this->logger->error("invalid request - none required elements in request");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }        
        
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

        if ($game->status != 'LOOP') {
            $this->logger->error("game is in invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_state" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        if ($game->current_player_id != $user->id) {
            $this->logger->error("other player is active now");
            $response->getBody()->write(json_encode([ "error" => "invalid_player" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        $nextCurrentPlayerId = -1;

        $mPlayers = $game->players()->get();
        $currentIndex = -1;
        $players = [];
        $pIndex = 0;
        foreach ($mPlayers as $player) {    
            $players[] = $player->id;
            if ($player->id == $game->current_player_id) {
                $currentIndex = $pIndex;
            }
            $pIndex++;
        }
        if ($currentIndex == count($players) - 1) {
            $nextCurrentPlayerId = $players[0];
        } else {
            $nextCurrentPlayerId = $players[$currentIndex + 1];
        }

        $playerCards = GameUser::where('game_id', $game->id)->where('user_id', $game->current_player_id)->first();
        if (!$playerCards) {
            $this->logger->error("invalid request - player cards not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_player" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        $response_data = array(
            "current_time" => date('Y-m-d H:i:s') 
        );

        if ($game->next_player_time != null) {
            //weryfikacja czy jest odpowiedni czas
            $nextPlayerTime = new DateTime($game->next_player_time);
            $nowTime = new DateTime();

            if ($nextPlayerTime < $nowTime) {

                //Wykonaj ruch automatycznie
                // Pobierz karte
                // Wyrzuc dowolna losowa
                $cards = json_decode($game->cards, true); 
                $draws = json_decode($game->draws, true);
                $player_cards = json_decode($playerCards->cards, true);

                $newCard = array_shift($cards);

                $player_cards[] = $newCard;
                $randIndex = array_rand($player_cards);
                $drawCard = $player_cards[$randIndex];

                $player_cards = ArrayUtils::array_remove_index($player_cards, $randIndex);

                $draws[] = $drawCard;

                $game->cards = json_encode($cards);
                $game->draws = json_encode($draws);

                $playerCards->cards = json_encode($player_cards);
                $playerCards->save();

                // Koniec ruchu
                $game->current_player_id = $nextCurrentPlayerId;
                $nextTimeEnd = new Datetime();
                $nextTimeEnd->modify('+40 seconds');
                $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");
                $game->save();

                $response_data["card"] = $drawCard;
                $response_data["timeout"] = "Player dont move in time";
                $response_data["next_player"] = $nextCurrentPlayerId;
                $response_data["next_time_player"] = $game->next_player_time;

                $response->getBody()->write(json_encode($response_data));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);  
            }
        }

        if (isset($data["undraw"]) && $data["undraw"] == true) {
            $cards = json_decode($game->draws, true); 
            $card = array_shift($cards);
            $game->draws = json_encode($cards);
            $response_data["undraw"] = $card;

            $player_cards = json_decode($playerCards->cards, true);
            $player_cards[] = $card;
            $playerCards->cards = json_encode($player_cards);
            $playerCards->save();
        }

        if (isset($data["draw"]) && strlen($data["draw"])>0) {
            $cards = json_decode($game->draws, true); 
            $cards[] = $data["draw"];
            $game->draws = json_encode($cards);

            $player_cards = json_decode($playerCards->cards, true);

            $cardIndex = array_search($data["draw"], $player_cards, true);
            if ($cardIndex !== false) {
                $player_cards = ArrayUtils::array_remove_index($player_cards, $cardIndex);
            }

            $playerCards->cards = json_encode($player_cards);
            $playerCards->save();

            // Koniec ruchu
            $game->current_player_id = $nextCurrentPlayerId;
            $nextTimeEnd = new Datetime();
            $nextTimeEnd->modify('+40 seconds');
            $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");
            $game->save();

            $response_data["timeout"] = "Player moved";
            $response_data["next_player"] = $nextCurrentPlayerId;
            $response_data["next_time_player"] = $game->next_player_time;
        }

        if (isset($data["card"]) && $data["card"] == true) {
            $cards = json_decode($game->cards, true); 
            $card = array_shift($cards);
            $game->cards = json_encode($cards);
            $response_data["card"] = $card;

            $player_cards = json_decode($playerCards->cards, true);
            $player_cards[] = $card;
            $playerCards->cards = json_encode($player_cards);
            $playerCards->save();
        }

        if (isset($data["meld"])) {
            $player_cards = json_decode($playerCards->cards, true);
            $melds = json_decode($game->melds, true);
            foreach($data["meld"] as $meld) {
                if (is_array($meld)) {
                    $found = true;
                    foreach ($meld as $card) {
                        $cardIndex = array_search($card, $player_cards, true);
                        if ($cardIndex !== false) {
                            $player_cards = ArrayUtils::array_remove_index($player_cards, $cardIndex);
                        } else {
                            $found = false;
                        }
                    }
                    if ($found) {
                        $melds[] = $meld;
                    }
                }
            }
            $game->melds = json_encode($melds);

            $playerCards->cards = json_encode($player_cards);
            $playerCards->save();
        }

        if (isset($data["add_to_meld"])) {
            //TODO ADD TO MEDL!

        }

        $game->save();

        $response->getBody()->write(json_encode($response_data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function sortCard($request, $response) 
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

        if ($game->status != "LOOP") {
            $this->logger->error("invalid request - game invalid state");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }
        
        $playerCards = GameUser::where('game_id', $game->id)->where('user_id', $user->id)->first();
        if (!$playerCards) {
            $this->logger->error("invalid request - player cards not in DB");
            $response->getBody()->write(json_encode([ "error" => "invalid_player" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        if (!isset($data['cards']) || !is_array($data['cards'])) {
            $this->logger->error("invalid request - without cards");
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        $cards = $data["cards"];
        $playerCards->cards = json_encode($cards);
        $playerCards->save();

        $response->getBody()->write(json_encode(array("status" => true)));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
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
                    $player['cards'] = json_decode($playerCard->cards, true);
                    break;
                }
            }
        }

        $response->getBody()->write(json_encode([
            "current_time" => date('Y-m-d H:i:s'),
            "id" => $game->id,
            "current_player_id" => $game->current_player_id,
            "next_player_time" => $game->next_player_time,
            "players" => $players,
            "cards" => json_decode($game->card, true),
            "draws" => json_decode($game->draws, true),
            "melds" => json_decode($game->melds, true),
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

            $cards = json_decode($game->cards, true);
            $draws = json_decode($game->draws, true);
            $first = array_shift($cards);
            $draws[] = $first;
            $game->cards = json_encode($cards);
            $game->draws = json_encode($draws);

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
            "id" => $game->id,
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