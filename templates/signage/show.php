<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Display Signage</title>
    <meta name="description" content="Display Signage Valas screen">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= $basePath; ?>/img/favicon.png">
    <link id="theme-stylesheet" rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/<?= htmlspecialchars((string)$activeTheme); ?>">
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

    <!-- HEADER -->
    <header class="main-header">
        <div class="header-container">
            <div class="header-identity">
                <div id="logo-container">
                    <?php if (!empty($logoUrl)): ?>
                        <img id="header-logo-img" src="<?= $basePath; ?>/img/logo/<?php echo htmlspecialchars((string)$logoUrl); ?>" alt="Logo" class="header-logo">
                    <?php endif; ?>
                </div>
                <div class="header-text">
                    <h1 id="header-title-text" class="header-title"><?php echo htmlspecialchars((string)($title ?? '')); ?></h1>
                    <p id="header-name-text" class="header-name"><?php echo htmlspecialchars((string)($name ?? '')); ?></p>
                </div>
            </div>
            <div class="header-addon">
                <span class="header-date">
                    <?php echo htmlspecialchars((string)$dateString); ?>
                </span>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="main-content-wrapper">
        <!-- Videos -->
        <div class="content-video">
            <?php if (!empty($playlist)): ?>
                <?php $firstVideo = $playlist[0]; ?>
                <video autoplay muted playsinline class="video-element" id="signagePlayer">
                    <source src="<?php echo $firstVideo; ?>" type="video/mp4">
                </video>

                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        const playlist = <?php echo json_encode($playlist); ?>;
                        const player = document.getElementById('signagePlayer');
                        let currentVideoIndex = 0;

                        if (player && playlist.length > 0) {
                            player.addEventListener('ended', function() {
                                currentVideoIndex++;
                                if (currentVideoIndex >= playlist.length) {
                                    currentVideoIndex = 0;
                                }
                                player.src = playlist[currentVideoIndex];
                                player.load();
                                player.play().catch(function(error) {
                                    console.log("Autoplay blocked or error: ", error);
                                });
                            });
                        }
                    });
                </script>
            <?php else: ?>
                <div style="color: #fff; padding: 20px;"></div>
            <?php endif; ?>
        </div>

        <!-- Valas -->
        <div class="content-kurs">
            <div class="kurs-header-title">
                <?php echo htmlspecialchars((string)($titles['title_1'] ?? 'Informasi Kurs Valuta Asing')); ?>
            </div>
            <div class="table-scroll-container" id="kursTableContainer">
                <table class="kurs-table">
                    <thead>
                        <tr>
                            <th>Mata Uang</th>
                            <th>Nilai Jual</th>
                            <th>Nilai Beli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($valasList)): ?>
                            <?php foreach ($valasList as $kurs): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($kurs['flag_image'])): ?>
                                            <img src="<?= $basePath; ?>/img/flags/svg/<?php echo htmlspecialchars((string)$kurs['flag_image']); ?>.svg" alt="Flag" class="flag-img">
                                        <?php endif; ?>
                                        <span class="currency-code"><?php echo htmlspecialchars((string)($kurs['currency_code'] ?? '')); ?></span>
                                        <span class="currency-full-name">| <?php echo htmlspecialchars((string)($kurs['currency_name'] ?? '')); ?></span>
                                    </td>
                                    <td class="val-beli">
                                        Rp <?php echo number_format((float)($kurs['buy_rate'] ?? 0), 2, ',', '.'); ?>
                                    </td>
                                    <td class="val-jual">
                                        Rp <?php echo number_format((float)($kurs['sell_rate'] ?? 0), 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; padding: 20px;">Tidak ada data kurs saat ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="marquee-sticky-wrapper">
        <div class="marquee-container">
            <marquee id="marquee-text" behavior="scroll" direction="left" scrollamount="6" class="marquee-element">
                <?php echo htmlspecialchars((string)$marquee); ?>
            </marquee>
        </div>
        <div class="clock-date">
            <span id="clock">00:00:00</span>
        </div>
    </div>

    <script src="<?= $basePath; ?>/js/show_clock.js"></script>
    <script src="<?= $basePath; ?>/js/show_identity.js"></script>
    <script src="<?= $basePath; ?>/js/auto_scroll_table.js"></script>
    <script src="<?= $basePath; ?>/js/themes_automatic.js"></script>
</body>

</html>
