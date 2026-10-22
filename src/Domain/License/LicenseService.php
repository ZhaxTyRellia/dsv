<?php

declare(strict_types=1);

namespace App\Domain\License;

class LicenseService
{
    private string $licenseFilePath;
    private string $secretSalt;

    public function __construct(string $licenseFilePath, string $secretSalt)
    {
        $this->licenseFilePath = $licenseFilePath;
        $this->secretSalt = $secretSalt;
    }

    public function getDeviceUUID(): ?string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = 'powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystemProduct).UUID"';
            ob_start();
            @system($command);
            $output = ob_get_clean();
            $uuid = trim((string)$output);

            if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid)) {
                return strtoupper($uuid);
            }
        }

        return null;
    }

    public function generateValidKey(?string $deviceId): ?string
    {
        if (empty($deviceId)) {
            return null;
        }
        return hash_hmac('sha256', $deviceId, $this->secretSalt);
    }

    public function isLicensed(): bool
    {
        $deviceId = $this->getDeviceUUID();
        $validSerial = $this->generateValidKey($deviceId);

        if (!$validSerial) {
            return false;
        }

        if (file_exists($this->licenseFilePath)) {
            $currentLicense = trim((string)@file_get_contents($this->licenseFilePath));
            return hash_equals($validSerial, $currentLicense);
        }

        return false;
    }

    public function activate(string $inputSerial): array
    {
        $deviceId = $this->getDeviceUUID();
        $validSerial = $this->generateValidKey($deviceId);

        if (!$validSerial) {
            return [
                'success' => false,
                'message' => 'Unable to detect Device ID on this system.'
            ];
        }

        $inputSerial = trim($inputSerial);

        if (!hash_equals($validSerial, $inputSerial)) {
            return [
                'success' => false,
                'message' => 'The serial number you entered is not valid for this device!'
            ];
        }

        $dir = dirname($this->licenseFilePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (@file_put_contents($this->licenseFilePath, $inputSerial) !== false) {
            return [
                'success' => true,
                'message' => 'License activated successfully!'
            ];
        }

        return [
            'success' => false,
            'message' => "Failed to save license file at {$this->licenseFilePath}. Please ensure the directory has write permissions."
        ];
    }

    public function getStatus(): array
    {
        $deviceId = $this->getDeviceUUID();
        $validSerial = $this->generateValidKey($deviceId);
        $isLicensed = $this->isLicensed();

        return [
            'is_licensed' => $isLicensed,
            'device_id' => $deviceId,
            'valid_key' => $validSerial,
            'file_path' => $this->licenseFilePath,
        ];
    }
}
