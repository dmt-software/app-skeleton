<?php

namespace DMT\Test\Fixtures;

use DMT\Routing\Attributes as DMT;

#[DMT\Route(methods: ['GET'], pattern: '/foo', name: 'foo')]
class DummyInvokableHandler
{
    public function __invoke()
    {
    }
}
