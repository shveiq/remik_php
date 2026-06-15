<?php

namespace App/Middleware;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        $auth = $request->getHeaderLine('Authorization');

        if (!$auth) {
            return new \Slim\Psr7\Response(401);
        }

        $token = str_replace('Bearer ', '', $auth);

        try {
            $decoded = JwtAuth::decode($token);
        } catch (Exception $e) {
            return new \Slim\Psr7\Response(401);
        }

        return $handler->handle(
            $request->withAttribute('user', $decoded->data)
        );
    }
}
