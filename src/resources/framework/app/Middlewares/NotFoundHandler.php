<?php

namespace App\Middlewares;

use Extro\RequestHandler\Contracts\FallbackRequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundHandler implements FallbackRequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(404);
    }
}
