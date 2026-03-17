<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

final readonly class TwigServiceProvider implements ServiceProviderInterface
{
    public function __construct(private App $app)
    {
    }

    public function register(Container $container): void
    {
        $container->set(
            id: Twig::class,
            value: fn() => Twig::create(__DIR__ . '/../../templates', ['cache' => __DIR__ . '/../../cache'])
        );

        $this->app->addMiddleware(TwigMiddleware::create($this->app, $container->get(Twig::class)));
    }
}
