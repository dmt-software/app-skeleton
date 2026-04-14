<?php

namespace DMT\Test\Fixtures;

use DMT\Routing\Attributes as DMT;

#[DMT\RouteGroup(pattern: '/foo')]
class DummyNoRoutesInGroupHandler
{
    public function index()
    {
    }
}
