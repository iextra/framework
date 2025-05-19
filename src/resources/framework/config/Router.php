<?php

namespace App\Config;

use Extro\HttpRouter\Contracts\ConfigInterface;

class Router implements ConfigInterface
{
    public function getRouteDirectoryPath(): string
    {
         return __DIR__ . '/../routes';
    }
}
