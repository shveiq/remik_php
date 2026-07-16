<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\WaitingUser;
use App\Models\GameUser;
use App\Models\User;

use Psr\Log\LoggerInterface;
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

}