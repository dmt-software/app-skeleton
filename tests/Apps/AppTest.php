<?php

namespace DMT\Test\Apps;

use DMT\Apps\App;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;

class AppTest extends TestCase
{
    public function testInit()
    {
        $app = new App(
            responseFactory: AppFactory::determineResponseFactory(),
            container: (new ContainerFactory())->createContainer()
        );
        $app->init();
var_dump($app->getContainer()->get(App::class));
        $this->assertInstanceOf(Container::class, $app->getContainer());
        $this->assertInstanceOf(App::class, $app->getContainer()->get(App::class));
    }
}
