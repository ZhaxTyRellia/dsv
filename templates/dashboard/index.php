<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Display Signage Valas</title>
    <meta name="description" content="Display Signage Valas Dashboard powered by Slim Framework v4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/dsv/app/img/favicon.png">

    <link rel="stylesheet" type="text/css" href="/dsv/app/css/dashboard.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section1.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section2.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section3.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section4.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section5.css">
    <link rel="stylesheet" type="text/css" href="/dsv/app/css/section6.css">

    <script src="/dsv/app/js/toast.js"></script>
</head>

<body>
    <!-- LOADING SCREEN -->
    <style>
        #loading-screen {
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s;
        }

        #loading-screen.hide {
            opacity: 0;
            pointer-events: none;
        }

        .loading-svg {
            width: 70px;
            height: 70px;
            animation: pulse 1.2s infinite alternate;
        }

        @keyframes pulse {
            0% { filter: drop-shadow(0 0 0 #d9003c); }
            100% { filter: drop-shadow(0 0 12px #d9003c); }
        }

        .loading-text {
            margin-top: 18px;
            text-align: center;
            font-size: 1.1em;
            color: #d9003c;
            font-family: Arial, sans-serif;
            letter-spacing: 1px;
            animation: fadein 1.2s infinite alternate;
        }

        @keyframes fadein {
            0% { opacity: 0.6; }
            100% { opacity: 1; }
        }
    </style>
    <div id="loading-screen">
        <div style="display: flex; flex-direction: column; align-items: center;">
            <svg class="loading-svg" viewBox="0 0 50 50">
                <circle cx="25" cy="25" r="20" fill="none" stroke="#d9003c" stroke-width="5" stroke-linecap="round"
                    stroke-dasharray="31.4 31.4" stroke-dashoffset="0">
                    <animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s"
                        repeatCount="indefinite" />
                </circle>
            </svg>
            <div class="loading-text">Loading .....</div>
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

    <button class="sidebar-toggle-btn" onclick="toggleSidebar()">&#9776;</button>

    <div class="sidebar-wrapper" id="sidebarMenu">
        <!-- Sidebar Logo -->
        <div class="sidebar-logo">
            <span class="topbar-logo-icon">&#x26ED;</span>
            <span class="topbar-header-text">Dashboard</span>
        </div>

        <!-- Clock Badge -->
        <div id="digital-clock" class="digital-clock-badge"></div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            <button class="nav-item-btn <?= ($activeTab === 1) ? 'active' : ''; ?>" onclick="switchMenu(1)">
                <span class="nav-icon">&#x1F516;</span>Update Titles
            </button>
            <button class="nav-item-btn <?= ($activeTab === 2) ? 'active' : ''; ?>" onclick="switchMenu(2)">
                <span class="nav-icon">&#36;</span>Update Valas
            </button>
            <button class="nav-item-btn <?= ($activeTab === 3) ? 'active' : ''; ?>" onclick="switchMenu(3)">
                <span class="nav-icon">&#127974;</span>Update Deposito
            </button>
            <button class="nav-item-btn <?= ($activeTab === 4) ? 'active' : ''; ?>" onclick="switchMenu(4)">
                <span class="nav-icon">&#x1F3A8;</span>Themes
            </button>
            <button class="nav-item-btn <?= ($activeTab === 5) ? 'active' : ''; ?>" onclick="switchMenu(5)">
                <span class="nav-icon">&#x1F4FC;</span>Videos
            </button>
            <button class="nav-item-btn <?= ($activeTab === 6) ? 'active' : ''; ?>" onclick="switchMenu(6)">
                <span class="nav-icon">&#9881;</span>Setting
            </button>
        </nav>

        <!-- Sidebar Footer Actions -->
        <div class="sidebar-footer-actions">
            <button id="theme-toggle-btn" class="icon-btn-sidebar" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                🌙 Dark
            </button>
            <a href="/dsv/show" class="valas-link-sidebar" title="Show Valas View">&#x26F6; Show Valas</a>
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
                        <span class="badge-icon">✓</span> Verified
                    </span>
                <?php else: ?>
                    <span class="badge-unverified" title="Device is not activated">
                        ⚠ Unactivated
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

    <script src="/dsv/app/js/sidebar.js"></script>
    <script src="/dsv/app/js/clock.js"></script>
    <script src="/dsv/app/js/scrolling.js"></script>
    <script src="/dsv/app/js/preview.js"></script>
    <script src="/dsv/app/js/darkmode.js"></script>
    <script src="/dsv/app/js/videos.js"></script>
    <script src="/dsv/app/js/themes.js"></script>

    <?php if ($activeTab > 1): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                switchMenu(<?= $activeTab; ?>);
            });
        </script>
    <?php endif; ?>
</body>

</html>
