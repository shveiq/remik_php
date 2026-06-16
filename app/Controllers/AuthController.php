<?php

namespace App\Controllers;

use App\Models\DeviceSession;
use App\Models\Device;

class AuthController
{
    /* =========================
     TOKEN
    ========================= */
    public function refreshToken($request, $response)
    {
        $data = $request->getAttribute('params') or [];
        
        print_r($data);

        if (!isset($data['session_id'])) {





        } else {
            $session_id = $data['session_id'];

        }

        $response->getBody()->write(json_encode([ "ok" => true, "data" => $data, "body" => $request->getParsedBody(), "headers" => $request->getBody(), "request" => print_r($request, true) ]));
         return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
        /*
        $refreshToken = $data['session_id'] ?? null;

        if (!$refreshToken) {
            return $this->json($response, ['error' => 'refresh_token_required'], 400);
        }

        $tokenRecord = RefreshToken::where('token', $refreshToken)->first();

        if (!$tokenRecord || strtotime($tokenRecord->expires_at) < time()) {
            return $this->json($response, ['error' => 'invalid_refresh_token'], 401);
        }

        $user = User::find($tokenRecord->user_id);

        if (!$user) {
            return $this->json($response, ['error' => 'user_not_found'], 404);
        }

        $accessToken = $this->generateJwt($user->id);

        return $this->json($response, [
            'access_token' => $accessToken,
            'user' => [
                'id' => $user->id,
                'login' => $user->login
            ]
        ]); */
    }

    /* =========================
     LOGIN
    ========================= */
    public function login($request, $response)
    {
        $data = json_decode($request->getBody(), true);

        $user = User::where('email', $data['email'] ?? null)->first();

        if (!$user || !password_verify($data['password'] ?? '', $user->password)) {
            return $this->json($response, ['error' => 'invalid_credentials'], 401);
        }

        $accessToken = $this->generateJwt($user->id);
        $refreshToken = $this->generateRefreshToken();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30)
        ]);

        return $this->json($response, [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->id,
                'login' => $user->login
            ]
        ]);
    }

    /* =========================
     REGISTER
    ========================= */

}
