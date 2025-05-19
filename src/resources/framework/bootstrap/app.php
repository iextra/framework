<?php

use Extro\HttpKernel\HttpKernel;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/container.php';
return $container->get(HttpKernel::class);
