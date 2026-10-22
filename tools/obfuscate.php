<?php

declare(strict_types=1);

/**
 * Obfuscates tools/LicenseService.src.php into src/Domain/License/LicenseService.php
 * Run:
 *   php tools/obfuscate.php
 */

$srcFile = __DIR__ . '/LicenseService.src.php';
$destFile = dirname(__DIR__) . '/src/Domain/License/LicenseService.php';

if (!file_exists($srcFile)) {
    echo "Error: Source file not found: $srcFile\n";
    exit(1);
}

$rawCode = file_get_contents($srcFile);

// Strip opening php tag if present
$cleanCode = preg_replace('/^<\?php\s*/i', '', trim($rawCode));

// Generate a random 256-bit AES key and 128-bit IV
$cipher = 'AES-256-CBC';
$encryptionKey = bin2hex(random_bytes(16)); // 32 hex chars = 256 bits
$iv = random_bytes(openssl_cipher_iv_length($cipher));

// Compress then encrypt
$compressed = gzdeflate($cleanCode, 9);
$encrypted = openssl_encrypt($compressed, $cipher, $encryptionKey, OPENSSL_RAW_DATA, $iv);

$payloadBase64 = base64_encode($iv . $encrypted);

// Split key into fragments to prevent plain text grep
$part1 = substr($encryptionKey, 0, 8);
$part2 = substr($encryptionKey, 8, 8);
$part3 = substr($encryptionKey, 16, 8);
$part4 = substr($encryptionKey, 24, 8);

// Random variable names
$vKey = '_' . substr(md5((string)rand()), 0, 6);
$vData = '_' . substr(md5((string)rand()), 0, 6);
$vCipher = '_' . substr(md5((string)rand()), 0, 6);
$vIvLen = '_' . substr(md5((string)rand()), 0, 6);
$vIv = '_' . substr(md5((string)rand()), 0, 6);
$vEnc = '_' . substr(md5((string)rand()), 0, 6);
$vDec = '_' . substr(md5((string)rand()), 0, 6);

$obfuscatedTemplate = <<<PHP
<?php

declare(strict_types=1);

/**
 * Display Signage Valas (DSV) Core Licensing Component
 * [Protected & Locked - Unauthorized modification or reproduction prohibited]
 */

\${$vCipher} = '{$cipher}';
\${$vKey} = implode('', ['{$part1}', '{$part2}', '{$part3}', '{$part4}']);
\${$vData} = base64_decode('{$payloadBase64}');
\${$vIvLen} = openssl_cipher_iv_length(\${$vCipher});
\${$vIv} = substr(\${$vData}, 0, \${$vIvLen});
\${$vEnc} = substr(\${$vData}, \${$vIvLen});
\${$vDec} = openssl_decrypt(\${$vEnc}, \${$vCipher}, \${$vKey}, OPENSSL_RAW_DATA, \${$vIv});

if (\${$vDec} !== false) {
    eval(gzinflate(\${$vDec}));
} else {
    throw new \RuntimeException('License module verification failed: integrity compromised.');
}
PHP;

file_put_contents($destFile, $obfuscatedTemplate);
echo "Successfully obfuscated LicenseService.php!\n";
echo "Destination: $destFile\n";
