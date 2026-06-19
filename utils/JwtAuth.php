<?php

namespace Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth
{
    private static string $secret = 'SECRET_KEY_CHANGE_ME1234567890@#$%';

    public static function generate(array $data): string
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'data' => $data
        ];

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decode(string $token)
    {
        return JWT::decode($token, new Key(self::$secret, 'HS256'));
    }
}
