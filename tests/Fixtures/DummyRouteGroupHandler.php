<?php

namespace DMT\Test\Fixtures;

use DMT\Routing\Attributes as DMT;

#[DMT\RouteGroup(pattern: '/group', routes: [
    new DMT\Route( methods: ['POST'], pattern: '/bar', callable: [self::class, 'store']),
])]
class DummyRouteGroupHandler
{
    #[DMT\Route(methods: ['GET'], pattern: '/foo')]
    public function show()
    {
    }

    public function store()
    {
    }
}
