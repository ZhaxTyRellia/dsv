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
    private const DEFAULT_PUBLIC_KEY_PEM = <<<'PEM'
-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEa4EhCDAJbrR3FrB883Q7cy/jP0Y+
dOOKynJavjU3HKpOBkr/xaPCGJTwm0wiBNTcnboSDFz6SXgsI6b21XwTmg==
-----END PUBLIC KEY-----
PEM;

    private array $dbSettings;
    private string $publicKeyPem;
    private string $publicKeyFingerprint;
    private string $runtimeNonce;
    private ?PDO $kernelPdo = null;
    private ?string $cachedDeviceId = null;
    private ?string $cachedPlatform = null;
    private bool $kernelBooted = false;
    private string $runtimeSeal = '';

    public function __construct(?array $dbSettings = null, ?string $publicKeyPem = null)
    {
        $this->publicKeyPem = trim((string)($publicKeyPem ?: self::DEFAULT_PUBLIC_KEY_PEM));
        $this->publicKeyFingerprint = hash('sha256', $this->publicKeyPem);
        $this->runtimeNonce = bin2hex(random_bytes(32));

        if ($dbSettings !== null && !empty($dbSettings['path'])) {
            $this->dbSettings = $dbSettings;
        } else {
            $this->dbSettings = [
                'driver' => 'sqlite',
                'path' => dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'dsv.db',
                'busy_timeout' => 5000,
                'flags' => [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ];
        }
    }

    public function detectBasePath(?string $configuredBasePath = null, ?array $server = null): string
    {
        $configured = trim((string)($configuredBasePath ?? ''));
        if ($configured !== '' && strtolower($configured) !== 'auto') {
            $normalized = '/' . trim(str_replace('\\', '/', $configured), '/');
            return $normalized === '/' ? '' : $normalized;
        }

        $srv = $server ?? $_SERVER;
        $scriptName = str_replace('\\', '/', (string)($srv['SCRIPT_NAME'] ?? ''));
        $requestUriPath = (string)parse_url((string)($srv['REQUEST_URI'] ?? ''), PHP_URL_PATH);

        if ($scriptName === '' || !str_starts_with($scriptName, '/')) {
            return '';
        }

        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($scriptDir === '' || $scriptDir === '.') {
            return '';
        }

        // When root .htaccess rewrites /<base>/route -> /<base>/public/index.php,
        // strip trailing /public unless the actual request URI explicitly starts with /public.
        if (preg_match('#^(.*)/public$#', $scriptDir, $matches) === 1) {
            $parentDir = $matches[1];
            $startsWithPublic = ($requestUriPath === $scriptDir || str_starts_with($requestUriPath, $scriptDir . '/'));
            if (!$startsWithPublic) {
                $scriptDir = $parentDir;
            }
        }

        // Preserve exact URL casing from REQUEST_URI when case-insensitive prefix matches (Windows)
        if ($scriptDir !== '' && $requestUriPath !== '') {
            $len = strlen($scriptDir);
            if (strncasecmp($requestUriPath, $scriptDir, $len) === 0) {
                $nextChar = $requestUriPath[$len] ?? '';
                if ($nextChar === '' || $nextChar === '/') {
                    return substr($requestUriPath, 0, $len);
                }
            }
        }

        return $scriptDir === '/' ? '' : $scriptDir;
    }

    /**
     * Core Bootstrap: Initializes Slim BasePath, RouteParser, View Globals, and Security Middleware.
     */
    public function bootKernel(App $app, ContainerInterface $container): void
    {
        $settings = $container->has('settings') ? (array)$container->get('settings') : [];
        if ($this->kernelPdo === null && !empty($settings['db']['path'])) {
            $this->dbSettings = (array)$settings['db'];
        }

        $resolvedBasePath = $this->detectBasePath($settings['app']['base_path'] ?? 'auto');
        $app->setBasePath($resolvedBasePath);

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
        $record = $this->getStoredLicenseRecord($this->getKernelPdo());
        $storedKey = trim((string)($record['license_key'] ?? ''));
        $this->runtimeSeal = hash_hmac('sha256', $deviceId . '::KERNEL_SEAL::' . $storedKey, $this->runtimeNonce);
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
        if (!$deviceId || !$this->verifyStoredLicense(false)) {
            throw new RuntimeException('Fatal System Error: Hardware license key is missing, invalid, or mismatched in SQLite identity table.');
        }

        $record = $this->getStoredLicenseRecord($this->getKernelPdo());
        $storedKey = trim((string)($record['license_key'] ?? ''));
        $expectedSeal = hash_hmac('sha256', $deviceId . '::KERNEL_SEAL::' . $storedKey, $this->runtimeNonce);
        if (!hash_equals($expectedSeal, $this->runtimeSeal)) {
            throw new RuntimeException('Fatal System Error: Runtime kernel seal integrity compromised.');
        }
    }

    /**
     * Core Database Factory: Unlocks and creates the SQLite PDO connection only when the kernel is authorized.
     * Automatically runs PHP schema migration & initial seed if database/tables do not exist yet, and skips if present.
     */
    public function createDatabaseConnection(array $dbSettings): PDO
    {
        $this->assertKernelAuthorized();

        $requestedPath = trim((string)($dbSettings['path'] ?? ''));
        $kernelPath = trim((string)($this->dbSettings['path'] ?? ''));

        if ($this->kernelPdo !== null && $requestedPath !== '' && $requestedPath === $kernelPath) {
            $pdo = $this->kernelPdo;
        } else {
            $pdo = $this->openSqliteConnection($dbSettings);
        }

        $requiredTables = [
            'identity', 'license_logs', 'titles', 'valas', 'videos',
            'deposit_1', 'deposit_2', 'deposit_3', 'deposit_4',
            'deposit_5', 'deposit_6', 'deposit_7', 'deposit_8',
        ];
        $placeholders = implode(',', array_fill(0, count($requiredTables), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name IN ({$placeholders})");
        $stmt->execute($requiredTables);
        $existingCount = (int)$stmt->fetchColumn();

        if ($existingCount < count($requiredTables)) {
            $this->migrateAndSeedDatabase($pdo, false);
        } else {
            $this->ensureIdentityLicenseColumns($pdo);
        }

        return $pdo;
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
     * Native PHP SQLite Database Migration & Seeder (Zero external .sql dependency).
     */
    private function migrateAndSeedDatabase(PDO $pdo, bool $truncateAll = false): bool
    {
        $pdo->exec('PRAGMA foreign_keys = OFF');

        $startedTransaction = !$pdo->inTransaction();
        if ($startedTransaction) {
            $pdo->beginTransaction();
        }

        try {
            // 1. Migrate deposit_1 through deposit_8 tables
            for ($i = 1; $i <= 8; $i++) {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS \"deposit_{$i}\" (
                        \"id\" INTEGER PRIMARY KEY AUTOINCREMENT,
                        \"tenor\" TEXT NOT NULL,
                        \"interest_rate\" REAL NOT NULL DEFAULT 0.00,
                        \"min_amount\" REAL DEFAULT 0.00,
                        \"description\" TEXT DEFAULT NULL
                    )
                ");

                if ($truncateAll) {
                    $pdo->exec("DELETE FROM \"deposit_{$i}\"");
                }
            }

            // 2. Migrate titles table
            $pdo->exec('
                CREATE TABLE IF NOT EXISTS "titles" (
                    "id_information" INTEGER PRIMARY KEY AUTOINCREMENT,
                    "title_1" TEXT DEFAULT NULL,
                    "title_2" TEXT DEFAULT NULL,
                    "title_3" TEXT DEFAULT NULL,
                    "title_4" TEXT DEFAULT NULL,
                    "title_5" TEXT DEFAULT NULL,
                    "title_6" TEXT DEFAULT NULL,
                    "title_7" TEXT DEFAULT NULL,
                    "title_8" TEXT DEFAULT NULL,
                    "title_9" TEXT DEFAULT NULL
                )
            ');

            // 3. Migrate valas table
            $pdo->exec('
                CREATE TABLE IF NOT EXISTS "valas" (
                    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
                    "currency_code" TEXT NOT NULL,
                    "currency_name" TEXT NOT NULL,
                    "flag_image" TEXT DEFAULT NULL,
                    "buy_rate" REAL DEFAULT 0.00,
                    "sell_rate" REAL DEFAULT 0.00
                )
            ');

            // 4. Migrate videos table
            $pdo->exec('
                CREATE TABLE IF NOT EXISTS "videos" (
                    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
                    "file_name" TEXT NOT NULL,
                    "create_at" TEXT DEFAULT (datetime(\'now\', \'localtime\'))
                )
            ');

            // 5. Ensure identity (with license columns) & license_logs tables exist
            $this->createKernelTablesSql($pdo);
            $this->ensureIdentityLicenseColumns($pdo);

            if ($truncateAll) {
                $pdo->exec('DELETE FROM "titles"');
                $pdo->exec('DELETE FROM "valas"');
                $pdo->exec('DELETE FROM "videos"');

                // Preserve license_* columns on identity row id=1 while resetting display fields
                $pdo->exec('DELETE FROM "identity" WHERE "id" != 1');
                $hasPrimaryIdentity = $pdo->query('SELECT 1 FROM "identity" WHERE "id" = 1 LIMIT 1')->fetchColumn();
                if ($hasPrimaryIdentity !== false) {
                    $resetStmt = $pdo->prepare('
                        UPDATE "identity"
                        SET "logo" = :logo,
                            "title" = :title,
                            "name" = :name,
                            "marquee" = :marquee,
                            "options" = :options,
                            "themes" = :themes
                        WHERE "id" = 1
                    ');
                    $resetStmt->execute([
                        ':logo' => 'default.png',
                        ':title' => 'Display Signage',
                        ':name' => 'Sub Title / Unit Name',
                        ':marquee' => 'Selamat Datang di Layanan Informasi Digital',
                        ':options' => '1',
                        ':themes' => 'show-0.css',
                    ]);
                } else {
                    $this->seedDefaultIdentity($pdo);
                }

                $hasSequence = $pdo->query("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'sqlite_sequence' LIMIT 1")->fetchColumn();
                if ($hasSequence !== false) {
                    $pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('deposit_1','deposit_2','deposit_3','deposit_4','deposit_5','deposit_6','deposit_7','deposit_8','titles','valas','videos')");
                }
            } else {
                // 6. Seed default identity record if table is empty
                $this->seedDefaultIdentity($pdo);
            }

            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->commit();
            }
        } catch (\Throwable $e) {
            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        } finally {
            $pdo->exec('PRAGMA foreign_keys = ON');
        }

        if ($truncateAll && !$pdo->inTransaction()) {
            try {
                $pdo->exec('VACUUM');
            } catch (\PDOException $e) {
                // Ignore if VACUUM is not supported or locked on current environment
            }
        }

        return true;
    }

    private function openSqliteConnection(array $dbSettings): PDO
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new RuntimeException('PDO SQLite driver (pdo_sqlite) is not enabled in this PHP environment.');
        }

        $dbPath = trim((string)($dbSettings['path'] ?? ''));
        if ($dbPath === '') {
            throw new RuntimeException('SQLite database path is not configured.');
        }

        $flags = (array)($dbSettings['flags'] ?? []);
        $busyTimeout = max(1000, (int)($dbSettings['busy_timeout'] ?? 5000));

        if ($dbPath !== ':memory:') {
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir) && !@mkdir($dbDir, 0775, true) && !is_dir($dbDir)) {
                throw new RuntimeException("Unable to create SQLite database directory: {$dbDir}");
            }
        }

        $dsn = ($dbPath === ':memory:') ? 'sqlite::memory:' : 'sqlite:' . $dbPath;
        $pdo = new PDO($dsn, null, null, $flags);

        $pdo->exec("PRAGMA busy_timeout = {$busyTimeout}");
        $pdo->exec('PRAGMA foreign_keys = ON');

        if ($dbPath !== ':memory:') {
            try {
                $journalMode = strtolower((string)$pdo->query('PRAGMA journal_mode = WAL')->fetchColumn());
                if ($journalMode === 'wal') {
                    $pdo->exec('PRAGMA synchronous = NORMAL');
                } else {
                    $pdo->exec('PRAGMA synchronous = FULL');
                }
            } catch (\PDOException $e) {
                try {
                    $pdo->exec('PRAGMA journal_mode = DELETE');
                    $pdo->exec('PRAGMA synchronous = FULL');
                } catch (\PDOException $fallbackException) {
                    // Ignore if filesystem does not permit changing journal mode
                }
            }
        }

        return $pdo;
    }

    private function getKernelPdo(): PDO
    {
        if ($this->kernelPdo === null) {
            $pdo = $this->openSqliteConnection($this->dbSettings);
            $this->ensureKernelTables($pdo);
            $this->kernelPdo = $pdo;
        }

        return $this->kernelPdo;
    }

    private function ensureKernelTables(PDO $pdo): void
    {
        $this->createKernelTablesSql($pdo);
        $this->ensureIdentityLicenseColumns($pdo);
        $this->seedDefaultIdentity($pdo);
    }

    private function createKernelTablesSql(PDO $pdo): void
    {
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS "identity" (
                "id" INTEGER PRIMARY KEY AUTOINCREMENT,
                "logo" TEXT DEFAULT NULL,
                "title" TEXT DEFAULT NULL,
                "name" TEXT DEFAULT NULL,
                "marquee" TEXT DEFAULT NULL,
                "options" TEXT DEFAULT \'1\',
                "themes" TEXT DEFAULT \'show-0.css\',
                "license_key" TEXT DEFAULT NULL,
                "license_device_id" TEXT DEFAULT NULL,
                "license_platform" TEXT DEFAULT NULL,
                "license_activated_at" TEXT DEFAULT NULL,
                "license_integrity_hash" TEXT DEFAULT NULL
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS "license_logs" (
                "id" INTEGER PRIMARY KEY AUTOINCREMENT,
                "event_type" TEXT NOT NULL,
                "device_id" TEXT DEFAULT NULL,
                "platform" TEXT NOT NULL DEFAULT \'unknown\',
                "status" TEXT NOT NULL,
                "message" TEXT DEFAULT NULL,
                "ip_address" TEXT DEFAULT NULL,
                "created_at" TEXT DEFAULT (datetime(\'now\', \'localtime\'))
            )
        ');
    }

    private function ensureIdentityLicenseColumns(PDO $pdo): void
    {
        $columns = array_column($pdo->query('PRAGMA table_info("identity")')->fetchAll(), 'name');
        $licenseColumns = [
            'license_key',
            'license_device_id',
            'license_platform',
            'license_activated_at',
            'license_integrity_hash',
        ];

        foreach ($licenseColumns as $col) {
            if (!in_array($col, $columns, true)) {
                $pdo->exec("ALTER TABLE \"identity\" ADD COLUMN \"{$col}\" TEXT DEFAULT NULL");
            }
        }
    }

    private function seedDefaultIdentity(PDO $pdo): void
    {
        $identityCount = (int)$pdo->query('SELECT COUNT(*) FROM "identity"')->fetchColumn();
        if ($identityCount === 0) {
            $stmt = $pdo->prepare('
                INSERT INTO "identity" ("id", "logo", "title", "name", "marquee", "options", "themes")
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
    }

    private function getStoredLicenseRecord(PDO $pdo): ?array
    {
        $stmt = $pdo->query('
            SELECT "license_key", "license_device_id", "license_platform", "license_activated_at", "license_integrity_hash"
            FROM "identity"
            ORDER BY "id" ASC
            LIMIT 1
        ');
        $row = $stmt ? $stmt->fetch() : false;
        return $row ?: null;
    }

    private function computeIntegrityHash(string $deviceId, string $licenseKey, string $platform): string
    {
        return hash('sha256', $deviceId . '::' . $licenseKey . '::' . $platform . '::' . $this->publicKeyFingerprint . '::LICENSE_RECORD');
    }

    private function recordLicenseLog(
        PDO $pdo,
        string $eventType,
        ?string $deviceId,
        string $platform,
        string $status,
        string $message
    ): void {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : null;

        $stmt = $pdo->prepare('
            INSERT INTO "license_logs" ("event_type", "device_id", "platform", "status", "message", "ip_address", "created_at")
            VALUES (:event_type, :device_id, :platform, :status, :message, :ip_address, datetime(\'now\', \'localtime\'))
        ');

        $stmt->execute([
            ':event_type' => $eventType,
            ':device_id' => $deviceId,
            ':platform' => $platform,
            ':status' => $status,
            ':message' => $message,
            ':ip_address' => $ip,
        ]);
    }

    private function recordMismatchIfNotRecent(PDO $pdo, ?string $deviceId, string $platform, string $message): void
    {
        try {
            $stmt = $pdo->prepare('
                SELECT 1 FROM "license_logs"
                WHERE "event_type" = \'device_mismatch\'
                  AND COALESCE("device_id", \'\') = :device_id
                  AND "created_at" >= datetime(\'now\', \'localtime\', \'-60 seconds\')
                LIMIT 1
            ');
            $stmt->execute([':device_id' => $deviceId ?? '']);

            if ($stmt->fetchColumn() !== false) {
                return;
            }

            $this->recordLicenseLog($pdo, 'device_mismatch', $deviceId, $platform, 'rejected', $message);
        } catch (\Throwable $e) {
            // Do not break license check if logging fails
        }
    }

    private function readProcessEnvValue(string $key): string
    {
        $val = getenv($key);
        if ($val !== false && trim((string)$val) !== '') {
            return trim((string)$val);
        }

        // Allow $_SERVER (e.g., Apache PassEnv) only when not injected via .env ($_ENV)
        if (isset($_SERVER[$key]) && !array_key_exists($key, $_ENV) && trim((string)$_SERVER[$key]) !== '') {
            return trim((string)$_SERVER[$key]);
        }

        return '';
    }

    private function resolveKioSignDeviceId(): ?string
    {
        $candidateKeys = [
            'ANDROID_HARDWARE_UUID',
            'ANDROID_DEVICE_ID',
            'KIOSIGN_DEVICE_ID',
            'ANDROID_RAW_ID',
            'KIOSIGN_DEVICE_RAW_ID',
        ];

        foreach ($candidateKeys as $key) {
            $rawId = $this->readProcessEnvValue($key);
            if ($rawId === '') {
                continue;
            }

            if (
                preg_match('/^[A-Za-z0-9][A-Za-z0-9\-_]{7,63}$/', $rawId) === 1 &&
                preg_match('/^[0\-_]+$/', $rawId) !== 1 &&
                strtoupper($rawId) !== 'UNKNOWN'
            ) {
                return strtoupper($rawId);
            }
        }

        return null;
    }

    private function verifyStoredLicense(bool $logMismatch = false): bool
    {
        $deviceId = $this->getDeviceUUID();
        $platform = $this->getPlatform();

        $pdo = $this->getKernelPdo();
        $record = $this->getStoredLicenseRecord($pdo);

        $storedKey = trim((string)($record['license_key'] ?? ''));
        $storedDeviceId = trim((string)($record['license_device_id'] ?? ''));
        $storedPlatform = trim((string)($record['license_platform'] ?? ''));
        $storedIntegrity = trim((string)($record['license_integrity_hash'] ?? ''));

        if ($storedKey === '') {
            return false;
        }

        if (!$deviceId) {
            if ($logMismatch) {
                $this->recordMismatchIfNotRecent(
                    $pdo,
                    $deviceId,
                    $platform,
                    'Stored license exists but current Device ID could not be detected.'
                );
            }
            return false;
        }

        $keyValid = $this->verifyActivateKey($deviceId, $storedKey);
        $deviceMatches = $storedDeviceId !== '' && hash_equals($deviceId, $storedDeviceId);
        $integrityMatches = $storedPlatform !== ''
            && $storedIntegrity !== ''
            && hash_equals($this->computeIntegrityHash($storedDeviceId, $storedKey, $storedPlatform), $storedIntegrity);

        if (!$keyValid || !$deviceMatches || !$integrityMatches) {
            if ($logMismatch) {
                $this->recordMismatchIfNotRecent(
                    $pdo,
                    $deviceId,
                    $platform,
                    'Stored SQLite license does not match current device or integrity check failed.'
                );
            }
            return false;
        }

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
            // 1. Windows motherboard/system UUID via PowerShell CIM
            $command = 'powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystemProduct).UUID"';
            ob_start();
            @system($command);
            $output = ob_get_clean();
            $uuid = trim((string)$output);

            if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid)) {
                $this->cachedDeviceId = strtoupper($uuid);
                $this->cachedPlatform = 'windows';
                return $this->cachedDeviceId;
            }
        } else {
            // 2. Raw KioSign Device ID via Apache/OS process environment (Android)
            $kioId = $this->resolveKioSignDeviceId();
            if ($kioId !== null) {
                $this->cachedDeviceId = $kioId;
                $this->cachedPlatform = 'android_kiosign';
                return $this->cachedDeviceId;
            }

            // 3. Linux read-only hardware/machine identifiers
            $candidateFiles = [
                '/sys/class/dmi/id/product_uuid',
                '/etc/machine-id',
                '/var/lib/dbus/machine-id',
                '/proc/device-tree/serial-number',
            ];

            foreach ($candidateFiles as $file) {
                if (!@is_readable($file)) {
                    continue;
                }

                $raw = str_replace("\0", '', trim((string)@file_get_contents($file)));
                if (
                    $raw === '' ||
                    $raw === '00000000-0000-0000-0000-000000000000' ||
                    $raw === '00000000000000000000000000000000'
                ) {
                    continue;
                }

                if (preg_match('/^[0-9a-fA-F]{32}$/', $raw) === 1) {
                    $formatted = sprintf(
                        '%s-%s-%s-%s-%s',
                        substr($raw, 0, 8),
                        substr($raw, 8, 4),
                        substr($raw, 12, 4),
                        substr($raw, 16, 4),
                        substr($raw, 20, 12)
                    );
                    $this->cachedDeviceId = strtoupper($formatted);
                    $this->cachedPlatform = 'linux';
                    return $this->cachedDeviceId;
                }

                if (preg_match('/^[A-Za-z0-9\-_]{8,64}$/', $raw) === 1) {
                    $this->cachedDeviceId = strtoupper($raw);
                    $this->cachedPlatform = 'linux';
                    return $this->cachedDeviceId;
                }
            }
        }

        $this->cachedPlatform = 'unknown';
        return null;
    }

    public function getPlatform(): string
    {
        if ($this->cachedPlatform === null) {
            $this->getDeviceUUID();
        }

        return $this->cachedPlatform ?? 'unknown';
    }

    public function normalizeDeviceId(string $deviceId): string
    {
        return strtoupper(trim($deviceId));
    }

    private function decodeActivateKey(string $activateKey): ?string
    {
        $trimmed = trim($activateKey);
        if ($trimmed === '' || preg_match('/^[A-Za-z0-9\-_]+$/', $trimmed) !== 1) {
            return null;
        }

        $remainder = strlen($trimmed) % 4;
        if ($remainder !== 0) {
            $trimmed .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($trimmed, '-_', '+/'), true);
        if ($decoded === false) {
            return null;
        }

        $len = strlen($decoded);
        if ($len < 64 || $len > 128) {
            return null;
        }

        return $decoded;
    }

    public function verifyActivateKey(?string $deviceId, string $activateKey): bool
    {
        if ($deviceId === null || trim($deviceId) === '' || !function_exists('openssl_verify')) {
            return false;
        }

        $rawSignature = $this->decodeActivateKey($activateKey);
        if ($rawSignature === null) {
            return false;
        }

        return @openssl_verify(
            $this->normalizeDeviceId($deviceId),
            $rawSignature,
            $this->publicKeyPem,
            OPENSSL_ALGO_SHA256
        ) === 1;
    }

    public function isLicensed(): bool
    {
        try {
            return $this->verifyStoredLicense(true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function activate(string $inputSerial): array
    {
        $deviceId = $this->getDeviceUUID();
        $platform = $this->getPlatform();

        try {
            $pdo = $this->getKernelPdo();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to initialize SQLite license storage: ' . $e->getMessage(),
            ];
        }

        if (!$deviceId) {
            try {
                $this->recordLicenseLog(
                    $pdo,
                    'activate',
                    $deviceId,
                    $platform,
                    'failed',
                    'Unable to detect Device ID on this system.'
                );
            } catch (\Throwable $e) {
                // Ignore logging error
            }

            return [
                'success' => false,
                'message' => 'Unable to detect Device ID on this system.',
            ];
        }

        $inputKey = trim($inputSerial);

        if ($inputKey === '' || !$this->verifyActivateKey($deviceId, $inputKey)) {
            try {
                $this->recordLicenseLog(
                    $pdo,
                    'activate',
                    $deviceId,
                    $platform,
                    'failed',
                    'Invalid Activate Key submitted for this device.'
                );
            } catch (\Throwable $e) {
                // Ignore logging error
            }

            return [
                'success' => false,
                'message' => 'The Activate Key you entered is not valid for this device!',
            ];
        }

        $startedTransaction = !$pdo->inTransaction();

        try {
            if ($startedTransaction) {
                $pdo->beginTransaction();
            }

            $this->seedDefaultIdentity($pdo);

            $activatedAt = date('Y-m-d H:i:s');
            $integrityHash = $this->computeIntegrityHash($deviceId, $inputKey, $platform);

            $stmt = $pdo->prepare('
                UPDATE "identity"
                SET "license_key" = :license_key,
                    "license_device_id" = :license_device_id,
                    "license_platform" = :license_platform,
                    "license_activated_at" = :license_activated_at,
                    "license_integrity_hash" = :license_integrity_hash
                WHERE "id" = (SELECT "id" FROM "identity" ORDER BY "id" ASC LIMIT 1)
            ');
            $stmt->execute([
                ':license_key' => $inputKey,
                ':license_device_id' => $deviceId,
                ':license_platform' => $platform,
                ':license_activated_at' => $activatedAt,
                ':license_integrity_hash' => $integrityHash,
            ]);

            $this->recordLicenseLog(
                $pdo,
                'activate',
                $deviceId,
                $platform,
                'success',
                'License activated and saved to SQLite identity table.'
            );

            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->commit();
            }

            return [
                'success' => true,
                'message' => 'License activated successfully!',
            ];
        } catch (\Throwable $e) {
            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Failed to save license to SQLite database: ' . $e->getMessage(),
            ];
        }
    }

    public function getStatus(): array
    {
        $deviceId = $this->getDeviceUUID();
        $platform = $this->getPlatform();
        $isLicensed = $this->isLicensed();

        $activatedAt = null;
        try {
            $record = $this->getStoredLicenseRecord($this->getKernelPdo());
            $activatedAt = isset($record['license_activated_at']) ? (string)$record['license_activated_at'] : null;
        } catch (\Throwable $e) {
            $activatedAt = null;
        }

        return [
            'is_licensed' => $isLicensed,
            'device_id' => $deviceId,
            'platform' => $platform,
            'activated_at' => $activatedAt,
            'storage' => 'sqlite:identity',
            'db_path' => (string)($this->dbSettings['path'] ?? ''),
        ];
    }
}
