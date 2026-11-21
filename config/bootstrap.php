<?php

declare(strict_types=1);

use App\Domain\Identity\IdentityRepository;
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

// Initialize Core Application Runtime & Activation Engine
$container->get(IdentityRepository::class)->initializeApp($app, $container);

// Register Middleware
(require __DIR__ . '/middleware.php')($app);

// Register Routes
(require __DIR__ . '/routes.php')($app);

return $app;
