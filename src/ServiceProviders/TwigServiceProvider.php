<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\Config\Config;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Environment;

final readonly class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $app = $container->get(App::class);

        $container->set(
            id: Twig::class,
            value: fn() => Twig::create(__DIR__ . '/../../templates', [
                'debug' => $container->get(Config::class)->get('app.debug', false),
                'cache' => __DIR__ . '/../../cache'
            ])
        );

        $container->set(
            id: Environment::class,
            value: fn() => $container->get(Twig::class)->getEnvironment()
        );

        $app->addMiddleware(
            middleware: TwigMiddleware::create($app, $container->get(Twig::class))
        );
    }
}
