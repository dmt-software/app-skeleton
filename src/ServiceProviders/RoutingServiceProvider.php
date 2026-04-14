<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\DependencyInjection\ConfigurationInterface;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use DMT\Middlewares\TrimTrailingSlashMiddleware;

/**
 * Routing service provider
 */
final readonly class RoutingServiceProvider implements ServiceProviderInterface
{
    public function __construct(
        private App $application,
        private ConfigurationInterface $config,
    ) {
    }

    public function register(Container $container): void
    {
        $this->application->addMiddleware(
            middleware: $container->get(TrimTrailingSlashMiddleware::class)
        );

        $this->application->addErrorMiddleware(
            displayErrorDetails: $this->config->get('app.debug', false),
            logErrors: $this->config->get('app.logErrors', true),
            logErrorDetails: $this->config->get('app.logErrorDetails', false),
        );
    }
}
