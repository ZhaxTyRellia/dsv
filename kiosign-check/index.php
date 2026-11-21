<?php

declare(strict_types=1);

/**
 * KioSign Raw Environment, JS Bridge & DSV Runtime Diagnostic Page
 *
 * Generic, zero-secret checker that inspects raw KioSign/Android variables
 * (ANDROID_HARDWARE_UUID, ANDROID_DEVICE_ID, KIOSIGN_DEVICE_ID, etc.),
 * JavaScript window.AndroidDevice interface, PHP/Apache requirements,
 * storage permissions, and SQLite WAL readiness for DSV and similar projects.
 */

const KIOSIGN_PRIMARY_ID_KEYS = [
    'ANDROID_HARDWARE_UUID',
    'ANDROID_DEVICE_ID',
    'KIOSIGN_DEVICE_ID',
    'ANDROID_RAW_ID',
    'KIOSIGN_DEVICE_RAW_ID',
];

const KIOSIGN_METADATA_KEYS = [
    'ANDROID_MODEL',
    'ANDROID_FINGERPRINT',
];

/**
 * Inspect an environment variable separately across getenv(), $_SERVER, and $_ENV.
 *
 * @return array{
 *     sources: list<string>,
 *     raw_by_source: array<string, ?string>,
 *     process_value: string,
 *     dotenv_value: string,
 *     from_process_env: bool
 * }
 */
function inspectEnvVar(string $key): array
{
    $sources = [];
    $rawBySource = [
        'getenv' => null,
        'server' => null,
        'env' => null,
    ];

    $fromGetenv = getenv($key);
    if ($fromGetenv !== false && trim((string)$fromGetenv) !== '') {
        $sources[] = 'getenv()';
        $rawBySource['getenv'] = trim((string)$fromGetenv);
    }

    if (isset($_SERVER[$key]) && trim((string)$_SERVER[$key]) !== '') {
        $sources[] = '$_SERVER';
        $rawBySource['server'] = trim((string)$_SERVER[$key]);
    }

    if (isset($_ENV[$key]) && trim((string)$_ENV[$key]) !== '') {
        $sources[] = '$_ENV';
        $rawBySource['env'] = trim((string)$_ENV[$key]);
    }

    $hasGetenv = $rawBySource['getenv'] !== null;
    $hasServerOnly = $rawBySource['server'] !== null && !array_key_exists($key, $_ENV);
    $fromProcessEnv = $hasGetenv || $hasServerOnly;

    $processValue = '';
    if ($hasGetenv) {
        $processValue = (string)$rawBySource['getenv'];
    } elseif ($hasServerOnly) {
        $processValue = (string)$rawBySource['server'];
    }

    $dotenvValue = (string)($rawBySource['env'] ?? $rawBySource['server'] ?? '');

    return [
        'sources' => $sources,
        'raw_by_source' => $rawBySource,
        'process_value' => $processValue,
        'dotenv_value' => $dotenvValue,
        'from_process_env' => $fromProcessEnv,
    ];
}

function isValidBridgeId(string $candidate): bool
{
    return $candidate !== ''
        && preg_match('/^[A-Za-z0-9][A-Za-z0-9\-_]{7,63}$/', $candidate) === 1
        && preg_match('/^[0\-_]+$/', $candidate) !== 1
        && strtoupper($candidate) !== 'UNKNOWN';
}

/**
 * Collect all ANDROID_* and KIOSIGN_* environment variables exposed by the OS/Apache process.
 *
 * @return array<string, string>
 */
function collectRawKioSignEnv(): array
{
    $collected = [];

    $processVars = getenv();
    if (is_array($processVars)) {
        foreach ($processVars as $k => $v) {
            if (
                is_string($k) &&
                (str_starts_with($k, 'KIOSIGN_') || str_starts_with($k, 'ANDROID_')) &&
                trim((string)$v) !== ''
            ) {
                $collected[$k] = trim((string)$v);
            }
        }
    }

    foreach ($_SERVER as $k => $v) {
        if (
            is_string($k) &&
            (str_starts_with($k, 'KIOSIGN_') || str_starts_with($k, 'ANDROID_')) &&
            !isset($collected[$k]) &&
            !array_key_exists($k, $_ENV) &&
            is_scalar($v) &&
            trim((string)$v) !== ''
        ) {
            $collected[$k] = trim((string)$v);
        }
    }

    ksort($collected);
    return $collected;
}

/**
 * Validate and report raw KioSign & Android bridge variables.
 *
 * @return array<string, mixed>
 */
function checkKioSignBridge(): array
{
    $rawKioSignEnv = collectRawKioSignEnv();
    $byKey = [];
    $selectedValid = null;
    $firstInvalid = null;
    $firstDotenv = null;

    foreach (KIOSIGN_PRIMARY_ID_KEYS as $key) {
        $info = inspectEnvVar($key);
        $candidate = $info['from_process_env'] ? $info['process_value'] : $info['dotenv_value'];
        $formatValid = isValidBridgeId($candidate);

        $entry = [
            'key' => $key,
            'sources' => $info['sources'],
            'raw_by_source' => $info['raw_by_source'],
            'from_process_env' => $info['from_process_env'],
            'format_valid' => $formatValid,
            'raw_value' => $candidate !== '' ? $candidate : null,
            'normalized' => $formatValid ? strtoupper($candidate) : null,
        ];
        $byKey[$key] = $entry;

        if ($info['from_process_env'] && $formatValid && $selectedValid === null) {
            $selectedValid = $entry;
        } elseif ($info['from_process_env'] && !$formatValid && $firstInvalid === null) {
            $firstInvalid = $entry;
        } elseif (!$info['from_process_env'] && !empty($info['sources']) && $firstDotenv === null) {
            $firstDotenv = $entry;
        }
    }

    $metadata = [];
    foreach (KIOSIGN_METADATA_KEYS as $metaKey) {
        $metaInfo = inspectEnvVar($metaKey);
        $metadata[$metaKey] = [
            'value' => $metaInfo['from_process_env'] ? $metaInfo['process_value'] : null,
            'raw_by_source' => $metaInfo['raw_by_source'],
            'sources' => $metaInfo['sources'],
            'from_process_env' => $metaInfo['from_process_env'],
        ];
    }

    if ($selectedValid !== null) {
        return [
            'status' => 'valid',
            'label' => 'Raw Device ID diterima dari proses KioSign/Apache (' . $selectedValid['key'] . ')',
            'selected_key' => $selectedValid['key'],
            'raw_value' => $selectedValid['raw_value'],
            'device_id' => $selectedValid['normalized'],
            'format_valid' => true,
            'from_process_env' => true,
            'sources_id' => $selectedValid['sources'],
            'by_key' => $byKey,
            'metadata' => $metadata,
            'raw_kiosign_env' => $rawKioSignEnv,
        ];
    }

    if ($firstInvalid !== null) {
        return [
            'status' => 'invalid_format',
            'label' => 'Format ' . $firstInvalid['key'] . ' tidak valid atau placeholder (0000/UNKNOWN)',
            'selected_key' => $firstInvalid['key'],
            'raw_value' => $firstInvalid['raw_value'],
            'device_id' => null,
            'format_valid' => false,
            'from_process_env' => true,
            'sources_id' => $firstInvalid['sources'],
            'by_key' => $byKey,
            'metadata' => $metadata,
            'raw_kiosign_env' => $rawKioSignEnv,
        ];
    }

    if ($firstDotenv !== null) {
        return [
            'status' => 'dotenv_only',
            'label' => $firstDotenv['key'] . ' hanya terdeteksi di $_ENV (.env), bukan dari env proses KioSign/Apache',
            'selected_key' => $firstDotenv['key'],
            'raw_value' => $firstDotenv['raw_value'],
            'device_id' => null,
            'format_valid' => $firstDotenv['format_valid'],
            'from_process_env' => false,
            'sources_id' => $firstDotenv['sources'],
            'by_key' => $byKey,
            'metadata' => $metadata,
            'raw_kiosign_env' => $rawKioSignEnv,
        ];
    }

    return [
        'status' => 'missing',
        'label' => 'Belum ada variabel Device ID yang diterima dari KioSign',
        'selected_key' => null,
        'raw_value' => null,
        'device_id' => null,
        'format_valid' => false,
        'from_process_env' => false,
        'sources_id' => [],
        'by_key' => $byKey,
        'metadata' => $metadata,
        'raw_kiosign_env' => $rawKioSignEnv,
    ];
}

/**
 * Inspect PHP version, extensions, Apache modules, and php.ini limits needed by DSV-class projects.
 *
 * @return array<string, mixed>
 */
function checkRequirements(): array
{
    $phpVersionOk = version_compare(PHP_VERSION, '8.1.0', '>=');

    $requiredExts = ['pdo', 'pdo_sqlite', 'json', 'session'];
    $recommendedExts = ['mbstring', 'openssl', 'fileinfo'];

    $extStatus = [];
    $coreExtOk = true;
    foreach ($requiredExts as $ext) {
        $loaded = extension_loaded($ext);
        $extStatus[$ext] = ['loaded' => $loaded, 'required' => true];
        if (!$loaded) {
            $coreExtOk = false;
        }
    }
    foreach ($recommendedExts as $ext) {
        $extStatus[$ext] = ['loaded' => extension_loaded($ext), 'required' => false];
    }

    $modRewrite = null;
    if (function_exists('apache_get_modules')) {
        $modules = @apache_get_modules();
        if (is_array($modules)) {
            $modRewrite = in_array('mod_rewrite', $modules, true);
        }
    }

    return [
        'core_ok' => $phpVersionOk && $coreExtOk,
        'php_version' => PHP_VERSION,
        'php_version_ok' => $phpVersionOk,
        'php_os' => PHP_OS,
        'php_sapi' => PHP_SAPI,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
        'mod_rewrite' => $modRewrite,
        'extensions' => $extStatus,
        'ini' => [
            'file_uploads' => (string)ini_get('file_uploads'),
            'upload_max_filesize' => (string)ini_get('upload_max_filesize'),
            'post_max_size' => (string)ini_get('post_max_size'),
            'memory_limit' => (string)ini_get('memory_limit'),
            'max_execution_time' => (string)ini_get('max_execution_time'),
            'date_timezone' => (string)(ini_get('date.timezone') ?: date_default_timezone_get()),
            'display_errors' => (string)ini_get('display_errors'),
            'display_startup_errors' => (string)ini_get('display_startup_errors'),
        ],
    ];
}

/**
 * Safely probe whether a directory exists and supports file write/read/delete.
 *
 * @return array{path: string, exists: bool, writable: bool, error: ?string}
 */
function probeDirectoryWrite(string $dir): array
{
    $normalized = rtrim($dir, '/\\');
    if ($normalized === '' || !is_dir($normalized)) {
        $parent = $normalized !== '' ? dirname($normalized) : '';
        return [
            'path' => $dir,
            'exists' => false,
            'writable' => false,
            'error' => ($parent !== '' && is_dir($parent) && is_writable($parent))
                ? 'Direktori belum ada (parent writable)'
                : 'Direktori tidak ditemukan',
        ];
    }

    $probeFile = $normalized . DIRECTORY_SEPARATOR . '.kiosign_probe_' . bin2hex(random_bytes(6)) . '.tmp';
    try {
        $bytes = @file_put_contents($probeFile, 'kiosign_ok');
        if ($bytes === false) {
            return [
                'path' => $normalized,
                'exists' => true,
                'writable' => false,
                'error' => 'Gagal menulis file uji sementara',
            ];
        }

        $readBack = @file_get_contents($probeFile);
        if ($readBack !== 'kiosign_ok') {
            return [
                'path' => $normalized,
                'exists' => true,
                'writable' => false,
                'error' => 'Hasil baca ulang probe tidak cocok',
            ];
        }

        return [
            'path' => $normalized,
            'exists' => true,
            'writable' => true,
            'error' => null,
        ];
    } catch (Throwable $e) {
        return [
            'path' => $normalized,
            'exists' => true,
            'writable' => false,
            'error' => $e->getMessage(),
        ];
    } finally {
        if (file_exists($probeFile)) {
            @unlink($probeFile);
        }
    }
}

/**
 * Probe SQLite driver, version, and temporary WAL/DELETE database read/write.
 *
 * @return array<string, mixed>
 */
function checkSqliteSupport(string $probeDir): array
{
    $drivers = PDO::getAvailableDrivers();
    $hasPdoSqlite = in_array('sqlite', $drivers, true);

    if (!$hasPdoSqlite) {
        return [
            'pdo_sqlite' => false,
            'sqlite_version' => null,
            'journal_mode' => null,
            'wal_supported' => false,
            'probe_ok' => false,
            'error' => 'Driver pdo_sqlite tidak tersedia',
        ];
    }

    $sqliteVersion = null;
    try {
        $memPdo = new PDO('sqlite::memory:');
        $verStmt = $memPdo->query('SELECT sqlite_version()');
        $sqliteVersion = $verStmt ? (string)$verStmt->fetchColumn() : null;
        if ($verStmt) {
            $verStmt->closeCursor();
            $verStmt = null;
        }
        $memPdo = null;
    } catch (Throwable $e) {
        $sqliteVersion = null;
    }

    $targetDir = (is_dir($probeDir) && is_writable($probeDir)) ? $probeDir : sys_get_temp_dir();
    $tempDb = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . '.kiosign_sqlite_probe_' . bin2hex(random_bytes(6)) . '.db';

    $pdo = null;
    $jmStmt = null;
    $stmt = null;
    $selectStmt = null;

    try {
        $pdo = new PDO('sqlite:' . $tempDb, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA busy_timeout = 3000');

        $journalMode = 'delete';
        try {
            $jmStmt = $pdo->query('PRAGMA journal_mode = WAL');
            $journalMode = strtolower((string)($jmStmt ? $jmStmt->fetchColumn() : 'delete'));
            if ($jmStmt) {
                $jmStmt->closeCursor();
                $jmStmt = null;
            }
            if ($journalMode === 'wal') {
                $pdo->exec('PRAGMA synchronous = NORMAL');
            } else {
                $pdo->exec('PRAGMA synchronous = FULL');
            }
        } catch (PDOException $e) {
            try {
                $jmStmt = $pdo->query('PRAGMA journal_mode = DELETE');
                $journalMode = strtolower((string)($jmStmt ? $jmStmt->fetchColumn() : 'delete'));
                if ($jmStmt) {
                    $jmStmt->closeCursor();
                    $jmStmt = null;
                }
                $pdo->exec('PRAGMA synchronous = FULL');
            } catch (PDOException $fallbackException) {
                $journalMode = 'unknown';
            }
        }

        $pdo->exec('CREATE TABLE "_probe" ("id" INTEGER PRIMARY KEY, "val" TEXT NOT NULL)');
        $stmt = $pdo->prepare('INSERT INTO "_probe" ("val") VALUES (:val)');
        $stmt->execute([':val' => 'kiosign_ok']);
        $stmt->closeCursor();
        $stmt = null;

        $selectStmt = $pdo->query('SELECT "val" FROM "_probe" WHERE "id" = 1');
        $fetched = $selectStmt ? $selectStmt->fetchColumn() : false;
        if ($selectStmt) {
            $selectStmt->closeCursor();
            $selectStmt = null;
        }

        if ($fetched !== 'kiosign_ok') {
            return [
                'pdo_sqlite' => true,
                'sqlite_version' => $sqliteVersion,
                'journal_mode' => $journalMode,
                'wal_supported' => $journalMode === 'wal',
                'probe_ok' => false,
                'error' => 'Verifikasi query SQLite tidak cocok',
            ];
        }

        return [
            'pdo_sqlite' => true,
            'sqlite_version' => $sqliteVersion,
            'journal_mode' => $journalMode,
            'wal_supported' => $journalMode === 'wal',
            'probe_ok' => true,
            'error' => null,
        ];
    } catch (Throwable $e) {
        return [
            'pdo_sqlite' => true,
            'sqlite_version' => $sqliteVersion,
            'journal_mode' => null,
            'wal_supported' => false,
            'probe_ok' => false,
            'error' => $e->getMessage(),
        ];
    } finally {
        $jmStmt = null;
        $stmt = null;
        $selectStmt = null;
        $pdo = null;
        gc_collect_cycles();
        foreach ([$tempDb, $tempDb . '-wal', $tempDb . '-shm', $tempDb . '-journal'] as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
}

/**
 * Inspect DSV LicenseService when inside a DSV project (read-only: never creates dsv.db).
 *
 * @return array<string, mixed>
 */
function checkDsvKernel(string $dsvRoot): array
{
    $autoloadPath = $dsvRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $settingsPath = $dsvRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php';
    $envPath = $dsvRoot . DIRECTORY_SEPARATOR . '.env';

    if (!file_exists($autoloadPath) || !file_exists($settingsPath)) {
        return [
            'available' => false,
            'device_id' => null,
            'platform' => null,
            'db_exists' => false,
            'is_licensed' => null,
            'error' => 'Mode standalone (project DSV parent tidak ditemukan)',
        ];
    }

    try {
        require_once $autoloadPath;

        if (file_exists($envPath) && class_exists(\Dotenv\Dotenv::class)) {
            \Dotenv\Dotenv::createImmutable($dsvRoot)->safeLoad();
        }

        $settings = require $settingsPath;
        $dbSettings = (array)($settings['db'] ?? []);
        $dbPath = (string)($dbSettings['path'] ?? '');
        $dbExists = $dbPath !== '' && $dbPath !== ':memory:' && file_exists($dbPath);

        if (!class_exists(\App\Domain\License\LicenseService::class)) {
            return [
                'available' => false,
                'device_id' => null,
                'platform' => null,
                'db_exists' => $dbExists,
                'is_licensed' => null,
                'error' => 'Class App\\Domain\\License\\LicenseService tidak ditemukan',
            ];
        }

        $licenseService = new \App\Domain\License\LicenseService($dbSettings);
        $deviceId = $licenseService->getDeviceUUID();
        $platform = $licenseService->getPlatform();
        $isLicensed = $dbExists ? $licenseService->isLicensed() : false;

        return [
            'available' => true,
            'device_id' => $deviceId,
            'platform' => $platform,
            'db_exists' => $dbExists,
            'is_licensed' => $isLicensed,
            'error' => null,
        ];
    } catch (Throwable $e) {
        return [
            'available' => false,
            'device_id' => null,
            'platform' => null,
            'db_exists' => false,
            'is_licensed' => null,
            'error' => $e->getMessage(),
        ];
    }
}

$dsvRoot = dirname(__DIR__);
$isDsvContext = file_exists($dsvRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php');

// Inspect KioSign/Android variables BEFORE Dotenv loads .env inside checkDsvKernel()
$bridge = checkKioSignBridge();
$requirements = checkRequirements();
$dsvKernel = checkDsvKernel($dsvRoot);

$docRoot = (string)($_SERVER['DOCUMENT_ROOT'] ?? '');
$currentDir = __DIR__;
$normalizedPathCheck = str_replace('\\', '/', $currentDir . ' ' . $docRoot);

if (preg_match('~(/sdcard/|/storage/emulated/|/KiosignHost)~i', $normalizedPathCheck) === 1) {
    $storageLocation = 'external_storage_granted';
    $storageLocationLabel = 'External Storage (/sdcard/KiosignHost) aktif';
} elseif (preg_match('~(/data/user/|/data/data/|/files/host)~i', $normalizedPathCheck) === 1) {
    $storageLocation = 'internal_fallback';
    $storageLocationLabel = 'Internal Fallback (files/host) — izin All Files Access belum aktif';
} elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $storageLocation = 'windows_local';
    $storageLocationLabel = 'Windows Local Host (WampDock / Dev)';
} else {
    $storageLocation = 'other';
    $storageLocationLabel = 'Linux / Host Custom';
}

$primaryStorageDir = ($isDsvContext && is_dir($dsvRoot . DIRECTORY_SEPARATOR . 'storage'))
    ? $dsvRoot . DIRECTORY_SEPARATOR . 'storage'
    : $currentDir;

$probes = [
    'primary' => probeDirectoryWrite($primaryStorageDir),
    'tmp' => probeDirectoryWrite(sys_get_temp_dir()),
];

$rawSessionPath = trim((string)session_save_path());
if ($rawSessionPath !== '') {
    $sessionDir = str_contains($rawSessionPath, ';')
        ? trim(substr($rawSessionPath, (int)strrpos($rawSessionPath, ';') + 1))
        : $rawSessionPath;
    if ($sessionDir !== '' && !preg_match('~^[a-z]+://~i', $sessionDir)) {
        $probes['session'] = probeDirectoryWrite($sessionDir);
    }
}

if ($isDsvContext) {
    $logoDir = $dsvRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo';
    $videosDir = $dsvRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'videos';
    $probes['logo'] = probeDirectoryWrite($logoDir);
    $probes['videos'] = probeDirectoryWrite($videosDir);
}

$storageProbeOk = $probes['primary']['writable'] && $probes['tmp']['writable'];
$storage = [
    'document_root' => $docRoot !== '' ? $docRoot : null,
    'current_dir' => $currentDir,
    'location_type' => $storageLocation,
    'location_label' => $storageLocationLabel,
    'probe_ok' => $storageProbeOk,
    'probes' => $probes,
];

$sqlite = checkSqliteSupport($primaryStorageDir);

$allKioSignReady = ($bridge['status'] === 'valid')
    && $requirements['core_ok']
    && $storage['probe_ok']
    && $sqlite['probe_ok']
    && ($storageLocation !== 'internal_fallback');

$issues = [];
if ($bridge['status'] === 'missing') {
    if ($storageLocation === 'windows_local') {
        $issues[] = 'Berjalan di Windows: variabel ANDROID_HARDWARE_UUID / ANDROID_DEVICE_ID / KIOSIGN_DEVICE_ID hanya dikirim oleh aplikasi Android KioSign.';
    } else {
        $issues[] = 'Variabel Device ID (ANDROID_HARDWARE_UUID / ANDROID_DEVICE_ID / KIOSIGN_DEVICE_ID) belum diterima dari KioSign.';
    }
} elseif ($bridge['status'] === 'dotenv_only') {
    $issues[] = ($bridge['selected_key'] ?? 'Device ID') . ' hanya terbaca dari $_ENV (.env), bukan dari env proses Apache/KioSign — ditolak demi keamanan.';
} elseif ($bridge['status'] === 'invalid_format') {
    $issues[] = 'Format ' . ($bridge['selected_key'] ?? 'Device ID') . ' tidak valid atau berupa placeholder (0000/UNKNOWN).';
}

if (!$requirements['core_ok']) {
    $issues[] = 'Persyaratan inti PHP (PHP >= 8.1, pdo, pdo_sqlite, json, session) belum terpenuhi seluruhnya.';
}
if ($requirements['mod_rewrite'] === false) {
    $issues[] = 'Apache mod_rewrite tidak aktif (dibutuhkan untuk clean URL Slim Framework).';
}
if ($storageLocation === 'internal_fallback') {
    $issues[] = 'Apache memakai fallback internal files/host (izin All Files Access di KioSign belum aktif saat server dimulai).';
}
if (!$storage['probe_ok']) {
    $issues[] = 'Izin tulis direktori storage utama atau temp gagal.';
}
if (!$sqlite['probe_ok']) {
    $issues[] = 'SQLite probe gagal: ' . ($sqlite['error'] ?? 'unknown error');
}
if ($requirements['ini']['display_errors'] === '1' || strtolower($requirements['ini']['display_errors']) === 'on') {
    $issues[] = 'display_errors masih On di php.ini (disarankan Off di produksi agar JSON API tidak terganggu).';
}

$summary = [
    'ready' => $allKioSignReady,
    'headline' => $allKioSignReady ? 'SIAP DIGUNAKAN DI KIOSIGN' : 'PERLU PENYESUAIAN',
    'issues' => $issues,
    'checked_at' => date('Y-m-d H:i:s'),
];

$payload = [
    'summary' => $summary,
    'bridge' => $bridge,
    'requirements' => $requirements,
    'dsv_kernel' => $dsvKernel,
    'storage' => $storage,
    'sqlite' => $sqlite,
];

$format = strtolower(trim((string)($_GET['format'] ?? 'html')));
if ($format === 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

function h(?string $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function renderBadge(bool $ok, string $okText, string $failText, bool $warn = false): string
{
    if ($ok) {
        return '<span class="badge badge-ok">' . h($okText) . '</span>';
    }
    if ($warn) {
        return '<span class="badge badge-warn">' . h($failText) . '</span>';
    }
    return '<span class="badge badge-fail">' . h($failText) . '</span>';
}

$displayDeviceId = $bridge['device_id'] ?? $dsvKernel['device_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KioSign Raw Device &amp; Runtime Check</title>
    <style>
        :root {
            --bg: #0b0f19;
            --card: #111827;
            --card-alt: #1a2234;
            --border: #1f2937;
            --text: #f3f4f6;
            --muted: #9ca3af;
            --ok-bg: rgba(16, 185, 129, 0.15);
            --ok-fg: #34d399;
            --ok-bd: rgba(16, 185, 129, 0.35);
            --warn-bg: rgba(245, 158, 11, 0.15);
            --warn-fg: #fbbf24;
            --warn-bd: rgba(245, 158, 11, 0.35);
            --fail-bg: rgba(239, 68, 68, 0.15);
            --fail-fg: #f87171;
            --fail-bd: rgba(239, 68, 68, 0.35);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            padding: 20px;
        }
        .container {
            max-width: 980px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .banner {
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border);
            background: var(--card);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .banner-ok { border-color: var(--ok-bd); background: linear-gradient(135deg, #0d1f1a 0%, #111827 100%); }
        .banner-warn { border-color: var(--warn-bd); background: linear-gradient(135deg, #1f190d 0%, #111827 100%); }
        .banner h1 { font-size: 20px; font-weight: 800; letter-spacing: 0.02em; }
        .banner-ok h1 { color: var(--ok-fg); }
        .banner-warn h1 { color: var(--warn-fg); }
        .sub { color: var(--muted); font-size: 13px; margin-top: 4px; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #334155;
            background: #1e293b;
            color: #f8fafc;
        }
        .btn:hover { background: #334155; }
        .btn-primary { background: #0284c7; border-color: #0ea5e9; }
        .btn-primary:hover { background: #0369a1; }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 20px;
        }
        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        .card-title { font-size: 15px; font-weight: 700; color: #ffffff; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border: 1px solid transparent;
        }
        .badge-ok { background: var(--ok-bg); color: var(--ok-fg); border-color: var(--ok-bd); }
        .badge-warn { background: var(--warn-bg); color: var(--warn-fg); border-color: var(--warn-bd); }
        .badge-fail { background: var(--fail-bg); color: var(--fail-fg); border-color: var(--fail-bd); }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
        }
        .item {
            background: var(--card-alt);
            border: 1px solid #263145;
            border-radius: 10px;
            padding: 12px 14px;
        }
        .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            font-weight: 700;
            margin-bottom: 4px;
        }
        .val {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            word-break: break-all;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }
        .issue-list {
            margin-top: 10px;
            padding-left: 18px;
            color: var(--warn-fg);
            font-size: 13px;
        }
        .issue-list li + li { margin-top: 4px; }
        .probe-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .probe-table th, .probe-table td {
            text-align: left;
            padding: 9px 10px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        .probe-table th { color: var(--muted); font-size: 11px; text-transform: uppercase; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; word-break: break-all; }
        .copy-toast { font-size: 12px; color: var(--ok-fg); display: none; }
        pre.json-box {
            margin-top: 10px;
            background: #090d16;
            border: 1px solid #1f2937;
            border-radius: 8px;
            padding: 10px 12px;
            color: var(--ok-fg);
            font-size: 12px;
            overflow-x: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="banner <?= $allKioSignReady ? 'banner-ok' : 'banner-warn'; ?>">
        <div>
            <h1><?= h($summary['headline']); ?></h1>
            <div class="sub">Pemeriksaan raw KioSign/Android env, JS Bridge, persyaratan PHP/Apache, storage, dan SQLite WAL (<?= h($summary['checked_at']); ?>)</div>
            <?php if (!empty($issues)): ?>
                <ul class="issue-list">
                    <?php foreach ($issues as $issue): ?>
                        <li><?= h($issue); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="actions">
            <?php if ($displayDeviceId !== ''): ?>
                <button type="button" class="btn btn-primary" onclick="copyDeviceId()">Salin Device ID</button>
                <span id="copyToast" class="copy-toast">Tersalin!</span>
            <?php endif; ?>
            <button type="button" class="btn" onclick="window.location.reload()">Refresh</button>
            <a class="btn" href="?format=json">JSON Output</a>
        </div>
    </div>

    <!-- 1. Raw PHP Server/Env Variables from KioSign -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">1. Raw PHP Server / Env Variables dari KioSign</div>
            <?= renderBadge(
                $bridge['status'] === 'valid',
                'BRIDGE VALID',
                strtoupper(str_replace('_', ' ', (string)$bridge['status'])),
                $bridge['status'] === 'missing' && $storageLocation === 'windows_local'
            ); ?>
        </div>
        <div class="grid" style="margin-bottom: 12px;">
            <div class="item">
                <div class="label">Status Bridge</div>
                <div class="val"><?= h((string)$bridge['label']); ?></div>
            </div>
            <div class="item">
                <div class="label">Key Terpilih &amp; Raw Value</div>
                <div class="val"><?= h(($bridge['selected_key'] ?? '-') . ': ' . ($bridge['raw_value'] ?? '(tidak ada)')); ?></div>
                <div class="sub">Normalized: <span class="mono"<?= $bridge['device_id'] !== null ? ' id="deviceIdVal"' : ''; ?>><?= h($bridge['device_id'] ?? '-'); ?></span></div>
            </div>
            <div class="item">
                <div class="label">Metadata Device (Proses Env)</div>
                <div class="val"><?= h($bridge['metadata']['ANDROID_MODEL']['value'] ?? '(ANDROID_MODEL kosong)'); ?></div>
                <div class="sub">Fingerprint: <?= h($bridge['metadata']['ANDROID_FINGERPRINT']['value'] ?? '(kosong)'); ?></div>
            </div>
        </div>
        <table class="probe-table">
            <thead>
                <tr>
                    <th>Variabel</th>
                    <th>$_SERVER</th>
                    <th>getenv()</th>
                    <th>$_ENV</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bridge['by_key'] as $key => $entry): ?>
                    <tr>
                        <td class="mono"><?= h($key); ?></td>
                        <td class="mono"><?= h($entry['raw_by_source']['server'] ?? '-'); ?></td>
                        <td class="mono"><?= h($entry['raw_by_source']['getenv'] ?? '-'); ?></td>
                        <td class="mono"><?= h($entry['raw_by_source']['env'] ?? '-'); ?></td>
                        <td>
                            <?php if ($entry['from_process_env'] && $entry['format_valid']): ?>
                                <?= renderBadge(true, 'VALID', ''); ?>
                            <?php elseif (!empty($entry['sources'])): ?>
                                <?= renderBadge(false, '', $entry['from_process_env'] ? 'FORMAT INVALID' : 'DOTENV ONLY', true); ?>
                            <?php else: ?>
                                <span class="sub">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!empty($bridge['raw_kiosign_env'])): ?>
                    <?php foreach ($bridge['raw_kiosign_env'] as $envKey => $envVal): ?>
                        <?php if (!in_array($envKey, KIOSIGN_PRIMARY_ID_KEYS, true)): ?>
                            <tr>
                                <td class="mono"><?= h($envKey); ?></td>
                                <td class="mono" colspan="3"><?= h($envVal); ?></td>
                                <td><?= renderBadge(true, 'PROSES ENV', ''); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 2. Live JavaScript Interface (window.AndroidDevice) -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">2. JavaScript Interface (window.AndroidDevice)</div>
            <span id="jsBridgeBadge" class="badge badge-warn">MEMERIKSA...</span>
        </div>
        <div class="grid">
            <div class="item">
                <div class="label">getDeviceId()</div>
                <div class="val" id="jsDeviceId">-</div>
            </div>
            <div class="item">
                <div class="label">getHardwareUuid()</div>
                <div class="val" id="jsHardwareUuid">-</div>
            </div>
            <div class="item">
                <div class="label">getDeviceModel()</div>
                <div class="val" id="jsDeviceModel">-</div>
            </div>
        </div>
        <pre class="json-box" id="jsDeviceInfoJson">Menunggu pemeriksaan window.AndroidDevice...</pre>
    </div>

    <!-- 3. DSV Kernel Recognition -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">3. Pengenalan oleh Kernel DSV (LicenseService)</div>
            <?= renderBadge(
                (bool)$dsvKernel['available'] && !empty($dsvKernel['device_id']),
                'TERDETEKSI',
                $dsvKernel['available'] ? 'DEVICE ID KOSONG' : 'STANDALONE',
                !$dsvKernel['available']
            ); ?>
        </div>
        <?php if ($dsvKernel['available']): ?>
            <div class="grid">
                <div class="item">
                    <div class="label">Device ID Terbaca DSV</div>
                    <div class="val"<?= $bridge['device_id'] === null ? ' id="deviceIdVal"' : ''; ?>><?= h($dsvKernel['device_id'] ?? '(tidak terdeteksi)'); ?></div>
                </div>
                <div class="item">
                    <div class="label">Platform Terdeteksi</div>
                    <div class="val"><?= h((string)($dsvKernel['platform'] ?? 'unknown')); ?></div>
                </div>
                <div class="item">
                    <div class="label">Status Lisensi SQLite</div>
                    <div class="val">
                        <?php if (!$dsvKernel['db_exists']): ?>
                            Belum diaktivasi (dsv.db belum dibuat)
                        <?php else: ?>
                            <?= $dsvKernel['is_licensed'] ? 'Aktif (Licensed)' : 'Belum Aktif (Unlicensed)'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="sub"><?= h((string)$dsvKernel['error']); ?></div>
        <?php endif; ?>
    </div>

    <!-- 4. Checklist Persyaratan Runtime -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">4. Checklist Persyaratan DSV &amp; Project Sejenis</div>
            <?= renderBadge($requirements['core_ok'], 'CORE READY', 'BELUM LENGKAP'); ?>
        </div>
        <div class="grid" style="margin-bottom: 12px;">
            <div class="item">
                <div class="label">PHP &amp; SAPI</div>
                <div class="val">PHP <?= h($requirements['php_version']); ?> (<?= h($requirements['php_sapi']); ?>)</div>
                <div class="sub">Minimal 8.1: <?= $requirements['php_version_ok'] ? 'OK' : 'Kurang'; ?> | OS: <?= h($requirements['php_os']); ?></div>
            </div>
            <div class="item">
                <div class="label">Apache &amp; mod_rewrite</div>
                <div class="val"><?= h((string)($requirements['server_software'] ?? 'Unknown / CLI')); ?></div>
                <div class="sub">
                    mod_rewrite: <?php
                    if ($requirements['mod_rewrite'] === true) {
                        echo 'Aktif';
                    } elseif ($requirements['mod_rewrite'] === false) {
                        echo 'Tidak Aktif';
                    } else {
                        echo 'Tidak dapat dicek (bukan mod_php)';
                    }
                    ?>
                </div>
            </div>
            <div class="item">
                <div class="label">Limit Upload &amp; php.ini</div>
                <div class="val">Upload: <?= h($requirements['ini']['upload_max_filesize']); ?> / Post: <?= h($requirements['ini']['post_max_size']); ?></div>
                <div class="sub">
                    Mem: <?= h($requirements['ini']['memory_limit']); ?> |
                    TZ: <?= h($requirements['ini']['date_timezone']); ?> |
                    display_errors: <?= h($requirements['ini']['display_errors']); ?>
                </div>
            </div>
        </div>
        <table class="probe-table">
            <thead>
                <tr>
                    <th>Ekstensi PHP</th>
                    <th>Kebutuhan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requirements['extensions'] as $ext => $meta): ?>
                    <tr>
                        <td class="mono"><?= h($ext); ?></td>
                        <td><?= $meta['required'] ? 'Wajib' : 'Disarankan'; ?></td>
                        <td><?= renderBadge($meta['loaded'], 'AKTIF', $meta['required'] ? 'TIDAK ADA' : 'OPSIONAL', !$meta['required']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 5. Storage & External Storage Permissions -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">5. Izin Storage &amp; Direktori Tulis</div>
            <?= renderBadge(
                (bool)$storage['probe_ok'] && $storageLocation !== 'internal_fallback',
                'WRITABLE',
                $storageLocation === 'internal_fallback' ? 'INTERNAL FALLBACK' : 'GAGAL TULIS',
                $storageLocation === 'internal_fallback'
            ); ?>
        </div>
        <div class="grid" style="margin-bottom: 12px;">
            <div class="item">
                <div class="label">Lokasi Host</div>
                <div class="val"><?= h($storageLocationLabel); ?></div>
            </div>
            <div class="item">
                <div class="label">DOCUMENT_ROOT</div>
                <div class="val"><?= h($storage['document_root'] ?? '-'); ?></div>
            </div>
            <div class="item">
                <div class="label">Direktori Saat Ini</div>
                <div class="val"><?= h($storage['current_dir']); ?></div>
            </div>
        </div>
        <table class="probe-table">
            <thead>
                <tr>
                    <th>Target</th>
                    <th>Path</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($probes as $key => $probe): ?>
                    <tr>
                        <td class="mono"><?= h($key); ?></td>
                        <td class="mono"><?= h($probe['path']); ?></td>
                        <td><?= renderBadge($probe['writable'], 'OK', 'GAGAL'); ?></td>
                        <td><?= h($probe['error'] ?? 'Write/read/delete OK'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 6. SQLite Engine & WAL -->
    <div class="card">
        <div class="card-head">
            <div class="card-title">6. Uji SQLite &amp; Journal Mode (WAL / DELETE)</div>
            <?= renderBadge((bool)$sqlite['probe_ok'], 'SQLITE READY', 'SQLITE ERROR'); ?>
        </div>
        <div class="grid">
            <div class="item">
                <div class="label">PDO SQLite &amp; Versi</div>
                <div class="val"><?= $sqlite['pdo_sqlite'] ? 'Aktif (v' . h($sqlite['sqlite_version'] ?? '?') . ')' : 'Tidak Tersedia'; ?></div>
            </div>
            <div class="item">
                <div class="label">Journal Mode Aktif</div>
                <div class="val"><?= h(strtoupper((string)($sqlite['journal_mode'] ?? 'N/A'))); ?></div>
                <div class="sub">WAL didukung: <?= $sqlite['wal_supported'] ? 'Ya' : 'Tidak (fallback DELETE)'; ?></div>
            </div>
            <div class="item">
                <div class="label">Hasil Probe Read/Write</div>
                <div class="val"><?= $sqlite['probe_ok'] ? 'Sukses (Create/Insert/Select/Cleanup)' : 'Gagal'; ?></div>
                <?php if (!empty($sqlite['error'])): ?>
                    <div class="sub"><?= h((string)$sqlite['error']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function inspectAndroidJsBridge() {
    var badge = document.getElementById('jsBridgeBadge');
    var elId = document.getElementById('jsDeviceId');
    var elUuid = document.getElementById('jsHardwareUuid');
    var elModel = document.getElementById('jsDeviceModel');
    var elJson = document.getElementById('jsDeviceInfoJson');

    if (typeof window.AndroidDevice === 'undefined' || window.AndroidDevice === null) {
        if (badge) {
            badge.className = 'badge badge-warn';
            badge.textContent = 'BROWSER BIASA';
        }
        if (elJson) {
            elJson.textContent = 'window.AndroidDevice tidak ditemukan (dibuka di luar WebView KioSign).';
        }
        return;
    }

    if (badge) {
        badge.className = 'badge badge-ok';
        badge.textContent = 'JS BRIDGE AKTIF';
    }

    try {
        if (elId && typeof window.AndroidDevice.getDeviceId === 'function') {
            elId.textContent = window.AndroidDevice.getDeviceId() || '-';
        }
        if (elUuid && typeof window.AndroidDevice.getHardwareUuid === 'function') {
            elUuid.textContent = window.AndroidDevice.getHardwareUuid() || '-';
        }
        if (elModel && typeof window.AndroidDevice.getDeviceModel === 'function') {
            elModel.textContent = window.AndroidDevice.getDeviceModel() || '-';
        }
        if (elJson && typeof window.AndroidDevice.getDeviceInfoJson === 'function') {
            var rawJson = window.AndroidDevice.getDeviceInfoJson();
            try {
                elJson.textContent = JSON.stringify(JSON.parse(rawJson), null, 2);
            } catch (e) {
                elJson.textContent = String(rawJson);
            }
        }
    } catch (err) {
        if (elJson) {
            elJson.textContent = 'Error membaca window.AndroidDevice: ' + err;
        }
    }
})();

function copyDeviceId() {
    var el = document.getElementById('deviceIdVal');
    if (!el) return;
    var text = (el.innerText || el.textContent || '').trim();
    if (!text || text === '-' || text.indexOf('(') === 0) return;

    function markCopied() {
        var toast = document.getElementById('copyToast');
        if (!toast) return;
        toast.style.display = 'inline';
        setTimeout(function () { toast.style.display = 'none'; }, 1600);
    }

    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
        navigator.clipboard.writeText(text).then(markCopied).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }

    function fallbackCopy() {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            markCopied();
        } finally {
            document.body.removeChild(ta);
        }
    }
}
</script>
</body>
</html>
