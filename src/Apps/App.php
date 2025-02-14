<?php

declare(strict_types=1);

namespace DMT\Apps;

use DMT\Config\Config;
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
     * Register app service provider.
     */
    public function init(): void
    {
        $container = $this->getContainer();

        $container->register(provider: new AppServiceProvider($this));
    }

    /**
     * Load the configuration file(s)
     */
    public function loadConfig(string $configPath): void
    {
        $files = [$configPath];
        if (is_dir($configPath)) {
            $files = glob($configPath . '/*.*');
        }

        foreach ($files as $file) {
            $this->getContainer()->get(id: Config::class)->load($file);
        }
    }

    /**
     * Register installed service providers
     */
    public function initServices(): void
    {
        $container = $this->getContainer();

        $container->register(provider: new RoutingServiceProvider($this));
    }
}
