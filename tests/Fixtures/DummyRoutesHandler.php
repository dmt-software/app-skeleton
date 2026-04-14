<?php

namespace DMT\Test\Fixtures;

use DMT\Routing\Attributes as DMT;

class DummyRoutesHandler
{
    #[DMT\Route(methods: ['GET', 'POST'], pattern: '/foo')]
    public function index()
    {
    }

    #[DMT\Route(methods: 'GET', pattern: '/foo/{id}')]
    public function show()
    {
    }
}
