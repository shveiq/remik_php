<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Models\User;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        $session = $request->getAttribute('session') or null;
        if (!$session) {
            return $this->unauthorized();
        }

        $session_data = $session->decode_session_data();
        if ($session_data === null || !isset($session_data['uid'])) {
            return $this->unauthorized();
        }

        $user = User::find($session_data['uid']);

        if (!$user) {
            return $this->unauthorized();
        }

        $request = $request->withAttribute('user', $user);

        $receivedSignature = $request->getHeaderLine('X-HMAC-Signature');
        if (empty($receivedSignature)) {
            return $this->securityViolation();
        }

        $hmacKey = $session_data['hmac'];
        if (!$hmacKey) {
            return $this->securityViolation();
        }

        $contents = file_get_contents('php://input');
        $expectedSignature = hash_hmac('sha256', $contents, $hmacKey);

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            return $this->securityViolation();
        }

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
    
    private function securityViolation(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'security_violation',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

}
