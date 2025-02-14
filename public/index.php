<?php

use DMT\Apps\App;
use DMT\DependencyInjection\ContainerFactory;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = new App(
    responseFactory: AppFactory::determineResponseFactory(),
    container: (new ContainerFactory())->createContainer()
);

/* ************************************* *
 *       Initialize the application      *
 * ************************************* */
$app->init();

/* ************************************* *
 *          Load configuration           *
 * ************************************* */
$app->loadConfig(__DIR__ . '/../config/');

$app->initServices();

$app->run();
