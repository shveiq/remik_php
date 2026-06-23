<?php

namespace App\Controllers;

use App\Models\DeviceSession;
use App\Models\User;
use Utils\JwtAuth;
use Ramsey\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class AuthController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /* =========================
     TOKEN
    ========================= */
    public function refreshToken($request, $response)
    {
        $device = $request->getAttribute('device') or null;
        $device_exists = $request->getAttribute('device_exists') or false;
        if ($device == null) {
            $this->logger->error("invalid request - device not exists ");
            $response->getBody()->write(json_encode([ "error" => "invalid_request" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  
        }

        $data = $request->getAttribute('params') or [];
        if (!isset($data['session_id'])) {
            $this->logger->info("with out session id");
            $session_id = (string) Uuid::uuid4();

            $session = new DeviceSession();
            $session->device_id = $device->id;
            $session->session_id = $session_id;
            $session->save_session_data(["uid" => null, "hmac" => JwtAuth::generateHMACKey()], false);
            $session->save();
        } else {
            $this->logger->info("with session id");
            $session_id = $$data['session_id'];

            $session = DeviceSession::where('session_id', $session_id)->first();
            if (!$session) {
                    $this->logger->error("invalid session - no session in DB");
                    $response->getBody()->write(json_encode([ "error" => "invalid_session" ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);            
            } else {
                if ($session->expired_date && strtotime($session->expired_date) < time()) {
                    $this->logger->error("expired session");
                    $response->getBody()->write(json_encode([ "error" => "expired_session" ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);
                } else {
                    $session_expired_date = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7);
                    $session->expired_date = $session_expired_date;
                    $session->save();
                }
            }
        }

        $session_data = $session->decode_session_data();
        $accessToken = JwtAuth::generate(["session_id" => $session_id, "uid" => $session_data['uid'] ?? null, "hmac" => $session_data['hmac'] ?? null, "exists" => $device_exists]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     LOGIN
    ========================= */
    public function login($request, $response)
    {

        $device = $request->getAttribute('device') or null;    
        $data = $request->getAttribute('params') or [];
        $session = $request->getAttribute('session') or null;

        if (!$device || !$session) {
            // Middleware should have already checked this, but just in case
            $this->logger->error("invalid request - device or session not exists ", ["device" => $device, "session" => $session]);
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $token = $data['token'];
        try {
            $decoded_data = JwtAuth::decode($token);
        } catch (Exception $e) {
            $this->logger->error("invalid credentials - jwt decode exception ". $e->message());
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        } 

        if (!$decoded_data || !isset($decoded_data->nick_or_email) || !isset($decoded_data->password)) {
            $this->logger->error("invalid credentials", ["data" => $decoded_data ]);
            $response->getBody()->write(json_encode([ "error" => "invalid_credentials" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $user = User::where('email', $decoded_data->nick_or_email ?? null)->first();
        if ($user == null) {
            $this->logger->info("Nie znalazlem takiego email", ["email" => $decoded_data->nick_or_email]);
            
            $user = User::where('nickname', $decoded_data->nick_or_email ?? null)->first();
            if ($user == null) {
                $this->logger->info("Nie znalazlem takiego nickname", ["nickname" => $decoded_data->nick_or_email]);
            } else {
                $this->logger->info("Znalazłem nickname", ["nickname" => $decoded_data->nick_or_email]);
            }
        } else {
            $this->logger->info("Znalazłem email", ["email" => $decoded_data->nick_or_email]);
        }

        $this->logger->info("Uzytkownik", [ "user" => $user ]);
        if (!$user || $user->isBot != false || !password_verify($decoded_data->password ?? '', $user->password)) {
            $this->logger->error("invalid credentials - not found in DB", ["data" => $decoded_data ]);
            $response->getBody()->write(json_encode([ "error" => "invalid_credentials" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $session_data = $session->decode_session_data();
        $session_data['uid'] = $user->id;
        $session->save_session_data($session_data, true);

        $accessToken = JwtAuth::generate(["session_id" => $session->session_id, "uid" => $user->id, "hmac" => $session_data['hmac'], "exists" => true]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     LOGOUT
    ========================= */
    public function logout($request, $response)
    {
        $session = $request->getAttribute('session') or null;
        if (!$session) {
            $this->logger->error("invalid request - session not exists ");
            $response->getBody()->write(json_encode([ "error" => "invalid_session" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $session->expired_at = time();
        $session->save();

        $response->getBody()->write(json_encode([ "message" => "logged_out" ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /* =========================
     GEUST
    ========================= */
    public function guest($request, $response)
    {
        $device = $request->getAttribute('device') or null;    
        $session = $request->getAttribute('session') or null;
     
        if (!$device || !$session) {
            // Middleware should have already checked this, but just in case
            $this->logger->error("invalid request - device or session not exists ", ["device" => $device, "session" => $session]);
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        $user = User::createGuest();
        $user->saveDevice($device);
        $user->startingData();

        // Update session with new user ID
        $session_data = $session->decode_session_data();
        $session_data['uid'] = $user->id;
        $session->save_session_data($session_data, true);

        // Generate access token
        $accessToken = JwtAuth::generate(["session_id" => $session->session_id, "uid" => $user->id, "exists" => true]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /* =========================
     REGISTER
    ========================= */
    public function register($request, $response)
    {
        $device = $request->getAttribute('device') or null;    
        $session = $request->getAttribute('session') or null;
        $data = $request->getAttribute('params') or [];
     
        if (!$device || !$session) {
            // Middleware should have already checked this, but just in case
            $this->logger->error("invalid request - device or session not exists ", ["device" => $device, "session" => $session]);
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);  
        }

        if (!isset($data['nickname']) || !isset($data['token'])) {
            $this->logger->error("invalid request - nickname, token not exists ", ["nickname" => $data["nickname"], "token" => $data["token"]]);
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }

        $token = $data['token'];
        try {
            $decoded_data = JwtAuth::decode($token);
        } catch (Exception $e) {
            $this->logger->error("invalid request - jwt decode exception ". $e->message());
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        } 

        $nickname = $data['nickname'];
        $email = $decoded_data->email . "";
        $password = $decoded_data->password . "";
        $birthday = $decoded_data->birthday . "";

        if (!isset($email) || $email === "" || !isset($password)  || $password === "" || !isset($birthday) || $birthday == "") {
            $this->logger->error("invalid request - email, password or birthday not exists ", ["email" => $email, "password" => $password, "birthday" => $birthday]);
            $response->getBody()->write(json_encode([ "error" => "invalid_data" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }

        // Check if nickname or email already exists
        if (User::where('nickname', $nickname)->exists()) {
            $this->logger->error("invalid request - nickname is exists");
            $response->getBody()->write(json_encode([ "error" => "nickname_exists" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        } 
        if (User::where('email', $email)->exists()) {
            $this->logger->error("invalid request - email is exists");
            $response->getBody()->write(json_encode([ "error" => "email_exists" ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        }


        $user = User::createNewUser($nickname, $email, $password, $birthday);
        $user->saveDevice($device);
        $user->startingData();

        // Update session with new user ID
        $session_data = $session->decode_session_data();
        $session_data['uid'] = $user->id;
        $session->save_session_data($session_data, true);

        // Generate access token
        $accessToken = JwtAuth::generate(["session_id" => $session->session_id, "uid" => $user->id, "exists" => true]);
        $response->getBody()->write(json_encode([ "token" => $accessToken ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}
