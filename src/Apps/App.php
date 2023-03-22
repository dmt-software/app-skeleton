<?php

declare(strict_types=1);

namespace DMT\Apps;

use DMT\DependencyInjection\Container;
use DMT\ServiceProviders\AppServiceProvider;
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
        $this->getContainer()->register(provider: new AppServiceProvider($this));
    }
}
