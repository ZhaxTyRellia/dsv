<?php

declare(strict_types=1);

namespace App\Domain\License;

use App\Middleware\LicenseMiddleware;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

/**
 * Core System Kernel & Hardware License Engine
 *
 * Acts as the central heart of the application:
 * 1. Boots the Slim application routing parser, base path, template globals, and middleware.
 * 2. Authenticates and constructs the PDO database connection.
 * 3. Guards all Action view rendering, JSON API output, and route URL generation.
 *
 * Removing or bypassing this class will cause the entire application to halt.
 */
class LicenseService
{
    private string $licenseFilePath;
    private string $secretSalt;
    private ?string $cachedDeviceId = null;
    private bool $kernelBooted = false;
    private string $runtimeSeal = '';

    public function __construct(?string $licenseFilePath = null, ?string $secretSalt = null)
    {
        $this->secretSalt = $secretSalt ?: 'GautavarisKey!!!';

        if ($licenseFilePath !== null && $licenseFilePath !== '') {
            $this->licenseFilePath = $licenseFilePath;
        } else {
            $userDir = getenv('USERPROFILE')
                ?: (getenv('HOMEDRIVE') && getenv('HOMEPATH') ? getenv('HOMEDRIVE') . getenv('HOMEPATH') : null)
                ?: (getenv('USERNAME') ? 'C:\\Users\\' . getenv('USERNAME') : null)
                ?: 'C:\\127.0.0.1';

            $this->licenseFilePath = rtrim($userDir, '\\/') . DIRECTORY_SEPARATOR . 'dsv' . DIRECTORY_SEPARATOR . 'license.key';
        }
    }

    /**
     * Core Bootstrap: Initializes Slim BasePath, RouteParser, View Globals, and Security Middleware.
     */
    public function bootKernel(App $app, ContainerInterface $container): void
    {
        $settings = $container->get('settings');
        if (!empty($settings['app']['base_path'])) {
            $app->setBasePath($settings['app']['base_path']);
        }

        $routeParser = $app->getRouteCollector()->getRouteParser();
        if (method_exists($container, 'set')) {
            $container->set(RouteParserInterface::class, $routeParser);
        }

        $renderer = $container->get(PhpRenderer::class);
        $renderer->addAttribute('urlFor', function (string $name, array $data = [], array $queryParams = []) use ($routeParser): string {
            return $this->resolveRouteUrl($routeParser, $name, $data, $queryParams);
        });
        $renderer->addAttribute('basePath', $app->getBasePath());
        $renderer->addAttribute('__kernel', $this);

        $app->add(LicenseMiddleware::class);

        $this->kernelBooted = true;
    }

    /**
     * Authorizes the current HTTP request lifecycle after middleware verification.
     */
    public function authorizeRuntime(): void
    {
        if (!$this->isLicensed()) {
            $this->runtimeSeal = '';
            throw new RuntimeException('Kernel Panic: Cannot authorize runtime on an unlicensed device.');
        }

        $deviceId = (string)$this->getDeviceUUID();
        $validKey = (string)$this->generateValidKey($deviceId);
        $this->runtimeSeal = hash_hmac('sha256', $deviceId . '::KERNEL_SEAL::' . $validKey, $this->secretSalt);
    }

    /**
     * Verifies that the kernel was properly booted, middleware executed, and hardware license is valid.
     */
    public function assertKernelAuthorized(): void
    {
        if (!$this->kernelBooted || $this->runtimeSeal === '') {
            throw new RuntimeException('Fatal System Error: Core kernel security middleware was bypassed or not initialized.');
        }

        $deviceId = $this->getDeviceUUID();
        $validSerial = $this->generateValidKey($deviceId);

        if (!$validSerial || !file_exists($this->licenseFilePath)) {
            throw new RuntimeException('Fatal System Error: Hardware license key is missing or unreadable.');
        }

        $currentLicense = trim((string)@file_get_contents($this->licenseFilePath));
        if (!hash_equals($validSerial, $currentLicense)) {
            throw new RuntimeException('Fatal System Error: Hardware license signature mismatch.');
        }

        $expectedSeal = hash_hmac('sha256', $deviceId . '::KERNEL_SEAL::' . $validSerial, $this->secretSalt);
        if (!hash_equals($expectedSeal, $this->runtimeSeal)) {
            throw new RuntimeException('Fatal System Error: Runtime kernel seal integrity compromised.');
        }
    }

    /**
     * Core Database Factory: Unlocks and creates the PDO connection only when the kernel is authorized.
     * Automatically runs PHP schema migration & initial seed if database/tables do not exist yet, and skips if present.
     */
    public function createDatabaseConnection(array $dbSettings): PDO
    {
        $this->assertKernelAuthorized();

        $host = $dbSettings['host'];
        $port = (int)$dbSettings['port'];
        $dbName = $dbSettings['database'];
        $charset = $dbSettings['charset'];
        $username = $dbSettings['username'];
        $password = $dbSettings['password'];
        $flags = $dbSettings['flags'];

        try {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $charset);
            $pdo = new PDO($dsn, $username, $password, $flags);

            // Check if core table exists; if empty, run PHP migration & seed, otherwise skip
            $check = $pdo->query("SHOW TABLES LIKE 'identity'");
            if ($check === false || $check->fetch() === false) {
                $this->migrateAndSeedDatabase($pdo, false);
            }

            return $pdo;
        } catch (\PDOException $e) {
            // Error 1049 = Unknown database: create database and run PHP migration & seed
            if (
                (int)$e->getCode() === 1049 ||
                str_contains($e->getMessage(), '1049') ||
                str_contains($e->getMessage(), 'Unknown database')
            ) {
                $serverDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $host, $port, $charset);
                $pdo = new PDO($serverDsn, $username, $password, $flags);

                $safeDbName = str_replace('`', '``', $dbName);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeDbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                $pdo->exec("USE `{$safeDbName}`");

                $this->migrateAndSeedDatabase($pdo, false);

                return $pdo;
            }

            throw $e;
        }
    }

    /**
     * Resets the database to factory defaults via native PHP migration & seeder.
     */
    public function resetDatabaseToFactory(PDO $pdo): bool
    {
        $this->assertKernelAuthorized();
        return $this->migrateAndSeedDatabase($pdo, true);
    }

    /**
     * Native PHP Database Migration & Seeder (Zero external .sql dependency).
     */
    private function migrateAndSeedDatabase(PDO $pdo, bool $truncateAll = false): bool
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Migrate deposit_1 through deposit_8 tables
        for ($i = 1; $i <= 8; $i++) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `deposit_{$i}` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `tenor` varchar(100) NOT NULL,
                    `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
                    `min_amount` decimal(15,2) DEFAULT 0.00,
                    `description` text DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            if ($truncateAll) {
                $pdo->exec("TRUNCATE TABLE `deposit_{$i}`");
            }
        }

        // 2. Migrate identity table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `identity` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `logo` varchar(255) DEFAULT NULL,
                `title` varchar(255) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `marquee` text DEFAULT NULL,
                `options` varchar(10) DEFAULT '1',
                `themes` varchar(100) DEFAULT 'show-0.css',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // 3. Migrate titles table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `titles` (
                `id_information` int(11) NOT NULL AUTO_INCREMENT,
                `title_1` varchar(255) DEFAULT NULL,
                `title_2` varchar(255) DEFAULT NULL,
                `title_3` varchar(255) DEFAULT NULL,
                `title_4` varchar(255) DEFAULT NULL,
                `title_5` varchar(255) DEFAULT NULL,
                `title_6` varchar(255) DEFAULT NULL,
                `title_7` varchar(255) DEFAULT NULL,
                `title_8` varchar(255) DEFAULT NULL,
                `title_9` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id_information`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // 4. Migrate valas table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `valas` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `currency_code` varchar(10) NOT NULL,
                `currency_name` varchar(100) NOT NULL,
                `flag_image` varchar(50) DEFAULT NULL,
                `buy_rate` decimal(15,2) DEFAULT 0.00,
                `sell_rate` decimal(15,2) DEFAULT 0.00,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // 5. Migrate videos table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `videos` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `file_name` varchar(255) NOT NULL,
                `create_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        if ($truncateAll) {
            $pdo->exec('TRUNCATE TABLE `titles`');
            $pdo->exec('TRUNCATE TABLE `valas`');
            $pdo->exec('TRUNCATE TABLE `videos`');
            $pdo->exec('TRUNCATE TABLE `identity`');
        }

        // 6. Seed default identity record if table is empty
        $identityCount = (int)$pdo->query('SELECT COUNT(*) FROM `identity`')->fetchColumn();
        if ($identityCount === 0) {
            $stmt = $pdo->prepare('
                INSERT INTO `identity` (`id`, `logo`, `title`, `name`, `marquee`, `options`, `themes`)
                VALUES (:id, :logo, :title, :name, :marquee, :options, :themes)
            ');
            $stmt->execute([
                ':id' => 1,
                ':logo' => 'default.png',
                ':title' => 'Display Signage',
                ':name' => 'Sub Title / Unit Name',
                ':marquee' => 'Selamat Datang di Layanan Informasi Digital',
                ':options' => '1',
                ':themes' => 'show-0.css',
            ]);
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        return true;
    }

    /**
     * Core View Responder: Guards template rendering across all Actions.
     */
    public function renderGuarded(
        PhpRenderer $renderer,
        ResponseInterface $response,
        string $template,
        array $data = []
    ): ResponseInterface {
        if (!$this->kernelBooted) {
            throw new RuntimeException('Fatal System Error: Core kernel not booted.');
        }

        if ($template !== 'license/activate.php') {
            $this->assertKernelAuthorized();
        }

        return $renderer->render($response, $template, $data);
    }

    /**
     * Core JSON Responder: Guards all REST/AJAX API responses.
     */
    public function jsonGuarded(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $this->assertKernelAuthorized();

        $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($payload ?: '{}');

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    /**
     * Core Route URL Resolver: Guards named route generation.
     */
    public function resolveRouteUrl(
        RouteParserInterface $routeParser,
        string $routeName,
        array $data = [],
        array $queryParams = []
    ): string {
        if (!$this->kernelBooted) {
            throw new RuntimeException('Fatal System Error: Core route resolver cannot run without booted kernel.');
        }

        return $routeParser->urlFor($routeName, $data, $queryParams);
    }

    public function getDeviceUUID(): ?string
    {
        if ($this->cachedDeviceId !== null) {
            return $this->cachedDeviceId;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = 'powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystemProduct).UUID"';
            ob_start();
            @system($command);
            $output = ob_get_clean();
            $uuid = trim((string)$output);

            if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid)) {
                $this->cachedDeviceId = strtoupper($uuid);
                return $this->cachedDeviceId;
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
