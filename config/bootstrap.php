<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables if .env file exists
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
}

// Build PHP-DI Container
$container = require __DIR__ . '/container.php';

// Instantiate Slim App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Set Base Path from settings
$settings = $container->get('settings');
if (!empty($settings['app']['base_path'])) {
    $app->setBasePath($settings['app']['base_path']);
}

// Register Middleware
(require __DIR__ . '/middleware.php')($app);

// Register Routes
(require __DIR__ . '/routes.php')($app);

return $app;
