<?php

declare(strict_types=1);

namespace DMT\Routing;

use DMT\Routing\Attributes\Route;
use DMT\Routing\Attributes\RouteGroup;
use DMT\Routing\Parser\RouteParser;
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
            foreach ($routing->middlewares as $middleware) {
                $group->addMiddleware($collector->getContainer()->get($middleware));
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
                foreach ($route->middlewares as $middleware) {
                    $map->addMiddleware($collector->getContainer()->get($middleware));
                }
            }
        }
    }
}
