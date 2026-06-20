<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Models\DeviceSession;

class DeviceMiddleware
{
    public function __invoke($request, $handler)
    {

        $device = $request->getAttribute('device') or null;
        $device_exists = $request->getAttribute('device_exists') or false;
        if ($device == null || $device_exists == false) {
            return $this->invalidApiRequest();
        }

        $data = $request->getAttribute('params') or [];
        if ($data['session_id'] ?? null) {
            $session_id = $data['session_id'];
        } else {
            return $this->invalidSession();
        }

        $session = DeviceSession::where('session_id', $session_id)->first();
        if (!$session) {
            return $this->invalidSession();
        } else {
            if ($session->expired_at && strtotime($session->expired_at) < time()) {
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