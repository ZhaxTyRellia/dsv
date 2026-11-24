<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

/**
 * Core Application Runtime, Identity Repository & Offline Hardware Activation Engine
 *
 * Fuses identity/configuration persistence, SQLite schema/migration management,
 * and asymmetric ECDSA P-256 hardware activation into one inseparable core unit.
 */
class IdentityRepository
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
    private ?PDO $internalPdo = null;
    private ?PDO $authorizedPdo = null;
    private ?string $cachedDeviceId = null;
    private ?string $cachedPlatform = null;
    private bool $appInitialized = false;
    private string $runtimeSeal = '';

    public function __construct(?array $dbSettings = null, ?string $publicKeyPem = null)
    {
        $rawPem = (string)($publicKeyPem ?: self::DEFAULT_PUBLIC_KEY_PEM);
        $this->publicKeyPem = str_replace("\r\n", "\n", trim($rawPem));
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

        // Strip trailing /public appended by root .htaccess rewrite unless URI explicitly targets /public
        if (preg_match('#^(.*)/public$#', $scriptDir, $matches) === 1) {
            $parentDir = $matches[1];
            $startsWithPublic = ($requestUriPath === $scriptDir || str_starts_with($requestUriPath, $scriptDir . '/'));
            if (!$startsWithPublic) {
                $scriptDir = $parentDir;
            }
        }

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

    public function initializeApp(App $app, ContainerInterface $container): void
    {
        $settings = $container->has('settings') ? (array)$container->get('settings') : [];
        if ($this->internalPdo === null && !empty($settings['db']['path'])) {
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
            return $this->routeUrl($routeParser, $name, $data, $queryParams);
        });
        $renderer->addAttribute('basePath', $app->getBasePath());
        $renderer->addAttribute('__runtime', $this);

        $this->appInitialized = true;
    }

    public function getPublicKeyPem(): string
    {
        return $this->publicKeyPem;
    }

    /**
     * Issues a per-request HMAC seal bound to the verified ECDSA key and runtime nonce.
     *
     * @return array{seal: string, nonce: string, device_id: string, key: string}
     */
    public function issueRuntimeSeal(): array
    {
        if (!$this->verifyStoredRecord(false)) {
            $this->runtimeSeal = '';
            throw new RuntimeException('Fatal System Error: Cannot issue runtime seal for an unactivated device.');
        }

        $deviceId = (string)$this->getDeviceUUID();
        $record = $this->getStoredLicenseRecord($this->getInternalPdo());
        $storedKey = trim((string)($record['license_key'] ?? ''));
        $seal = hash_hmac(
            'sha256',
            $deviceId . '::RUNTIME_SEAL::' . $storedKey . '::' . $this->publicKeyFingerprint,
            $this->runtimeNonce
        );
        $this->runtimeSeal = $seal;

        return [
            'seal' => $seal,
            'nonce' => $this->runtimeNonce,
            'device_id' => $deviceId,
            'key' => $storedKey,
        ];
    }

    public function assertRuntimeAuthorized(): void
    {
        if (!$this->appInitialized || $this->runtimeSeal === '') {
            throw new RuntimeException('Fatal System Error: Core runtime security middleware was bypassed or not initialized.');
        }

        $deviceId = $this->getDeviceUUID();
        if (!$deviceId || !$this->verifyStoredRecord(false)) {
            throw new RuntimeException('Fatal System Error: Device activation is missing, invalid, or mismatched.');
        }

        $record = $this->getStoredLicenseRecord($this->getInternalPdo());
        $storedKey = trim((string)($record['license_key'] ?? ''));
        $expectedSeal = hash_hmac(
            'sha256',
            $deviceId . '::RUNTIME_SEAL::' . $storedKey . '::' . $this->publicKeyFingerprint,
            $this->runtimeNonce
        );

        if (!hash_equals($expectedSeal, $this->runtimeSeal)) {
            throw new RuntimeException('Fatal System Error: Runtime seal integrity verification failed.');
        }
    }

    public function openAuthorizedDatabase(array $dbSettings): PDO
    {
        $this->assertRuntimeAuthorized();

        $requestedPath = trim((string)($dbSettings['path'] ?? ''));
        $internalPath = trim((string)($this->dbSettings['path'] ?? ''));

        if ($this->authorizedPdo !== null && $requestedPath !== '' && $requestedPath === $internalPath) {
            return $this->authorizedPdo;
        }

        if ($this->internalPdo !== null && $requestedPath !== '' && $requestedPath === $internalPath) {
            $pdo = $this->internalPdo;
        } else {
            $pdo = $this->openSqliteConnection($dbSettings);
        }

        $requiredTables = [
            'identity', 'titles', 'valas', 'videos',
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

        if ($requestedPath !== '' && $requestedPath === $internalPath) {
            $this->authorizedPdo = $pdo;
        }

        return $pdo;
    }

    public function resetDatabaseToFactory(): bool
    {
        $this->assertRuntimeAuthorized();
        return $this->migrateAndSeedDatabase($this->getInternalPdo(), true);
    }

    public function get(): ?array
    {
        $this->assertRuntimeAuthorized();
        $pdo = $this->openAuthorizedDatabase($this->dbSettings);
        $stmt = $pdo->prepare('SELECT "id", "logo", "title", "name", "marquee", "options", "themes" FROM "identity" ORDER BY "id" ASC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(string $logo, string $title, string $name, string $marquee, int $id = 1): bool
    {
        $this->assertRuntimeAuthorized();
        $pdo = $this->openAuthorizedDatabase($this->dbSettings);
        $stmt = $pdo->prepare(
            'UPDATE "identity" SET "logo" = :logo, "title" = :title, "name" = :name, "marquee" = :marquee WHERE "id" = :id'
        );
        return $stmt->execute([
            ':logo' => $logo,
            ':title' => $title,
            ':name' => $name,
            ':marquee' => $marquee,
            ':id' => $id,
        ]);
    }

    public function updateOption(string $option, int $id = 1): bool
    {
        $this->assertRuntimeAuthorized();
        $pdo = $this->openAuthorizedDatabase($this->dbSettings);
        $stmt = $pdo->prepare('UPDATE "identity" SET "options" = :options WHERE "id" = :id');
        return $stmt->execute([
            ':options' => $option,
            ':id' => $id,
        ]);
    }

    public function updateTheme(string $theme, int $id = 1): bool
    {
        $this->assertRuntimeAuthorized();
        $pdo = $this->openAuthorizedDatabase($this->dbSettings);
        $stmt = $pdo->prepare('UPDATE "identity" SET "themes" = :themes WHERE "id" = :id');
        return $stmt->execute([
            ':themes' => $theme,
            ':id' => $id,
        ]);
    }

    private function migrateAndSeedDatabase(PDO $pdo, bool $truncateAll = false): bool
    {
        $pdo->exec('PRAGMA foreign_keys = OFF');

        $startedTransaction = !$pdo->inTransaction();
        if ($startedTransaction) {
            $pdo->beginTransaction();
        }

        try {
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

            $pdo->exec('
                CREATE TABLE IF NOT EXISTS "videos" (
                    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
                    "file_name" TEXT NOT NULL,
                    "create_at" TEXT DEFAULT (datetime(\'now\', \'localtime\'))
                )
            ');

            $this->createKernelTablesSql($pdo);
            $this->ensureIdentityLicenseColumns($pdo);

            if ($truncateAll) {
                $pdo->exec('DELETE FROM "titles"');
                $pdo->exec('DELETE FROM "valas"');
                $pdo->exec('DELETE FROM "videos"');

                // Preserve license_* columns on identity row id=1 on factory reset
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
                // Ignore if VACUUM is not permitted on current filesystem
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

    private function getInternalPdo(): PDO
    {
        if ($this->internalPdo === null) {
            $pdo = $this->openSqliteConnection($this->dbSettings);
            $this->ensureKernelSchema($pdo);
            $this->internalPdo = $pdo;
        }

        return $this->internalPdo;
    }

    private function ensureKernelSchema(PDO $pdo): void
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

        $pdo->exec('DROP TABLE IF EXISTS "license_logs"');
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

    private function readProcessEnvValue(string $key): string
    {
        $val = getenv($key);
        if ($val !== false && trim((string)$val) !== '') {
            return trim((string)$val);
        }

        if (isset($_SERVER[$key]) && !array_key_exists($key, $_ENV) && trim((string)$_SERVER[$key]) !== '') {
            return trim((string)$_SERVER[$key]);
        }

        return '';
    }

    private function resolveEnvironmentDeviceId(): ?string
    {
        $candidateKeys = [
            'DEVICE_UUID',
            'HARDWARE_UUID',
            'SERVER_HARDWARE_UUID',
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

    private function formatHashAsUuid(string $seed): string
    {
        $hex = hash('sha256', $seed);
        return strtoupper(sprintf(
            '%s-%s-4%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 13, 3),
            dechex(0x8 | (hexdec($hex[16]) & 0x3)) . substr($hex, 17, 3),
            substr($hex, 20, 12)
        ));
    }

    private function runShellCommand(string $command): string
    {
        if (!function_exists('system')) {
            return '';
        }

        ob_start();
        @system($command);
        return trim((string)ob_get_clean());
    }

    private function verifyStoredRecord(bool $logMismatch = false): bool
    {
        $deviceId = $this->getDeviceUUID();
        $pdo = $this->getInternalPdo();
        $record = $this->getStoredLicenseRecord($pdo);

        $storedKey = trim((string)($record['license_key'] ?? ''));
        $storedDeviceId = trim((string)($record['license_device_id'] ?? ''));
        $storedPlatform = trim((string)($record['license_platform'] ?? ''));
        $storedIntegrity = trim((string)($record['license_integrity_hash'] ?? ''));

        if ($storedKey === '' || !$deviceId) {
            return false;
        }

        $keyValid = $this->verifyActivateKey($deviceId, $storedKey);
        $deviceMatches = $storedDeviceId !== '' && hash_equals($deviceId, $storedDeviceId);
        $integrityMatches = $storedPlatform !== ''
            && $storedIntegrity !== ''
            && hash_equals($this->computeIntegrityHash($storedDeviceId, $storedKey, $storedPlatform), $storedIntegrity);

        return $keyValid && $deviceMatches && $integrityMatches;
    }

    public function renderView(
        PhpRenderer $renderer,
        ResponseInterface $response,
        string $template,
        array $data = []
    ): ResponseInterface {
        if (!$this->appInitialized) {
            throw new RuntimeException('Fatal System Error: Core application runtime not initialized.');
        }

        if ($template !== 'dashboard/activate.php') {
            $this->assertRuntimeAuthorized();
        }

        return $renderer->render($response, $template, $data);
    }

    public function sendJson(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $this->assertRuntimeAuthorized();

        $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($payload ?: '{}');

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function routeUrl(
        RouteParserInterface $routeParser,
        string $routeName,
        array $data = [],
        array $queryParams = []
    ): string {
        if (!$this->appInitialized) {
            throw new RuntimeException('Fatal System Error: Route resolver cannot run without initialized runtime.');
        }

        return $routeParser->urlFor($routeName, $data, $queryParams);
    }

    public function getDeviceUUID(): ?string
    {
        if ($this->cachedDeviceId !== null) {
            return $this->cachedDeviceId;
        }

        $osFamily = strtoupper(PHP_OS_FAMILY ?: substr(PHP_OS, 0, 3));

        if (str_starts_with($osFamily, 'WIN')) {
            $this->cachedPlatform = 'windows';

            // 1. Windows CIM motherboard UUID via PowerShell
            $uuid = $this->runShellCommand('powershell -NoProfile -Command "(Get-CimInstance Win32_ComputerSystemProduct).UUID"');
            if (
                preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid) === 1 &&
                strtoupper($uuid) !== 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF' &&
                strtoupper($uuid) !== '00000000-0000-0000-0000-000000000000'
            ) {
                $this->cachedDeviceId = strtoupper($uuid);
                return $this->cachedDeviceId;
            }

            // 2. Fallback: Windows Cryptography MachineGuid via Registry
            $regOut = $this->runShellCommand('reg query "HKLM\\SOFTWARE\\Microsoft\\Cryptography" /v MachineGuid 2>NUL');
            if (preg_match('/([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})/', $regOut, $m) === 1) {
                $this->cachedDeviceId = strtoupper($m[1]);
                return $this->cachedDeviceId;
            }
        } elseif ($osFamily === 'DARWIN') {
            $this->cachedPlatform = 'macos';
            $ioOut = $this->runShellCommand('ioreg -rd1 -c IOPlatformExpertDevice');
            if (preg_match('/"IOPlatformUUID"\s*=\s*"([^"]+)"/', $ioOut, $m) === 1) {
                $this->cachedDeviceId = strtoupper(trim($m[1]));
                return $this->cachedDeviceId;
            }
        } else {
            $isAndroid = @is_file('/system/build.prop') || getenv('ANDROID_ROOT') !== false;
            $this->cachedPlatform = $isAndroid ? 'android' : 'linux';

            // 1. Environment hardware/device identifier (if provided by server container/runtime)
            $envId = $this->resolveEnvironmentDeviceId();
            if ($envId !== null) {
                $this->cachedDeviceId = $envId;
                return $this->cachedDeviceId;
            }

            // 2. Standard Linux / Android read-only hardware & machine identifiers
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
                    return $this->cachedDeviceId;
                }

                if (preg_match('/^[A-Za-z0-9\-_]{8,64}$/', $raw) === 1) {
                    $this->cachedDeviceId = strtoupper($raw);
                    return $this->cachedDeviceId;
                }
            }
        }

        // Universal deterministic server fallback (containers / restricted shared hosts)
        if ($this->cachedPlatform === null) {
            $this->cachedPlatform = 'server';
        }
        $seed = php_uname('s') . '|' . php_uname('n') . '|' . php_uname('m') . '|' . __DIR__;
        $this->cachedDeviceId = $this->formatHashAsUuid($seed);
        return $this->cachedDeviceId;
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

    public function isActivated(): bool
    {
        try {
            return $this->verifyStoredRecord(true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function activate(string $inputKey): array
    {
        $deviceId = $this->getDeviceUUID();
        $platform = $this->getPlatform();

        try {
            $pdo = $this->getInternalPdo();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to initialize SQLite license storage: ' . $e->getMessage(),
            ];
        }

        if (!$deviceId) {
            return [
                'success' => false,
                'message' => 'Unable to detect Device ID on this system.',
            ];
        }

        $inputKey = trim($inputKey);

        if ($inputKey === '' || !$this->verifyActivateKey($deviceId, $inputKey)) {
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

    public function deactivate(): array
    {
        try {
            $pdo = $this->getInternalPdo();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to open SQLite license storage: ' . $e->getMessage(),
            ];
        }

        $startedTransaction = !$pdo->inTransaction();

        try {
            if ($startedTransaction) {
                $pdo->beginTransaction();
            }

            $stmt = $pdo->prepare('
                UPDATE "identity"
                SET "license_key" = NULL,
                    "license_device_id" = NULL,
                    "license_platform" = NULL,
                    "license_activated_at" = NULL,
                    "license_integrity_hash" = NULL
                WHERE "id" = (SELECT "id" FROM "identity" ORDER BY "id" ASC LIMIT 1)
            ');
            $stmt->execute();

            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->commit();
            }

            $this->runtimeSeal = '';
            $this->authorizedPdo = null;

            return [
                'success' => true,
                'message' => 'Device license has been unbound successfully. Please enter an Activate Key to reactivate.',
            ];
        } catch (\Throwable $e) {
            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => 'Failed to unbind device license: ' . $e->getMessage(),
            ];
        }
    }

    public function getStatus(): array
    {
        $deviceId = $this->getDeviceUUID();
        $platform = $this->getPlatform();
        $isLicensed = $this->isActivated();

        $activatedAt = null;
        try {
            $record = $this->getStoredLicenseRecord($this->getInternalPdo());
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
            'public_key_fp' => strtoupper(substr($this->publicKeyFingerprint, 0, 16)),
        ];
    }
}
