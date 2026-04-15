<?php

declare(strict_types=1);

namespace DMT\Apps;

use DMT\DependencyInjection\ConfigurationInterface;
use DMT\DependencyInjection\Container;
use DMT\Routing\ControllerRouting;
use DMT\ServiceProviders\AppServiceProvider;
use DMT\ServiceProviders\DoctrineServiceProvider;
use DMT\ServiceProviders\RoutingServiceProvider;
use DMT\ServiceProviders\TwigServiceProvider;
use ReflectionMethod;
use Slim\App as BaseApp;
use Slim\Interfaces\RouteCollectorProxyInterface;

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
            $this->getContainer()->get(id: ConfigurationInterface::class)->load($file);
        }
    }

    /**
     * Register installed service providers
     */
    public function initServices(): void
    {
        $container = $this->getContainer();

        $container->register($container->get(DoctrineServiceProvider::class));
        $container->register($container->get(TwigServiceProvider::class));
        $container->register($container->get(RoutingServiceProvider::class));
    }

    /**
     * Add the routes from a controller class.
     *
     * @param class-string $controller
     */
    public function routeController(string $controller, ?RouteCollectorProxyInterface $collector = null): void
    {
        $container = $this->getContainer();

        $container->get(ControllerRouting::class)->route($controller, $collector ?? $this);
        $container->set(
            id: $controller,
            value: fn () => new ReflectionMethod($container, 'getInstance')->invoke($container, $controller)
        );
    }
}
