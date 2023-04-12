<?php

declare(strict_types=1);

namespace DMT\Apps;

use DMT\DependencyInjection\Container;
use DMT\ServiceProviders\AppServiceProvider;
use DMT\ServiceProviders\RoutingServiceProvider;
use Slim\App as BaseApp;

/**
 * Application
 *
 * @method Container getContainer()
 */
class App extends BaseApp
{
    /**
     * Register all (installed) service providers.
     */
    public function init(): void
    {
        $container = $this->getContainer();
        $container->register(provider: new AppServiceProvider($this));
        $container->register(provider: new RoutingServiceProvider($this));
    }
}
