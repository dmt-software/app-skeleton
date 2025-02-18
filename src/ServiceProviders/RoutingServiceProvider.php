<?php

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use DMT\Middlewares\TrimTrailingSlashMiddleware;

/**
 * Routing service provider
 */
readonly class RoutingServiceProvider implements ServiceProviderInterface
{
    public function __construct(private App $app)
    {
    }

    public function register(Container $container): void
    {
        $container->set(
            id: TrimTrailingSlashMiddleware::class,
            value: fn() => new TrimTrailingSlashMiddleware($this->app->getResponseFactory())
        );
    }
}
