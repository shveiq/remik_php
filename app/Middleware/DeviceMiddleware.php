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

        $data = $request->getAttribute('params') or [];
        if ($data['session_id'] != null) {
            $session_id = $data['session_id'];
        } else {
            $this->logger->error('Invalid Session', [ 'data' => $data ]);
            return $this->invalidSession();
        }

        $session = DeviceSession::where('session_id', $session_id)->first();
        if (!$session) {
            $this->logger->error('Invalid Session - no record in DB');
            return $this->invalidSession();
        } else {
            if ($session->expired_at && strtotime($session->expired_at) < time()) {
                $this->logger->error('Invalid Session - session expired', [ 'time' => time(), 'expired_date' =>$session->expired_at ]);
                return $this->invalidSession();
            } else {
                $session_expired_at = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7);
                $session->expired_at = $session_expired_at;
                $session->save();
            }
            $request = $request->withAttribute('session', $session);
        }

        $response = $handler->handle($request);
        return $response;
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