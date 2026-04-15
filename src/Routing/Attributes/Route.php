<?php

declare(strict_types=1);

namespace DMT\Routing\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Route
{
    public array $methods;

    public function __construct(
        string|array $methods,
        public string $pattern,
        public string|array $callable = '',
        public ?string $name = null,
        /** @var list<class-string<MiddlewareInterface>> */
        public array $middlewares = [],
    ) {
        $this->methods = array_map('strtoupper', is_string($methods) ? explode(',', $methods) : $methods);
    }
}
