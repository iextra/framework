<?php

namespace App\Middlewares;

use Extro\HttpRouter\Exceptions\DuplicateRequestMethodException;
use Extro\HttpRouter\Exceptions\RequestMethodNotAllowedException;
use Extro\HttpRouter\Exceptions\RequestNotMatchedException;
use Extro\HttpRouter\RouterFactory;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMatching implements MiddlewareInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws DuplicateRequestMethodException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            /** @var RouterFactory $routerFactory */
            $routerFactory = $this->container->get(RouterFactory::class);
            $router = $routerFactory->create();

            $endpoint = $request->getUri()->getPath();

            if (in_array($request->getMethod(), ['OPTIONS', 'HEAD'])) {
                $response = new EmptyResponse(200);

                return $response->withHeader(
                    'Access-Control-Allow-Methods',
                    implode(', ',  $router->getAllowedRequestMethodsForUri($endpoint))
                );
            }

            // TODO: можно использовать кэширование результатов сопоставления маршрутов с URI,
            // TODO чтобы избежать повторных вычислений, если URI уже был обработан ранее.
            $route = $router->match($endpoint, $request->getMethod());

            if (!empty($middlewares = array_reverse($route->getMiddlewares()))) {
                foreach ($middlewares as $middleware) {
                    $handler->addToStart($this->container->get($middleware));
                }
            }

            return $handler->handle($request->withAttribute('route', $route));
        }
        catch (RequestMethodNotAllowedException $e) {
            return new EmptyResponse(405);
        } catch (RequestNotMatchedException $e) {
            return new EmptyResponse(404);
        }
    }
}
