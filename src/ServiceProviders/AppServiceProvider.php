<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\Config\Config;
use DMT\Config\Loaders\FileLoader;
use DMT\Config\Loaders\FileLoaderInterface;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App as BaseApp;

/**
 * App service provider
 *
 * Store some stuff within the container.
 */
class AppServiceProvider implements ServiceProviderInterface
{
    public function __construct(private readonly App $app)
    {
    }

    /**
     * @inheritDoc
     */
    public function register(Container $container): void
    {
        $container->set(id: App::class, value: fn() => $this->app);
        $container->set(id: BaseApp::class, value: fn() => $this->app);
        $container->set(id: Config::class, value: fn() => new Config($container->get(FileLoaderInterface::class)));
        $container->set(id: ContainerInterface::class, value: fn() => $container);
        $container->set(id: FileLoaderInterface::class, value: fn() => new FileLoader());
        $container->set(id: ResponseFactoryInterface::class, value: fn() => $this->app->getResponseFactory());
    }
}
