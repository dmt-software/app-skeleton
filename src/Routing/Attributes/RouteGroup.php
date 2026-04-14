<?php

namespace DMT\Routing\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class RouteGroup
{
    public function __construct(
        public readonly string $pattern,
        public readonly string $handler = '',
        /** @var list<MiddlewareInterface> */
        public readonly array $middlewares = [],
        /** @var list<Route>  */
        public array $routes = [],
    ) {
    }
}
