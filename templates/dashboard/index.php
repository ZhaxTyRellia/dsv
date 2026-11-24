<?php
if (!empty($__activation_gate) || empty($licenseStatus['is_licensed'])):
    $platformRaw = strtolower(trim((string)($platform ?? ($licenseStatus['platform'] ?? 'unknown'))));
    $platformLabel = match ($platformRaw) {
        'windows' => 'Windows Server',
        'linux' => 'Linux Server',
        'macos' => 'macOS Server',
        'android', 'android_kiosign' => 'Android Server',
        default => 'Universal Server',
    };
    $deviceIdStr = trim((string)($device_id ?? ($licenseStatus['device_id'] ?? '')));
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Device Activation — Display Signage</title>
    <meta name="description" content="Hardware Device License Activation">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= $basePath; ?>/img/favicon.png">
    <style>
        :root {
            --bg-base: #f4f7fb;
            --card-bg: rgba(255, 255, 255, 0.96);
            --card-border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --indigo: #6366f1;
            --sky: #0ea5e9;
            --emerald: #10b981;
            --amber: #f59e0b;
            --rose: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text-main);
            background-color: var(--bg-base);
            background-image:
                radial-gradient(circle at 12% 18%, rgba(99, 102, 241, 0.18) 0%, transparent 42%),
                radial-gradient(circle at 88% 15%, rgba(14, 165, 233, 0.20) 0%, transparent 42%),
                radial-gradient(circle at 82% 85%, rgba(16, 185, 129, 0.16) 0%, transparent 45%),
                radial-gradient(circle at 16% 84%, rgba(245, 158, 11, 0.14) 0%, transparent 42%),
                url("<?= $basePath; ?>/img/login.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.45; transform: scale(0.82); }
        }

        .activation-card {
            width: 100%;
            max-width: 480px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow:
                0 20px 45px -12px rgba(15, 23, 42, 0.12),
                0 8px 18px -6px rgba(99, 102, 241, 0.08);
            overflow: hidden;
            animation: cardEnter 0.28s ease-out;
        }

        .card-accent-bar {
            height: 6px;
            width: 100%;
            background: linear-gradient(90deg, #6366f1 0%, #0ea5e9 35%, #10b981 70%, #f59e0b 100%);
        }

        .card-body {
            padding: 30px 28px 26px 28px;
        }

        .emblem-wrap {
            width: 68px;
            height: 68px;
            margin: 0 auto 14px auto;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.10) 0%, rgba(14, 165, 233, 0.12) 55%, rgba(16, 185, 129, 0.10) 100%);
            border: 1px solid rgba(14, 165, 233, 0.28);
            padding: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px -6px rgba(14, 165, 233, 0.25);
        }

        .emblem-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .pill-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 4px 11px;
            border-radius: 999px;
        }

        .pill-status {
            color: #b45309;
            background: #fef3c7;
            border: 1px solid #fcd34d;
        }

        .pill-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #f59e0b;
            animation: pulseDot 1.6s infinite;
        }

        .pill-platform {
            color: #0369a1;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            font-size: 13.5px;
            line-height: 1.55;
            color: var(--text-muted);
            margin-bottom: 22px;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
            margin-bottom: 16px;
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-error {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .alert-success {
            color: #047857;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
        }

        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 7px;
        }

        .device-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: linear-gradient(135deg, #f0f9ff 0%, #eef2ff 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .device-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px;
            font-weight: 700;
            color: #1d4ed8;
            word-break: break-all;
            user-select: all;
        }

        .device-code-missing {
            color: var(--rose);
        }

        .btn-copy {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 8px;
            border: 1px solid #c7d2fe;
            background: #ffffff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(99, 102, 241, 0.08);
            transition: all 0.16s ease;
        }

        .btn-copy:hover {
            border-color: #6366f1;
            background: #eef2ff;
            color: #3730a3;
        }

        .btn-copy.copied {
            border-color: #34d399;
            background: #ecfdf5;
            color: #059669;
        }

        .key-input {
            width: 100%;
            padding: 13px 14px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            background: #f8fafc;
            color: var(--text-main);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13.5px;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
            margin-bottom: 16px;
        }

        .key-input::placeholder {
            color: #94a3b8;
        }

        .key-input:focus {
            outline: none;
            background: #ffffff;
            border-color: var(--indigo);
            box-shadow: 0 0 0 3.5px rgba(99, 102, 241, 0.15);
        }

        .btn-activate {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 18px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #0ea5e9 55%, #10b981 100%);
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 22px -6px rgba(14, 165, 233, 0.45);
            transition: transform 0.15s ease, filter 0.15s ease, opacity 0.15s ease;
        }

        .btn-activate:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .btn-activate:active {
            transform: translateY(0);
        }

        .btn-activate:disabled {
            opacity: 0.7;
            cursor: wait;
            transform: none;
        }

        .card-footer {
            padding: 13px 28px;
            border-top: 1px solid #f1f5f9;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.04), rgba(14, 165, 233, 0.05), rgba(16, 185, 129, 0.04));
            text-align: center;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.02em;
        }
    </style>
</head>

<body>
    <main class="activation-card">
        <div class="card-accent-bar"></div>
        <div class="card-body">
            <div class="emblem-wrap">
                <img src="<?= $basePath; ?>/img/favicon.png" alt="DSV Logo">
            </div>

            <div class="pill-row">
                <span class="pill pill-status"><span class="pill-dot"></span>Activation Required</span>
                <span class="pill pill-platform"><?= htmlspecialchars($platformLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <h1>Application Activation</h1>
            <p class="subtitle">
                Copy this device's <strong>Device ID</strong> to generate an official <strong>Activate Key</strong>, then paste it below to unlock full access.
            </p>

            <?php if (!empty($flashMessages) && is_array($flashMessages)): ?>
                <?php foreach ($flashMessages as $msg): ?>
                    <?php
                    $isErr = in_array((string)($msg['type'] ?? ''), ['danger', 'error'], true);
                    ?>
                    <div class="alert <?= $isErr ? 'alert-error' : 'alert-success'; ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php if ($isErr): ?>
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            <?php else: ?>
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            <?php endif; ?>
                        </svg>
                        <span><?= htmlspecialchars((string)($msg['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span><?= htmlspecialchars((string)$error_msg, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <span class="field-label">Device ID</span>
            <div class="device-box">
                <span id="deviceIdText" class="device-code<?= $deviceIdStr === '' ? ' device-code-missing' : ''; ?>">
                    <?= $deviceIdStr !== '' ? htmlspecialchars($deviceIdStr, ENT_QUOTES, 'UTF-8') : 'Not Detected'; ?>
                </span>
                <?php if ($deviceIdStr !== ''): ?>
                    <button type="button" id="copyDeviceBtn" class="btn-copy" onclick="copyDeviceID(this)">
                        <svg id="copyIconDefault" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <svg id="copyIconDone" style="display:none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span id="copyBtnLabel">Copy</span>
                    </button>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?= $urlFor('activation.submit'); ?>" onsubmit="return handleActivateSubmit(this)">
                <label class="field-label" for="activate_key">Enter Activate Key</label>
                <input
                    type="text"
                    id="activate_key"
                    name="activate_key"
                    class="key-input"
                    placeholder="Paste official Activate Key here"
                    required
                    autocomplete="off"
                    spellcheck="false"
                    autofocus
                >
                <button type="submit" id="activateSubmitBtn" class="btn-activate">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                    </svg>
                    <span id="activateBtnLabel">Activate License</span>
                </button>
            </form>
        </div>

        <div class="card-footer">
            ECDSA P-256 Offline Verification &bull; Hardware Bound
        </div>
    </main>

    <script>
        function copyDeviceID(btn) {
            var el = document.getElementById('deviceIdText');
            if (!el) return;
            var deviceIdText = (el.innerText || el.textContent || '').trim();
            if (!deviceIdText || deviceIdText === 'Not Detected') return;

            function showCopied() {
                var label = document.getElementById('copyBtnLabel');
                var iconDef = document.getElementById('copyIconDefault');
                var iconDone = document.getElementById('copyIconDone');
                btn.classList.add('copied');
                if (label) label.innerText = 'Copied!';
                if (iconDef) iconDef.style.display = 'none';
                if (iconDone) iconDone.style.display = 'inline';

                setTimeout(function () {
                    btn.classList.remove('copied');
                    if (label) label.innerText = 'Copy';
                    if (iconDef) iconDef.style.display = 'inline';
                    if (iconDone) iconDone.style.display = 'none';
                }, 1500);
            }

            function fallbackCopy() {
                var ta = document.createElement('textarea');
                ta.value = deviceIdText;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                ta.setSelectionRange(0, deviceIdText.length);
                try {
                    if (document.execCommand('copy')) {
                        showCopied();
                    } else {
                        alert('Failed to copy Device ID.');
                    }
                } catch (err) {
                    alert('Failed to copy Device ID: ' + err);
                } finally {
                    document.body.removeChild(ta);
                }
            }

            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                navigator.clipboard.writeText(deviceIdText).then(showCopied).catch(fallbackCopy);
            } else {
                fallbackCopy();
            }
        }

        function handleActivateSubmit(form) {
            var btn = document.getElementById('activateSubmitBtn');
            var label = document.getElementById('activateBtnLabel');
            if (btn && label) {
                btn.disabled = true;
                label.innerText = 'Verifying...';
            }
            return true;
        }
    </script>
</body>

</html>
<?php
    return;
endif;

if (!isset($__runtime) || !($__runtime instanceof \App\Domain\Identity\IdentityRepository)) {
    throw new \RuntimeException('Fatal System Error: Core runtime instance missing from view context.');
}
$__runtime->assertRuntimeAuthorized();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Display Signage Valas</title>
    <meta name="description" content="Display Signage Valas Dashboard powered by Slim Framework v4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= $basePath; ?>/img/favicon.png">

    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/dashboard.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section1.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section2.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section3.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section4.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section5.css">
    <link rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/section6.css">

    <script src="<?= $basePath; ?>/js/toast.js"></script>
</head>

<body>
    <!-- LOADING SCREEN -->
    <style>
        #loading-screen {
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(244, 247, 251, 0.94);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease;
        }

        [data-theme="dark"] #loading-screen {
            background: rgba(11, 17, 32, 0.94);
        }

        #loading-screen.hide {
            opacity: 0;
            pointer-events: none;
        }

        .loading-svg {
            width: 64px;
            height: 64px;
            animation: pulse 1.1s infinite alternate;
        }

        @keyframes pulse {
            0% { filter: drop-shadow(0 0 0 #6366f1); }
            100% { filter: drop-shadow(0 0 12px #0ea5e9); }
        }

        .loading-text {
            margin-top: 14px;
            text-align: center;
            font-size: 0.98rem;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            animation: fadein 1.1s infinite alternate;
        }

        @keyframes fadein {
            0% { opacity: 0.55; }
            100% { opacity: 1; }
        }
    </style>
    <div id="loading-screen">
        <div style="display: flex; flex-direction: column; align-items: center;">
            <svg class="loading-svg" viewBox="0 0 50 50">
                <circle cx="25" cy="25" r="20" fill="none" stroke="#6366f1" stroke-width="5" stroke-linecap="round"
                    stroke-dasharray="31.4 31.4" stroke-dashoffset="0">
                    <animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.9s"
                        repeatCount="indefinite" />
                </circle>
            </svg>
            <div class="loading-text">Loading Dashboard...</div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            const loading = document.getElementById('loading-screen');
            if (loading) {
                loading.classList.add('hide');
                setTimeout(() => loading.style.display = 'none', 500);
            }
        });
    </script>

    <button class="sidebar-toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <div class="sidebar-wrapper" id="sidebarMenu">
        <!-- Sidebar Logo -->
        <div class="sidebar-logo">
            <img src="<?= $basePath; ?>/img/favicon.png" alt="DSV Logo" class="sidebar-logo-img">
            <span class="topbar-header-text">Dashboard</span>
        </div>

        <!-- Clock Badge -->
        <div id="digital-clock" class="digital-clock-badge"></div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            <button class="nav-item-btn <?= ($activeTab === 1) ? 'active' : ''; ?>" onclick="switchMenu(1)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>Update Titles
            </button>
            <button class="nav-item-btn <?= ($activeTab === 2) ? 'active' : ''; ?>" onclick="switchMenu(2)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </span>Update Valas
            </button>
            <button class="nav-item-btn <?= ($activeTab === 3) ? 'active' : ''; ?>" onclick="switchMenu(3)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="22" x2="21" y2="22"></line>
                        <line x1="6" y1="18" x2="6" y2="11"></line>
                        <line x1="10" y1="18" x2="10" y2="11"></line>
                        <line x1="14" y1="18" x2="14" y2="11"></line>
                        <line x1="18" y1="18" x2="18" y2="11"></line>
                        <polygon points="12 2 20 7 4 7"></polygon>
                    </svg>
                </span>Update Deposits
            </button>
            <button class="nav-item-btn <?= ($activeTab === 4) ? 'active' : ''; ?>" onclick="switchMenu(4)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle>
                        <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle>
                        <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle>
                        <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle>
                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
                    </svg>
                </span>Themes
            </button>
            <button class="nav-item-btn <?= ($activeTab === 5) ? 'active' : ''; ?>" onclick="switchMenu(5)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                    </svg>
                </span>Videos
            </button>
            <button class="nav-item-btn <?= ($activeTab === 6) ? 'active' : ''; ?>" onclick="switchMenu(6)">
                <span class="nav-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </span>Settings
            </button>
        </nav>

        <!-- Sidebar Footer Actions -->
        <div class="sidebar-footer-actions">
            <button id="theme-toggle-btn" class="icon-btn-sidebar" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg><span>Dark</span>
            </button>
            <a href="<?= $urlFor('signage.show'); ?>" class="valas-link-sidebar" title="Show Valas View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg><span>Show Valas</span>
            </a>
        </div>

        <!-- Display Current Option Badge -->
        <?php if (!empty($currentOption)): ?>
            <div class="topbar-badge-option-wrapper">
                <span class="topbar-badge-option">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" class="topbar-badge-svg">
                        <circle cx="12" cy="12" r="12" fill="#38bdf8" />
                        <text x="12" y="16" text-anchor="middle" font-size="13" fill="#fff" font-family="monospace" font-weight="bold"><?php echo htmlspecialchars((string)$currentOption); ?></text>
                    </svg>
                    Option: <b><?php echo htmlspecialchars((string)$currentOption); ?> View</b>
                </span>
            </div>
        <?php endif; ?>

        <!-- Sidebar Footer License & Device Status -->
        <div class="sidebar-license-footer">
            <div class="license-status-wrapper">
                <span class="license-label">Status:</span>
                <?php if (!empty($licenseStatus['is_licensed'])): ?>
                    <span class="badge-verified" title="License is valid and verified">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Verified
                    </span>
                <?php else: ?>
                    <span class="badge-unverified" title="Device is not activated">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:2px;">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        Unactivated
                    </span>
                <?php endif; ?>
            </div>
            <div class="device-id-wrapper">
                <span class="device-label">Device ID:</span>
                <?php $devId = $licenseStatus['device_id'] ?? null; ?>
                <span class="device-id-code" id="sidebarDeviceId" title="<?php echo htmlspecialchars($devId ?? 'Not Detected'); ?>">
                    <?php echo htmlspecialchars($devId ? substr($devId, 0, 13) . '...' : 'NONE'); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="main-content-wrapper">
        <?php
        include __DIR__ . '/sections/titles.php';
        include __DIR__ . '/sections/valas.php';
        include __DIR__ . '/sections/deposit.php';
        include __DIR__ . '/sections/themes.php';
        include __DIR__ . '/sections/videos.php';
        include __DIR__ . '/sections/settings.php';
        ?>
    </div>

    <div id="toast-container"></div>

    <?php if (!empty($flashMessages)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php foreach ($flashMessages as $msg): ?>
                    showToast(<?php echo json_encode($msg['message']); ?>, <?php echo json_encode($msg['type']); ?>);
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>

    <script>window.APP_BASE_PATH = <?= json_encode((string)$basePath, JSON_UNESCAPED_SLASHES); ?>;</script>
    <script src="<?= $basePath; ?>/js/sidebar.js"></script>
    <script src="<?= $basePath; ?>/js/clock.js"></script>
    <script src="<?= $basePath; ?>/js/scrolling.js"></script>
    <script src="<?= $basePath; ?>/js/preview.js"></script>
    <script src="<?= $basePath; ?>/js/darkmode.js"></script>
    <script src="<?= $basePath; ?>/js/videos.js"></script>
    <script src="<?= $basePath; ?>/js/themes.js"></script>

    <?php if ($activeTab > 1): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                switchMenu(<?= $activeTab; ?>);
            });
        </script>
    <?php endif; ?>
</body>

</html>
