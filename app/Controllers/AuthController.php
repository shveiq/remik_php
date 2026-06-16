<?php

namespace App\Controllers;

use App\Models\DeviceSession;
use App\Models\Device;

use Ramsey\Uuid\Uuid;

class AuthController
{
    /* =========================
     TOKEN
    ========================= */
    public function refreshToken($request, $response)
    {
        $device = $request->getAttribute('device') or null;
        $device_exists = $request->getAttribute('device_exists') or false;
        if ($device_exists == false) {
            $response->getBody()->write(json_encode([ "error" => "Nowe urzadzenie" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['session_id'])) {
            $session_id = (string) Uuid::uuid4();

            $session = new DeviceSession();
            $session->device_id = $device->id;
            $session->session_id = $session_id;
            $session->save_session_data([], false);
            $session->save();
        } else {
            $session_id = $data['session_id'];

            $session = DeviceSession::where('session_id', $session_id)->first();
            if (!$session) {
                $session_id = (string) Uuid::uuid4();

                $session = new DeviceSession();
                $session->device_id = $device->id;
                $session->session_id = $session_id;
                $session->save_session_data([], false);
                $session->save();
            } else {
                if ($session->expired_at && strtotime($session->red_at) < time()) {
                    $session_data = $session->decode_session_data();

                    $session_id = (string) Uuid::uuid4();
                    $session = new DeviceSession();
                    $session->device_id = $device->id;
                    $session->session_id = $session_id;
                    $session->save_session_data($session_data, false);
                    $session->save();
                }
            }
        }

        $accessToken = $this->generateJwt($session->id);

        $session_data = $session->decode_session_data();
        if (!isset($session_data['user_id'])) {
            $response->getBody()->write(json_encode([ "status" => true, "session_id" => $session_id, "data" => $session_data, "access_token" => $accessToken ]));
        } else {
            $response->getBody()->write(json_encode([ "status" => true, "session_id" => $session_id, "access_token" => $accessToken ]));
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

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
