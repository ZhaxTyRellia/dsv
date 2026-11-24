<?php

declare(strict_types=1);

use App\Domain\Identity\IdentityRepository;
use Slim\Factory\AppFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
