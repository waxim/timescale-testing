<?php
require __DIR__.'/../vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
$whoops->register();

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => 'localhost',
    'database'  => 'postgres',
    'username'  => 'postgres',
    'password'  => 'password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();

$application = new Symfony\Component\Console\Application("Flux - Timescale Testing", "1.0");
$application->add(new App\Commands\FillTableWithRows);
$application->run();