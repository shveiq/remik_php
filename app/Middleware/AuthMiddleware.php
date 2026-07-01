<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Models\User;
use Psr\Log\LoggerInterface;

class AuthMiddleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($request, $handler)
    {
        $session = $request->getAttribute('session') or null;
        if (!$session) {
            $this->logger->error('Invalid Request - no session');
            return $this->unauthorized();
        }

        $session_data = $session->decode_session_data();
        if ($session_data === null || !isset($session_data['uid'])) {
            $this->logger->error('Invalid Request - no session or user', [ "session_data" => $session_data ]);
            return $this->unauthorized();
        }

        $user = User::find($session_data['uid']);

        if (!$user) {
            $this->logger->error('Invalid Request - no user in DB');
            return $this->unauthorized();
        }

        $request = $request->withAttribute('user', $user);

        $receivedSignature = $request->getHeaderLine('X-HMAC-Signature');
        if (empty($receivedSignature)) {
            $this->logger->error('Invalid Request - no hmac header');
            return $this->securityViolation();
        }

        $hmacKeyHex = $session_data['hmac'];
        if (!$hmacKeyHex) {
            $this->logger->error('Invalid Request - no hmac in session');
            return $this->securityViolation();
        }
        $hmacKey = hex2bin($hmacKeyHex);

        $contents = file_get_contents('php://input');
        $expectedSignature = hash_hmac('sha256', $contents, $hmacKey);

	$this->logger->info($hmacKey);
        $this->logger->info($contents);
        $this->logger->info($expectedSignature);
        $this->logger->info($receivedSignature);

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            $this->logger->error('Invalid Request - hmac hash are not equals');
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
