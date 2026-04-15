<?php

declare(strict_types=1);

namespace DMT\Routing\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class RouteGroup
{
    public function __construct(
        public readonly string $pattern,
        /** @var class-string */
        public readonly string $handler = '',
        /** @var list<class-string<MiddlewareInterface>> */
        public readonly array $middlewares = [],
        /** @var list<Route>  */
        public array $routes = [],
    ) {
    }
}
