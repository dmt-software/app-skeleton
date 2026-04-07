<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\Config\Config;
use DMT\DependencyInjection\ConfigurationInterface;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

final readonly class DoctrineServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $config = $container->get(ConfigurationInterface::class);

        $configuration = ORMSetup::createAttributeMetadataConfig(
            paths: [__DIR__ . '/../Entities'],
            isDevMode: $config->get('app.debug', false) === true,
        );
        $configuration->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(
            params: $config->get('database'),
            config: $configuration
        );

        $container->set(
            id: EntityManagerInterface::class,
            value: fn() => new EntityManager($connection, $configuration)
        );
    }
}
