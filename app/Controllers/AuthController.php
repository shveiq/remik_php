<?php

namespace App\Controllers

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{

    /* =========================
     LOGIN
    ========================= */
    public function login($request, $response)
    {
        $data = json_decode($request->getBody(), true);

        $user = User::where('login', $data['login'] ?? null)->first();

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
}
