<?php

namespace App\Middlewares;

use Extro\Di\Invoker;
use Extro\HttpRouter\Route;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use RuntimeException;

class RouteHandler implements MiddlewareInterface
{
    private Invoker $invoker;

    public function __construct(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Route $route*/
        if (!empty($route = $request->getAttribute('route'))) {
            $routeHandler = $route->getHandler();
            $response = $this->invoker->invoke($routeHandler);

            if ($response instanceof ResponseInterface) {
                return  $response;
            }

            throw new RuntimeException(sprintf(
                "The route handler '%s' should return %s returns the %s",
                $route->getPath(),
                ResponseInterface::class,
                gettype($response)
            ));
        }

        return $handler->handle($request);
    }
}