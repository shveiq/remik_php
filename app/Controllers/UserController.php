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

        if ($user->isBot) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);  
        }

        $avatar = array();
        if (isset($user->avatar_id) && strlen($user->avatar_id) > 0) {
            $avatar = array("id" => $user->avatar_id);
        } else if (isset($user->avatar_url) && strlen($user->avatar_url) > 0) {
            $avatar = array("url" => $user->avatar_url);
        }

        $emailEncoded = null;
        if (!$user->isGuest) {
            $session = $request->getAttribute('session') or null;
            if (!$session) {
                $session_data = $session->decode_session_data();
                if ($session_data === null) { 
                    $hmacKeyHex = $session_data['hmac'];
                    if (!$hmacKeyHex) {
                        $this->logger->error('Invalid Request - no hmac in session');
                        $response->getBody()->write(json_encode([ 'error' => 'security_violation' ]));
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(401); 
                    }
                    $hmacKey = hex2bin($hmacKeyHex);
                    $iv = random_bytes(openssl_cipher_iv_length('aes-256-gcm'));
                    $ciphertext = openssl_encrypt(
                        $user->email,
                        'aes-256-gcm',
                        $hmacKey,
                        OPENSSL_RAW_DATA,
                        $iv,
                        $tag
                    );
                    $emailEncoded = base64_encode($iv . $tag . $ciphertext);
                }
            }
        }

        $response->getBody()->write(json_encode([
            "current_time" => date("Y-m-d h:i:s", time()),
            "nickname" => $user->nickname,
            "avatar" => $avatar,
            "email" => $emailEncoded,
            "coins" => $user->coins_amount,
            "diamonds" => $user->diamonds_amount,
            "level" => $user->level_id,
            "league" => $user->league->name,
            "league_logo" => $user->league->type->logo,
            "lp" => $user->level_points,
            "max_lp" => $user->level->amount,
            "lgp" => $user->league_points,
            "last_free_coins_date" => $user->last_free_coins_date,
            "last_wheels_date" => $user->last_wheels_date
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
            "percentage" => $league->type->percentage,
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

    public function getBonus($request, $response) 
    {
        $user = $request->getAttribute('user') or null;   
        if (!$user) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['bonus_type'])) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }
        
        if ($data['bonus_type'] == 'coins') {
            $percentage = (100 + $user->league->type->percentage) / 100;
            $user->coins_amount += 200 * $percentage;
            $user->last_free_coins_date = date('Y-m-d H:i:s', time());
            $user->save();
        } else if ($data['bonus_type'] == 'diamonds') {
            $user->diamonds_amount += 10;
            $user->save();
        } else if (($data['bonus_type'] == 'wheeleOfFortune') && isset($data['bonus_value'])) {
            $bonus_value = intval($data['bonus_value']);
            if ($bonus_value > 0) {
                $user->coins_amount += $bonus_value;
                $user->last_wheels_date = date('Y-m-d H:i:s', time());
                $user->save();
            } else {
                $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);  
            }
        } else {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $response->getBody()->write(json_encode([
            "status" => "ok"
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);  
    }
}