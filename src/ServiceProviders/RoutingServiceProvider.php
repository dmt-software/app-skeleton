<?php

namespace DMT\ServiceProviders;

use DMT\Apps\App;
use DMT\Config\Config;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use InvalidArgumentException;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public const ROUTING_CACHE_FILE = __DIR__ . '/../../cache/routing/.routes.cache';

    public function __construct(private readonly App $app)
    {
    }

    public function register(Container $container): void
    {
        $config = $container->get(id: Config::class);

        if (!$config->get(option: 'application.debug', default: true)) {
            $cacheFile = $config->get('routing.cache.file', self::ROUTING_CACHE_FILE);

            $this->ensureCacheDirectoryExists(dirname($cacheFile));
            $this->app->getRouteCollector()->setCacheFile(cacheFile: $cacheFile);
        }

        $this->app->get('/', function ($req, $res, $args) {
            $res->getBody()->write('<h1>Work in progress</h1><p>time to dive into attributes</p>');
            return $res;
        });
    }

    private function ensureCacheDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        $i = 0;
        while (true) {
            if (!$dir = realpath(path: dirname(path: $directory, levels: ++$i))) {
                continue;
            }

            if ($dir == dirname($dir)) {
                break;
            }

            if (is_writable($dir)) {
                mkdir($directory, recursive: true);
                return;
            }
        }
        throw new InvalidArgumentException(message: 'cache is not writable');
    }
}
