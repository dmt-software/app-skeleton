<?php

declare(strict_types=1);

namespace DMT\Routing\Parser;

use ArgumentCountError;
use DMT\Routing\Attributes\Route;
use DMT\Routing\Attributes\RouteGroup;
use DMT\Routing\RoutingException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RouteParser
{
    /**
     * @param class-string $handler
     *
     * @return RouteGroup|list<Route>
     * @throws \DMT\Routing\RoutingException
     */
    public function parse(string $handler): array|RouteGroup
    {
        try {
            $class = new ReflectionClass($handler);

            if ($class->hasMethod('__invoke') && $class->getAttributes(Route::class)) {
                return $this->getRouteAttributes($class);
            }

            if ($class->getAttributes(RouteGroup::class)) {
                return $this->getRouteGroupAttribute($class);
            }

            $routes = [];
            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                array_push($routes, ...$this->getRouteAttributes($method));
            }

            if (empty($routes)) {
                throw new RoutingException('No routes found');
            }

            return $routes;
        } catch (ReflectionException | ArgumentCountError $exception) {
            throw new RoutingException('Could not parse routes', previous: $exception);
        }
    }

    /**
     * @throws ReflectionException
     */
    private function getRouteGroupAttribute(ReflectionClass $class): RouteGroup
    {
        $attributes = $class->getAttributes(RouteGroup::class);

        if (count($attributes) > 1) {
            throw new RoutingException("Handlers can contain only one route group");
        }

        $routeGroupClass = new ReflectionClass($attributes[0]->getName());

        /** @var RouteGroup $routeGroup */
        $routeGroup = $routeGroupClass->newInstanceArgs(
            $attributes[0]->getArguments() + ['handler' => $class->getName()]
        );

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            array_push($routeGroup->routes, ...$this->getRouteAttributes($method));
        }

        if (count($routeGroup->routes) == 0) {
            throw new RoutingException('No routes found');
        }

        return $routeGroup;
    }

    /**
     * @throws ReflectionException
     */
    private function getRouteAttributes(ReflectionMethod|ReflectionClass $methodOrClass): array
    {
        $callable = $methodOrClass instanceof ReflectionMethod
            ? [$methodOrClass->getDeclaringClass()->getName(), $methodOrClass->getName()]
            : $methodOrClass->getName()
        ;

        return array_map(
            function (ReflectionAttribute $attribute) use ($callable) {
                $routeClass = new ReflectionClass($attribute->getName());

                /** @var Route $route */
                $route = $routeClass->newInstanceArgs(
                    $attribute->getArguments() + compact('callable')
                );

                return $route;
            },
            $methodOrClass->getAttributes(Route::class)
        );
    }
}
