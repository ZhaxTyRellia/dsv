<?php

declare(strict_types=1);

namespace App\Support;

class FlashMessage
{
    private const SESSION_KEY = '_flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }

    public function add(string $type, string $message): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
        $_SESSION[self::SESSION_KEY][] = [
            'type' => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message,
        ];
    }

    public function get(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);
        return is_array($messages) ? $messages : [];
    }

    public function has(): bool
    {
        return !empty($_SESSION[self::SESSION_KEY]);
    }
}
