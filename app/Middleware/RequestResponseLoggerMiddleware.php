<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class RequestResponseLoggerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $start = microtime(true);

        // --- Request log ---
        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'uri'    => (string) $request->getUri(),
            'ip'     => $request->getServerParams()['REMOTE_ADDR'] ?? null,
            'headers' => $request->getHeaders(),
            'query'  => $request->getQueryParams(),
            'body'   => $request->getParsedBody(),
        ]);

        // Handle request
        $response = $handler->handle($request);

        $duration = microtime(true) - $start;

        // --- Response log ---
        $this->logger->info('Outgoing response', [
            'status'   => $response->getStatusCode(),
            'headers'  => $response->getHeaders(),
            'reply'    => $response->getBody(),
            'duration' => round($duration * 1000, 2) . ' ms',
        ]);

        return $response;
    }
}