<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\Config\Config;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use DMT\Middlewares\TrimTrailingSlashMiddleware;

/**
 * Routing service provider
 */
final readonly class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $app = $container->get(App::class);

        $container->set(
            id: TrimTrailingSlashMiddleware::class,
            value: fn() => new TrimTrailingSlashMiddleware($app->getResponseFactory())
        );

        $app->addMiddleware(
            middleware: $container->get(TrimTrailingSlashMiddleware::class)
        );

        $app->addErrorMiddleware(
            displayErrorDetails: $container->get(Config::class)->get('app.debug', false),
            logErrors: true,
            logErrorDetails: true
        );
    }
}
