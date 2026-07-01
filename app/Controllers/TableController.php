<?php

namespace App\Controllers;

use Psr\Log\LoggerInterface;

use App\Models\RemikTable;

class TableController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function getAll($request, $response)
    {
        $tables = RemikTable::all();
        $response->getBody()->write(json_encode($tables));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}