<?php
$platformRaw = strtolower(trim((string)($platform ?? 'unknown')));
$platformLabel = match ($platformRaw) {
    'windows' => 'Windows Host',
    'android_kiosign' => 'Android KioSign',
    'linux' => 'Linux Host',
    default => 'Hardware Device',
};
$deviceIdStr = trim((string)($device_id ?? ''));
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
            width: 58px;
            height: 58px;
            margin: 0 auto 16px auto;
            border-radius: 16px;
            background: linear-gradient(135deg, #6366f1 0%, #0ea5e9 55%, #10b981 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 10px 22px -4px rgba(14, 165, 233, 0.35);
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
            <div class="emblem-wrap" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <circle cx="12" cy="11" r="2"/>
                    <path d="M12 13v3"/>
                </svg>
            </div>

            <div class="pill-row">
                <span class="pill pill-status"><span class="pill-dot"></span>Activation Required</span>
                <span class="pill pill-platform"><?= htmlspecialchars($platformLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <h1>Application Activation</h1>
            <p class="subtitle">
                Salin <strong>Device ID</strong> perangkat ini untuk menghasilkan <strong>Activate Key</strong> resmi, lalu tempel di bawah untuk membuka akses penuh.
            </p>

            <?php if (!empty($flashMessages) && is_array($flashMessages)): ?>
                <?php foreach ($flashMessages as $msg): ?>
                    <?php
                    $isErr = in_array((string)($msg['type'] ?? ''), ['danger', 'error'], true);
                    ?>
                    <div class="alert <?= $isErr ? 'alert-error' : 'alert-success'; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg id="copyIconDefault" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <svg id="copyIconDone" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
