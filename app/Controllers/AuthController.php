<?php

namespace App\Controllers;

use App\Models\DeviceSession;
use App\Models\User;
use Utils\JwtAuth;
use Ramsey\Uuid\Uuid;

class AuthController
{
    /* =========================
     TOKEN
    ========================= */
    public function refreshToken($request, $response)
    {
        $device = $request->getAttribute('device') or null;
        $device_exists = $request->getAttribute('device_exists') or false;
        if ($device == null) {
            $response->getBody()->write(json_encode([ "error" => "invalid_request" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['session_id'])) {
            $session_id = (string) Uuid::uuid4();

            $session = new DeviceSession();
            $session->device_id = $device->id;
            $session->session_id = $session_id;
            $session->save_session_data(["uid" => null, "hmac" => JwtAuth::generateHMACKey()], false);
            $session->save();
        } else {
            $session_id = $$data['session_id'];

            $session = DeviceSession::where('session_id', $session_id)->first();
            if (!$session) {
                    $response->getBody()->write(json_encode([ "error" => "expired_session" ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);            
            } else {
                if ($session->expired_at && strtotime($session->expired_at) < time()) {
                    $response->getBody()->write(json_encode([ "error" => "expired_session" ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);
                } else {
                    $session_expired_at = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7);
                    $session->expired_at = $session_expired_at;
                    $session->save();
                }
            }
        }

        $session_data = $session->decode_session_data();
        $accessToken = JwtAuth::generate(["session_id" => $session_id, "uid" => $session_data['uid'] ?? null, "hmac" => $session_data['hmac'] ?? null, "exists" => $device_exists]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     LOGIN
    ========================= */
    public function login($request, $response)
    {
        $device = $request->getAttribute('device') or null;    
        $data = $request->getAttribute('params') or [];
        $session = $request->getAttribute('session') or null;

        if (!$device || !$session) {
            // Middleware should have already checked this, but just in case
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $token = $data['token'];
        try {
            $decoded_data = JwtAuth::decode($token);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        } 

        if (!$decoded_data || !isset($decoded_data['nick_or_email']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode([ "error" => "invalid_credentials" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $user = User::where('email', $decoded_data['nick_or_email'] ?? null)->first();
        $user = $user ?: User::where('nickname', $decoded_data['nick_or_email'] ?? null)->first();

        if (!$user || $user->isBot != false || !password_verify($data['password'] ?? '', $user->password)) {
            $response->getBody()->write(json_encode([ "error" => "invalid_credentials" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $session_data = $session->decode_session_data();
        $session_data['uid'] = $user->id;
        $session->save_session_data($session_data, true);

        $accessToken = JwtAuth::generate(["session_id" => $session->id, "uid" => $user->id, "exists" => true]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     LOGOUT
    ========================= */
    public function logout($request, $response)
    {
        $session = $request->getAttribute('session') or null;
        if (!$session) {
            $response->getBody()->write(json_encode([ "error" => "invalid_session" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $session->expired_at = time();
        $session->save();

        $response->getBody()->write(json_encode([ "message" => "logged_out" ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     REGISTER
    ========================= */
    public function register($request, $response)
    {
        $device = $request->getAttribute('device') or null;    
        $session = $request->getAttribute('session') or null;
        $data = $request->getAttribute('params') or [];
     
        if (!$device || !$session) {
            // Middleware should have already checked this, but just in case
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        if (!isset($data['nickname']) || !isset($data['email']) || !isset($data['password']) || !isset($data['birthday'])) {
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }

        // Check if nickname or email already exists
        if (User::where('nickname', $data['nickname'])->exists()) {
            $response->getBody()->write(json_encode([ "error" => "nickname_exists" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        } 
        if (User::where('email', $data['email'])->exists()) {
            $response->getBody()->write(json_encode([ "error" => "email_exists" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        }


        $user = User::createNewUser($data['nickname'], $data['email'], $data['password'], $data['birthday']);
        $user->startingData();

        // Update session with new user ID
        $session_data = $session->decode_session_data();
        $session_data['uid'] = $user->id;
        $session->save_session_data($session_data, true);

        // Generate access token
        $accessToken = JwtAuth::generate(["session_id" => $session->id, "uid" => $user->id, "exists" => true]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}
