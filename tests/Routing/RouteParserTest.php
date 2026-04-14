<?php

namespace DMT\Test\Routing;

use DMT\Routing\Attributes\Route;
use DMT\Routing\Attributes\RouteGroup;
use DMT\Routing\RouteParser;
use DMT\Routing\RoutingException;
use DMT\Test\Fixtures\DummyInvokableHandler;
use DMT\Test\Fixtures\DummyMisconfiguredHandler;
use DMT\Test\Fixtures\DummyNoRoutesHandler;
use DMT\Test\Fixtures\DummyNoRoutesInGroupHandler;
use DMT\Test\Fixtures\DummyRouteGroupHandler;
use DMT\Test\Fixtures\DummyRoutesHandler;
use PHPUnit\Framework\TestCase;

class RouteParserTest extends TestCase
{
    public function testParseRouteForInvokableHandler(): void
    {
        $route = new RouteParser()->parse(DummyInvokableHandler::class)[0];

        $this->assertSame(DummyInvokableHandler::class, $route->callable);
    }

    public function testParseRoutesForHandler(): void
    {
        $routes = new RouteParser()->parse(DummyRoutesHandler::class);

        $this->assertCount(2, $routes);
        $this->assertContainsOnlyInstancesOf(Route::class, $routes);
        $this->assertSame([DummyRoutesHandler::class, 'index'], $routes[0]->callable);
    }

    public function testParseRouteGroupForHandler(): void
    {
        $routeGroup = new RouteParser()->parse(DummyRouteGroupHandler::class);

        $this->assertInstanceOf(RouteGroup::class, $routeGroup);
        $this->assertCount(2, $routeGroup->routes);
        $this->assertSame([DummyRouteGroupHandler::class, 'store'], $routeGroup->routes[0]->callable);
        $this->assertSame([DummyRouteGroupHandler::class, 'show'], $routeGroup->routes[1]->callable);
    }

    public function testNoRoutesForHandler(): void
    {
        $this->expectException(RoutingException::class);
        $this->expectExceptionMessage('No routes found');

        new RouteParser()->parse(DummyNoRoutesHandler::class);
    }

    public function testNoRoutesInGroupHandler(): void
    {
        $this->expectException(RoutingException::class);
        $this->expectExceptionMessage('No routes found');

        new RouteParser()->parse(DummyNoRoutesInGroupHandler::class);
    }

    public function testMisconfiguredRouteGroupHandler(): void
    {
        $this->expectException(RoutingException::class);
        $this->expectExceptionMessage('Could not parse routes');

        new RouteParser()->parse(DummyMisconfiguredHandler::class);
    }
}
