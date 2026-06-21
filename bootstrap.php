<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require __DIR__ . '/vendor/autoload.php';

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'sotarsoft.pl', //'localhost', //'sotarsoft.pl',
    'database' => 'host924640_remik',
    'username' => 'host924640_remik',
    'password' => 'DFtfUG6rsmxraRVcRndM',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setEventDispatcher(
    new Dispatcher(new Container())
);

$capsule->setAsGlobal();
$capsule->bootEloquent();


$capsule->getEventDispatcher()->listen(
    QueryExecuted::class,
    function (QueryExecuted $query) use ($logger) {
        $logger->debug('SQL', [
            'sql'      => $query->sql,
            'bindings' => $query->bindings,
            'time_ms'  => $query->time,
        ]);
    }
);