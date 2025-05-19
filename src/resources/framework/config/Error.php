<?php

namespace App\Config;

use Extro\HttpKernel\Contracts\ErrorHandler\ConfigInterface;

class Error implements ConfigInterface
{
    public function isDebugMode(): bool
    {
        return true;
    }
}
