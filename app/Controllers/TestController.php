<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;

class TestController
{

    /* =========================
     LOGIN
    ========================= */
    public function info($request, $response)
    {

        $row = Capsule::selectOne('
            SELECT
                DATABASE() AS db_name,
                USER() AS user_name,
                VERSION() AS mysql_version
        ');

        $response->getBody()->write(print_r($row, true));
        return $response;
    }

}