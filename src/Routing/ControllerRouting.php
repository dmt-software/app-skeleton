<?php

declare(strict_types=1);

namespace DMT\Routing;

use DMT\Routing\Attributes\Route;
use DMT\Routing\Attributes\RouteGroup;
use DMT\Routing\Parser\RouteParser;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class ControllerRouting
{
    public function route(string $class, RouteCollectorProxyInterface $collector): void
    {
        $routing = new RouteParser()->parse($class);

        if (!$routing instanceof RouteGroup) {
            $this->addRoutes($collector, $routing);

            return;
        }

        $router = $this;

        $group = $collector->group(
            $routing->pattern,
            fn (RouteCollectorProxyInterface $routeCollectorProxy) => $router->addRoutes(
                $routeCollectorProxy,
                $routing->routes
            )
        );

        if ($routing->middlewares !== null) {
            foreach ($routing->middlewares as $middleware => $arguments) {
                $group->addMiddleware(
                    $this->loadMiddleware(
                        $collector->getContainer(),
                        $middleware,
                        $arguments
                    )
                );
            }
        }
    }

    /**
     * @param list<Route> $routes
     */
    private function addRoutes(RouteCollectorProxyInterface $collector, array $routes): void
    {
        foreach ($routes as $route) {
            $map = $collector->map($route->methods, $route->pattern, $route->callable);

            if ($route->name) {
                $map->setName($route->name);
            }

            if ($route->middlewares !== null) {
                foreach ($route->middlewares as $middleware => $arguments) {
                    $map->addMiddleware(
                        $this->loadMiddleware(
                            $collector->getContainer(),
                            $middleware,
                            $arguments
                        )
                    );
                }
            }
        }
    }

    private function loadMiddleware(
        ContainerInterface $container,
        string|int $middleware,
        string|array $arguments = []
    ): MiddlewareInterface {
        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }
        if (is_string($middleware) && class_exists($middleware)) {
            array_unshift($arguments, $middleware);
        }

        return $container->get(...$arguments);
    }
}
