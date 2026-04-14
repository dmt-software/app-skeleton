<?php

namespace DMT\Test\Fixtures;

use DMT\Routing\Attributes as DMT;

class DummyMisconfiguredHandler
{
    #[DMT\Route(methods: ['GET'])]
    public function show()
    {
    }
}
