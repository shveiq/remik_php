<?php

namespace App\Controllers;

use App\Models\CronJobLog;

class CronJobController
{
    public function cronJob($request, $response)
    {
        $cronJobLog = new CronJobLog();
        $cronJobLog->save();

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}