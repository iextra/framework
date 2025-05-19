<?php

use Extro\Di\ContainerFactory;

$config = require __DIR__ . '/../config/Di.php';
return ContainerFactory::create($config);
