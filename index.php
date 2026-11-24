<?php

declare(strict_types=1);

/**
 * Universal Root Entry Point — forwards to public/index.php across all server engines
 * (Apache, Nginx, IIS, LiteSpeed, WampDock, XAMPP, Laragon, cPanel, PHP CLI-Server).
 */

require_once __DIR__ . '/public/index.php';
