<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Framework\API\Application\Wrappers\SlimApplicationWrapper;
use Framework\API\REST;

use function App\Infrastructure\DependencyInjection\get_dependency_definitions;

require_once __DIR__.'/../vendor/autoload.php';

// PHP DI setup
$containerBuilder = new ContainerBuilder();

$containerBuilder->useAttributes(false);
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions(get_dependency_definitions());

$container = $containerBuilder->build();

// Slim framework application setup
$app = \DI\Bridge\Slim\Bridge::create($container);

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(displayErrorDetails: true, logErrors: false, logErrorDetails: false);

// REST API setup
$api = new REST(new SlimApplicationWrapper($app));
$api->setBaseURL('/api');

$api->registerEndpoint(new \App\Application\Endpoints\Post\CreateEndpoint());
$api->registerEndpoint(new \App\Application\Endpoints\Post\GetAllEndpoint());

$api->run();
