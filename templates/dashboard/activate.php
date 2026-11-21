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
            --bg-0: #050814;
            --bg-1: #0b1120;
            --card-bg: rgba(15, 23, 42, 0.78);
            --card-border: rgba(148, 163, 184, 0.16);
            --surface: rgba(2, 6, 23, 0.65);
            --surface-border: rgba(56, 189, 248, 0.22);
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --indigo: #6366f1;
            --emerald: #34d399;
            --amber: #fbbf24;
            --rose: #fb7185;
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
            color: var(--text);
            background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.14), transparent 42%),
                        radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.16), transparent 45%),
                        linear-gradient(155deg, var(--bg-0) 0%, var(--bg-1) 100%);
            position: relative;
            overflow-x: hidden;
        }

        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
            animation: floatGlow 10s ease-in-out infinite alternate;
        }

        .ambient-orb-1 {
            width: 320px;
            height: 320px;
            background: #0284c7;
            top: -80px;
            left: -80px;
        }

        .ambient-orb-2 {
            width: 300px;
            height: 300px;
            background: #4f46e5;
            bottom: -80px;
            right: -80px;
            animation-delay: -5s;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(18px, -14px) scale(1.08); }
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.45; transform: scale(0.8); }
        }

        .activation-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: var(--card-bg);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65),
                        inset 0 1px 0 rgba(255, 255, 255, 0.08);
            overflow: hidden;
            animation: fadeSlideUp 0.35s ease-out;
        }

        .card-accent-bar {
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, #38bdf8 0%, #6366f1 50%, #a855f7 100%);
        }

        .card-body {
            padding: 32px 28px 26px 28px;
        }

        .emblem-wrap {
            width: 56px;
            height: 56px;
            margin: 0 auto 16px auto;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.18), rgba(99, 102, 241, 0.22));
            border: 1px solid rgba(56, 189, 248, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            box-shadow: 0 10px 25px -5px rgba(56, 189, 248, 0.28);
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
            padding: 4px 10px;
            border-radius: 999px;
        }

        .pill-status {
            color: var(--amber);
            background: rgba(251, 191, 36, 0.12);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .pill-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--amber);
            animation: pulseDot 1.6s infinite;
        }

        .pill-platform {
            color: #cbd5e1;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.22);
        }

        h1 {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            font-size: 13.5px;
            line-height: 1.55;
            color: var(--muted);
            margin-bottom: 22px;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.45;
            margin-bottom: 16px;
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-error {
            color: #fecdd3;
            background: rgba(244, 63, 94, 0.14);
            border: 1px solid rgba(244, 63, 94, 0.35);
        }

        .alert-success {
            color: #a7f3d0;
            background: rgba(16, 185, 129, 0.14);
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #cbd5e1;
            margin-bottom: 7px;
        }

        .device-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: var(--surface);
            border: 1px solid var(--surface-border);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .device-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--accent);
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
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(30, 41, 59, 0.85);
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .btn-copy:hover {
            border-color: rgba(56, 189, 248, 0.5);
            background: rgba(56, 189, 248, 0.16);
            color: #ffffff;
        }

        .btn-copy.copied {
            border-color: rgba(52, 211, 153, 0.5);
            background: rgba(16, 185, 129, 0.2);
            color: var(--emerald);
        }

        .key-input {
            width: 100%;
            padding: 13px 14px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: var(--surface);
            color: #ffffff;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13.5px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 16px;
        }

        .key-input::placeholder {
            color: #64748b;
        }

        .key-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.18);
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
            background: linear-gradient(135deg, #0284c7 0%, #4f46e5 100%);
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.45);
            transition: transform 0.15s ease, filter 0.15s ease, opacity 0.15s ease;
        }

        .btn-activate:hover {
            filter: brightness(1.08);
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
            border-top: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(2, 6, 23, 0.35);
            text-align: center;
            font-size: 11.5px;
            color: #64748b;
            letter-spacing: 0.02em;
        }
    </style>
</head>

<body>
    <div class="ambient-orb ambient-orb-1"></div>
    <div class="ambient-orb ambient-orb-2"></div>

    <main class="activation-card">
        <div class="card-accent-bar"></div>
        <div class="card-body">
            <div class="emblem-wrap" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
