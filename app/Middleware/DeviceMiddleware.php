<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Models\DeviceSession;
use Psr\Log\LoggerInterface;

class DeviceMiddleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($request, $handler)
    {
        $device = $request->getAttribute('device') or null;
        $device_exists = $request->getAttribute('device_exists') or false;
        if ($device == null || $device_exists == false) {
            $this->logger->error('Invalid Api Request', [ 'device' => $device, 'device_exists' => $device_exists ]);
            return $this->invalidApiRequest();
        }

        $session_id = $request->getAttribute('session_id') or [];
        if (!$session_id) {
            $this->logger->error('Invalid Session - no session id');
            return $this->invalidSession();
        }

        $session = DeviceSession::where('session_id', $session_id)->first();
        if (!$session) {
            $this->logger->error('Invalid Session - no record in DB');
            return $this->invalidSession();
        } else {
            if ($session->expired_date && strtotime($session->expired_date) < time()) {
                $this->logger->error('Invalid Session - session expired', [ 'time' => time(), 'expired_date' =>$session->expired_at ]);
                return $this->invalidSession();
            } else {
                $session_expired_date = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7);
                $session->expired_date = $session_expired_date;
                $session->save();
            }
            $request = $request->withAttribute('session', $session);
        }

        return $handler->handle($request);
    }

    private function invalidApiRequest(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'invalid_api_request',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }

    private function invalidSession(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'invalid_session',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
}