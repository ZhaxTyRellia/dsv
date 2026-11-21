<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Display Signage Valas (DSV)',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'base_path' => $_ENV['APP_BASE_PATH'] ?? 'auto',
    ],
    'db' => [
        'driver' => 'sqlite',
        'path' => (function (): string {
            $configured = trim((string)($_ENV['DB_PATH'] ?? 'storage/dsv.db'));
            if ($configured === '') {
                $configured = 'storage/dsv.db';
            }

            if (
                $configured === ':memory:' ||
                str_starts_with($configured, '/') ||
                str_starts_with($configured, '\\') ||
                preg_match('/^[A-Za-z]:[\\\\\/]/', $configured) === 1
            ) {
                return $configured;
            }

            return dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $configured);
        })(),
        'busy_timeout' => (int)($_ENV['DB_BUSY_TIMEOUT'] ?? 5000),
        'flags' => [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
    'paths' => [
        'root' => dirname(__DIR__),
        'public' => dirname(__DIR__) . '/public',
        'storage' => dirname(__DIR__) . '/storage',
        'templates' => dirname(__DIR__) . '/templates',
        'uploads' => [
            'logo' => dirname(__DIR__) . '/public/img/logo',
            'videos' => dirname(__DIR__) . '/public/videos',
        ],
    ],
];
