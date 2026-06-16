<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Models\Device;

class ApiMiddleware
{
    public function __invoke($request, $handler)
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            } else {
                return $this->invalidMessage();
            }
        
            $data = $request->getParsedBody() ?? [];
            if (is_array($data)) {
                if (array_key_exists('app', $data) && is_array($data['app'])) {
                    $appData = $data['app'];
                    if (array_key_exists('id', $appData)) {
                        $request = $request->withAttribute('app_id', $appData['id']);
                    } else {
                        return $this->invalidApiMessage();
                    }
                    if (array_key_exists('app_version', $appData)) {
                        $request = $request->withAttribute('app_version', $appData['app_version']);
                    } else {
                        return $this->invalidApiMessage();
                    }
                    if (array_key_exists('model', $appData)) {
                        $request = $request->withAttribute('model', $appData['model']);
                    }
                    if (array_key_exists('system_version', $appData)) {
                        $request = $request->withAttribute('system_version', $appData['system_version']);
                    }
                    if (array_key_exists('system', $appData)) {
                        $request = $request->withAttribute('system', $appData['system']);
                    }

                    $device = Device::where('app_id', $appData['id'])->first();
                    if ($device) {
                        $request = $request->withAttribute('device', $device);
                        $request = $request->withAttribute('device_exists', true);
                    } else {
                        $device = new Device();
                        $device->app_id = $appData['id'];
//                        $device->app_version = $appData['app_version'];
                        $device->system = $appData['system'] ?? "<unknown>";
                        $device->model = $appData['model'] ?? "<unknown>";
                        $device->system_version = $appData['system_version'] ?? "<unknown>";
                        $device->save();
                        $request = $request->withAttribute('device', $device);
                        $request = $request->withAttribute('device_exists', false);
                    }

                }
                if (array_key_exists('request', $data)) {
                    $request = $request->withAttribute('params', $data['request']);
                }
            }
        } 

        $request->withAttribute('params', array("ok" => true));

        $response = $handler->handle($request);
        return $response;
    }

    private function invalidMessage(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'invalid_request',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

    private function invalidApiMessage(): Response
    {
        $response = new Response();

        $response->getBody()->write(json_encode([
            'error' => 'invalid_api_request',
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

}
