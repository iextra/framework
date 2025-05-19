<?php

use Extro\HttpKernel\HttpKernel;

ini_set('display_errors', 'on');

define('APP_START_TIME', microtime(true));
define('APP_START_MEMORY', memory_get_peak_usage());

session_start();

require __DIR__ . '/../vendor/autoload.php';

/** @var HttpKernel $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->run();
