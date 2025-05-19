<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Profiler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = defined('APP_START_TIME') ? APP_START_TIME : microtime(true);
        $startMemory = defined('APP_START_MEMORY') ? APP_START_MEMORY : memory_get_peak_usage();

        $response = $handler->handle($request);
        $time = microtime(true) - $startTime;
        $memory = abs($startMemory - memory_get_peak_usage());

        return $response
            ->withHeader('X-Time', self::formatTime($time))
            ->withHeader('X-Memory', self::formatMemory($memory));
    }

    protected static function formatTime(float $time): string
    {
        return round($time, 3) . ' Sec';
    }

    protected static function formatMemory(float $memory): string
    {
        return round($memory / 1024 / 1024, 2) . ' Mb';
    }
}
