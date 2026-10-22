<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Display Signage Valas (DSV)',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'base_path' => $_ENV['APP_BASE_PATH'] ?? '/dsv',
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => (int)($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_NAME'] ?? 'dsv',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
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
        'templates' => dirname(__DIR__) . '/templates',
        'uploads' => [
            'logo' => dirname(__DIR__) . '/public/img/logo',
            'videos' => dirname(__DIR__) . '/public/videos',
        ],
    ],
    'license' => [
        'file_path' => (function () {
            $configured = $_ENV['LICENSE_FILE_PATH'] ?? null;
            if (!empty($configured)) {
                return preg_replace_callback('/%([^%]+)%/', function ($matches) {
                    return getenv($matches[1]) ?: ($_SERVER[$matches[1]] ?? $matches[0]);
                }, $configured);
            }

            $userDir = getenv('USERPROFILE')
                ?: (getenv('HOMEDRIVE') && getenv('HOMEPATH') ? getenv('HOMEDRIVE') . getenv('HOMEPATH') : null)
                ?: (getenv('USERNAME') ? 'C:\\Users\\' . getenv('USERNAME') : null)
                ?: 'C:\\127.0.0.1';

            return rtrim($userDir, '\\/') . DIRECTORY_SEPARATOR . 'dsv' . DIRECTORY_SEPARATOR . 'license.key';
        })(),
        'secret_salt' => $_ENV['LICENSE_SECRET_SALT'] ?? 'GautavarisKey!!!',
    ],
];
