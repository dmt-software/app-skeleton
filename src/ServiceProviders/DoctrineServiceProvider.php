<?php

declare(strict_types=1);

namespace DMT\ServiceProviders;

use DMT\DependencyInjection\ConfigurationInterface;
use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

final readonly class DoctrineServiceProvider implements ServiceProviderInterface
{
    public function __construct(private ConfigurationInterface $config)
    {
    }

    public function register(Container $container): void
    {
        if ($this->config->get('app.debug', false) === true) {
            $queryCache = new ArrayAdapter();
            $metadataCache = new ArrayAdapter();
        } else {
            $queryCache = new PhpFilesAdapter('doctrine_queries');
            $metadataCache = new PhpFilesAdapter('doctrine_metadata');
        }

        $driverImpl = new AttributeDriver([__DIR__ . '/../Entity']);

        $configuration = new Configuration();
        $configuration->setMetadataCache($metadataCache);
        $configuration->setQueryCache($queryCache);
        $configuration->setMetadataDriverImpl($driverImpl);
        $configuration->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(
            params: $this->config->get('database'),
            config: $configuration
        );

        $container->set(
            id: EntityManagerInterface::class,
            value: fn() => new EntityManager($connection, $configuration)
        );
    }
}
