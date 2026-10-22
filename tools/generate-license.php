<?php

declare(strict_types=1);

/**
 * DSV License Key Generator (Developer Only Tool)
 * Usage:
 *   php tools/generate-license.php [DEVICE_ID]
 *
 * Example:
 *   php tools/generate-license.php 8AAE73DD-6EE0-0C4B-B971-D8C497521A5E
 */

$secretSalt = 'MTP-Production!!!';

$deviceId = $argv[1] ?? null;

if (empty($deviceId)) {
    // If not provided as argument, try reading current system UUID
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $uuid = trim((string)shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystemProduct).UUID"'));
        if (!empty($uuid)) {
            $deviceId = strtoupper($uuid);
        }
    }
}

if (empty($deviceId)) {
    echo "\nError: Please provide Device ID.\n";
    echo "Usage: php tools/generate-license.php [DEVICE_ID]\n\n";
    exit(1);
}

$deviceId = strtoupper(trim($deviceId));
$licenseKey = hash_hmac('sha256', $deviceId, $secretSalt);

echo "\n======================================================\n";
echo "       DSV LICENSE KEY GENERATOR (MASTER TOOL)        \n";
echo "======================================================\n";
echo " Device ID    : " . $deviceId . "\n";
echo " License Key  : " . $licenseKey . "\n";
echo "======================================================\n\n";
