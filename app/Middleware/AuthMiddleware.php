<?php

namespace App\Middleware;

use Utils\JwtAuth;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader) {
            return $this->unauthorized();
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JwtAuth::decode($token);
        } catch (Exception $e) {
            return $this->unauthorized();
        }

        $sessionId = $decoded->uid ?? null;
        /*
        $session = Session::find($sessionId);
        if (!$session) {
            return -> $this->unauthorized();
        }
        $request = $request->withAttribute('session', $session);
        */
        print("OK");

        return $handler->handle($request);
    }

    private function unauthorized(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'unauthorized',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
    
}
