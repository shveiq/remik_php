<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/vendor/autoload.php';

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'sotarsoft.pl',
    'database' => 'host924640_remik',
    'username' => 'host924640_remik',
    'password' => 'DFtfUG6rsmxraRVcRndM',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
