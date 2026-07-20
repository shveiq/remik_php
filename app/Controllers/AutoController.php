<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\WaitingUser;
use App\Models\GameUser;
use App\Models\User;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Utils\ArrayUtils;
use Utils\CardUtils;
use Utils\PlayingCard;

class AutoController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function initGames($request, $response)
    {
        $waitingUsers = WaitingUser::where('status', 'INIT')->get();

        $waitingGroup = [];
        foreach ($waitingUsers as $wUser) {
            if (!array_key_exists($wUser->table_id."", $waitingGroup)) {
                $waitingGroup[$wUser->table_id.""] = array();
            }
            $waitingGroup[$wUser->table_id.""][] = $wUser;
        }

        $allBots = [];
        foreach (User::where('isBot', true)->get() as $player) {
            $allBots[] = [
                "id" => $player->id,
                "nickname" => $player->nickname,
                "isBot" => true,
            ];
        }
        shuffle($allBots);
    
        foreach ($waitingGroup as $table_id => $users) {
            
            $index = 0;
            while ($index < count($users)) {

                $game = new Game();
                $game->table_id = intval($table_id);
                $game->max_players = 4;
                $game->status = "INIT";
                $game->save();

                $players = [];
                if (count($users) > 4) {
                    $count_bots = random_int(0, 2);
                } else {
                    $count_bots = 4-count($users);
                }
                
                for ($i=0; $i <4 - $count_bots; $i++) {
                    if ($index < count($users)) {
                        $user = $users[$index]->user;
                        if ($user) {
                            $players[] = array(
                                "id" => $user->id,
                                "nickname" => $user->nickname,
                                "isBot" => false,
                            );
                            $index++;
                        } else {
                            $count_bots++;
                        }
                    } else {
                        $count_bots++;
                    }
                }

                for ($i=0; $i<$count_bots; $i++) {
                    $players[] = array_shift($allBots);
                }

                shuffle($players);

                foreach($players as $player) {
                    $gm = new GameUser();
                    $gm->game_id = $game->id;
                    $gm->user_id = $player["id"];
                    $gm->cards = "[]";
                    $gm->save();

                    foreach ($waitingUsers as $wUser) {
                        if ($wUser->user_id == $player["id"] && $wUser->table_id == $game->table_id) {
                            $wUser->status = "ADDED";
                            $wUser->game_id = $game->id;
                            $wUser->save();
                            break;
                        }
                    }

                    $game->status = "START";
                    $game->save();
                }

            }

        }
        
        $response->getBody()->write(json_encode([]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function shuffleGames($request, $response)
    {
        $games = Game::where("status", "START")->get();

        foreach($games as $game) {
            $cards = PlayingCard::generateDeck();
            shuffle($cards);

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
                $sortedCards = CardUtils::sortCards($players[$i]);
                $strCards = array();
                foreach ($sortedCards as $card) {
                    $strCards[] = $card->toJSONString();
                };
                $playerCard->cards = json_encode($strCards);
                $playerCard->save();
                $i++;            
            }

            $strCards = array();
            foreach ($cards as $card) {
                $strCards[] = $card->toJSONString();
            }
            $game->cards = json_encode($strCards);
            $game->status = "SHUFFLED";
            $game->save();
        }
                
        $response->getBody()->write(json_encode([]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function nextPlayerMoves($request, $response)
    {   
        $games = Game::where("status", "LOOP")->get();

        $response_data = array();

        foreach ($games as $game) {
	    $this->logger->info("GAMEID: ".$game->id);
            $currentPlayer = $game->currentPlayer;
            if (!$currentPlayer || $game->next_player_time == null) {
	        $this->logger->error("GAMEID: ".$game->id." Nie ma currentPlayer lub next_player_time");
                continue;
            }
            $nowTime = new DateTime();
            $nextPlayerTime = new DateTime($game->next_player_time);

            $mPlayers = $game->players()->get();
            if (!$mPlayers || count($mPlayers) < 3) {
	        $this->logger->error("GAMEID: ".$game->id." Nie ma mPlayers lub mPlayers < 3");
                continue;
            }
 
            $nextCurrentPlayerId = null;
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

            $currentPlayerCards = GameUser::where('game_id', $game->id)->where('user_id', $game->current_player_id)->first();
            if (!$currentPlayerCards) {
	        $this->logger->error("GAMEID: ".$game->id." Nie ma currentPlayerCards");
                continue;
            }
            $plCards = [];
            $cards = [];
            $draws = [];
            try {
                $plCards = json_decode($currentPlayerCards->cards, false);
                $cards = json_decode($game->cards, false);
                $draws = json_decode($game->draws, false);
            } catch (Exception $e) {
                $this->logger->error("GAMEID: ".$game->id.". Błąd parsowania: ".$e->getMessage());
            }

            $response_data["plCards"] = $plCards;
            $response_data["cards"] = $cards;
            $response_data["draws"] = $draws;

            $isChanged = false;
            if (count($cards) < 7 && count($draws)>5) {
                // jesli jest juz malo kart do dobrania, wez z odrzuconych najstarsze

                $response_data["malo_kart"] = true;

                $movedCard = array_slice($draws, 0, count($draws)-5);
                $oldDraws = array_slice($draws, count($draws) - 5);

                shuffle($movedCard);
                $draws = $oldDraws;

                foreach($movedCard as $card) {
                    $cards[] = $card;
                }
                $isChanged = true;

                $response_data["cards2"] = $cards;
                $response_data["draws2"] = $draws;
            }

            $this->logger->info("GAMEID: ".$game->id.". Zaczynam.");
            if ($currentPlayer->isBot == true) {
                $this->logger->info("GAMEID: ".$game->id.". Ruch bota.");
                if ($nextPlayerTime > $nowTime) {
                    //generuj ruch
	                $this->logger->info("GAMEID: ".$game->id.". Generuje ruch.");
                    if ($currentPlayerCards->botStatus == "") {
                        $betterNewCard = true; // TODO!!!
                        if ($betterNewCard) {
                            // wez karte    
                            $card = array_shift($cards);
                            $plCards[] = $card;
                            $game->cards = json_encode($cards);
                            $currentPlayerCards->cards = json_encode($plCards);
                            $currentPlayerCards->botStatus = "START";
                            $currentPlayerCards->save();
                            $isChanged = true;
                        } else {
                            // lub dobierz
                            $card = array_shift($draws);
                            $plCards[] = $card;
                            $game->cards = json_encode($cards);
                            $currentPlayerCards->cards = json_encode($plCards);
                            $currentPlayerCards->botStatus = "START";
                            $currentPlayerCards->save();
                            $isChanged = true;
                        }
                    } else if ($currentPlayerCards->botStatus == "START") {
                        $foundMeld = true; // TODO!
                        // moze meldunek
                        if ($foundMeld) {
                            // TODO!!!
                            $currentPlayerCards->botStatus = "MELD";
                            $currentPlayerCards->save();
                        } else {
                            $plIndex = random_int(0, count($plCards)-1); 
                            $newCard = $plCards[$plIndex];
                            $plCards = ArrayUtils::array_remove_index($plCards, $plIndex);
                            $currentPlayerCards->cards = json_encode($plCards);
                            $currentPlayerCards->botStatus = "";

                            $draws[] = $newCard;
                            $game->draws = json_encode($draws);

                            $game->current_player_id = $nextCurrentPlayerId;
                            $nextTimeEnd = new Datetime();
                            $nextTimeEnd->modify('+40 seconds');
                            $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");
                            $currentPlayerCards->save();
                            $isChanged = true;                            
                        }
                    } else if ($currentPlayerCards->botStatus == "MELD") {
                        $plIndex = random_int(0, count($plCards)-1); 
                        $newCard = $plCards[$plIndex];
                        $plCards = ArrayUtils::array_remove_index($plCards, $plIndex);
                        $currentPlayerCards->cards = json_encode($plCards);
                        $currentPlayerCards->botStatus = "";

                        $draws[] = $newCard;
                        $game->draws = json_encode($draws);

                        $game->current_player_id = $nextCurrentPlayerId;
                        $nextTimeEnd = new Datetime();
                        $nextTimeEnd->modify('+40 seconds');
                        $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");
                        $currentPlayerCards->save();
                        $isChanged = true;                            
                    }
                } else {
	                $this->logger->info("GAMEID: ".$game->id.". Robi ruch za bot'a minal czas");
                    // wylosuj dowolny ruch
                    $card = array_shift($cards);
                    $plCards[] = $card;
                    $game->cards = json_encode($cards);
 
                    $plIndex = random_int(0, count($plCards)-1); 
                    $newCard = $plCards[$plIndex];
                    $plCards = ArrayUtils::array_remove_index($plCards, $plIndex);
                    $currentPlayerCards->cards = json_encode($plCards);

                    $draws[] = $newCard;
                    $game->draws = json_encode($draws);

                    $game->current_player_id = $nextCurrentPlayerId;
                    $nextTimeEnd = new Datetime();
                    $nextTimeEnd->modify('+40 seconds');
                    $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");
 
                    $currentPlayerCards->save();
                    $isChanged = true;
                }
            } else {
               $this->logger->info("GAMEID: ".$game->id.". Ruch usera.");
                if ($nextPlayerTime > $nowTime) {
	                $this->logger->info("GAMEID: ".$game->id.". Czekamy... ".$nextPlayerTime->format("Y-m-d H:i:s")." ".$nowTime->format("Y-m-d H:i:s"));
                    continue;
                } else {
	                $this->logger->info("GAMEID: ".$game->id.". Robi ruch za user'a");
                    // wylosuj dowolny ruch
                    $card = array_shift($cards);
                    $plCards[] = $card;
                    $game->cards = json_encode($cards);
 
                    $plIndex = random_int(0, count($plCards)-1); 
                    $newCard = $plCards[$plIndex];
                    $plCards = ArrayUtils::array_remove_index($plCards, $plIndex);
                    $currentPlayerCards->cards = json_encode($plCards);

                    $draws[] = $newCard;
                    $game->draws = json_encode($draws);
 

                    $game->current_player_id = $nextCurrentPlayerId;
                    $nextTimeEnd = new Datetime();
                    $nextTimeEnd->modify('+40 seconds');
                    $game->next_player_time = $nextTimeEnd->format("Y-m-d H:i:s");

                    $currentPlayerCards->save();
                    $isChanged = true;
                }
            }
            if ($isChanged) {
                $game->save();
            }
        }

        $response->getBody()->write(json_encode($response_data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}