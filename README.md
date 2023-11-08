# Application Skeleton 

[![build](https://github.com/dmt-software/app-skeleton/actions/workflows/push-action.yml/badge.svg)](https://github.com/dmt-software/app-skeleton/actions/workflows/push-action.yml)

## Installation

Replace **[path]** in the command below to create a new application in that location. 

```
composer create-project dmt-software/app-skeleton [path] --remove-vcs
```
## Dependency injection

Dependency is handled by _Service Providers_ which are included within the _App::init_ method. Within this method you 
can add more providers that list their dependencies within the _Dependency Container_.

> see [dmt-software/di-plug](https://packagist.org/packages/dmt-software/di-plug) for container implementations that are
> supported.

### Container 

By default, the application uses Pimple container to hold the dependency injection.
This can be changed to another container implementation. 

#### PHP-DI container 

```bash
composer remove pimple/pimple
composer require php-di/php-di
```

The container will be auto-discovered, but to ensure the chosen container 
implementation is used it can be configured manually.

```php

use DMT\Apps\App;
use DMT\DependencyInjection\ContainerFactory;
use DI\Container;
use Slim\Factory\AppFactory;

$app = new App(
    responseFactory: AppFactory::determineResponseFactory(),
    container: (new ContainerFactory())->createContainer(new Container())
);
```

## Configuration

### Get/Set

Configuration options within the configuration can easily be accessed by a dotted-slug.

```php
$config->get(); // will return all the options as array
$config->get(option: 'option.slug'); // will return the value stored in Config::options['option']['slug'] if set
$config->get(option: 'not.set.option', default: 'value'); // will return the default when it is not set
```

Setting new options will use the same dotted-slug as option identifier.

```php
// all will store the same value in the config
$config->set(value: ['option' => ['slug' => 'value']]);
$config->set(option: 'option', value: ['slug' => 'value']);
$config->set(option: 'option.slug', value: 'value');
```

Setting options within the configuration will use the *array_replace* strategy.

### Loading configuration

The configuration can be loaded from a file. By default, this is a php file that returns an array or a Closure that 
returns an array containing the configuration options.

```php 
// file: config/application.php
return static function () {
    return [
        // settings
    ];
};

// within the application or service provider
$config->load('config/application.php');
```

#### Yaml configuration

```bash
composer require symfony/yaml
```

Use the ChainLoader to enable both file include and yaml config files.

```php
use DMT\Config\Loaders\FileLoader;
use DMT\Config\Loaders\FileLoaderInterface;
use DMT\Config\Loaders\LoaderChain;
use Symfony\Component\Yaml\Yaml;

$container->set(
    id: FileLoaderInterface::class, 
    value: fn() => new LoaderChain(loaders: [
        new FileLoader(),
        new FileLoader('yaml', Yaml::parseFile(...))
    ])
);
```
