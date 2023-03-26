<?php

declare(strict_types=1);

namespace DMT\Attributes;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

#[Attribute(Attribute::TARGET_METHOD), NamedArgumentConstructor]
final class Route
{
    public function __construct(
        public readonly string $route,
        public readonly string|null $name,
        public readonly string|array $methods = 'GET',
        public readonly array $middleware = [],
    ) {

    }
}