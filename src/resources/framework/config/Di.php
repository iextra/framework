<?php

namespace App\Config;

use App\Config;
use App\Middlewares\NotFoundHandler;
use Extro\Di\Container;
use Extro\Di\Contracts\ContainerConfigInterface;
use Extro\HttpKernel\Contracts\ErrorHandlerInterface;
use Extro\HttpKernel\Contracts\ResponseSenderInterface;
use Extro\HttpKernel\Default\ErrorHandler;
use Extro\HttpKernel\Default\ResponseSender;
use Extro\HttpKernel\Contracts\ErrorHandler\ConfigInterface as ErrorHandlerConfigInterface;
use Extro\RequestHandler\Contracts\FallbackRequestHandlerInterface;
use Extro\RequestHandler\Contracts\MiddlewareConfigInterface;
use Extro\RequestHandler\Contracts\MiddlewareLoaderInterface;
use Extro\RequestHandler\MiddlewareLoader;
use Extro\RequestHandler\RequestHandler;
use Extro\HttpRouter\Contracts\ConfigInterface as RoutingConfigInterface;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Di implements ContainerConfigInterface
{
    public function getAliases(): array
    {
        return [
            ContainerInterface::class => Container::class,
            RequestHandlerInterface::class => RequestHandler::class,
            MiddlewareLoaderInterface::class => MiddlewareLoader::class,
            MiddlewareConfigInterface::class => Config\Middlewares::class,
            FallbackRequestHandlerInterface::class => NotFoundHandler::class,
            ResponseSenderInterface::class => ResponseSender::class,
            ErrorHandlerInterface::class => ErrorHandler::class,
            ErrorHandlerConfigInterface::class => Config\Error::class,
            RoutingConfigInterface::class => Config\Router::class,

            ServerRequestInterface::class => ServerRequest::class,
            RequestInterface::class => Request::class,
        ];
    }

    public function getFactories(): array
    {
        return [
            Request::class => function (ContainerInterface $container) {
                /** @var ServerRequestInterface $r */
                $serverRequest = $container->get(ServerRequestInterface::class);

                return (new RequestFactory())->createRequest(
                    method: $serverRequest->getMethod(),
                    uri: $serverRequest->getUri()
                );
            },
            ServerRequest::class => fn () => ServerRequestFactory::fromGlobals(),
        ];
    }

    public function getOnAfterBuildHandles(): array
    {
        return [];
    }

    public static function create(): static
    {
        return new static();
    }
}

return Di::create();
