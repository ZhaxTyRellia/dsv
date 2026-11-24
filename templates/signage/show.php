<?php
$slideMode = (string)($displayConfig['slide_mode'] ?? 'auto');
$slideDuration = (int)($displayConfig['slide_duration'] ?? 10);
$orientation = (string)($displayConfig['orientation'] ?? 'auto');
$forexTitle = trim((string)($titles['title_1'] ?? 'FOREIGN EXCHANGE COUNTER RATES'));
if ($forexTitle === '') {
    $forexTitle = 'FOREIGN EXCHANGE COUNTER RATES';
}
$spotlights = array_slice($valasList ?? [], 0, 4);
$depositSlides = $activeDepositSlides ?? [];
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars((string)($title ?: 'Display Signage Valas'), ENT_QUOTES, 'UTF-8'); ?> — Digital Signage</title>
    <meta name="description" content="Enterprise BUMN Digital Signage Forex & Treasury Display">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= $basePath; ?>/img/favicon.png">
    <link id="theme-stylesheet" rel="stylesheet" type="text/css" href="<?= $basePath; ?>/css/<?= htmlspecialchars((string)$activeTheme, ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body
    data-slide-mode="<?= htmlspecialchars($slideMode, ENT_QUOTES, 'UTF-8'); ?>"
    data-slide-duration="<?= $slideDuration; ?>"
    data-orientation="<?= htmlspecialchars($orientation, ENT_QUOTES, 'UTF-8'); ?>"
    data-auto-cycle-themes="<?= !empty($autoCycleThemes) ? '1' : '0'; ?>"
>
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-container">
            <div class="header-identity">
                <div id="logo-container" class="header-logo-box">
                    <?php if (!empty($logoUrl)): ?>
                        <img id="header-logo-img" src="<?= $basePath; ?>/img/logo/<?= htmlspecialchars((string)$logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" class="header-logo" onerror="this.src='<?= $basePath; ?>/img/favicon.png';">
                    <?php else: ?>
                        <img id="header-logo-img" src="<?= $basePath; ?>/img/favicon.png" alt="DSV Logo" class="header-logo">
                    <?php endif; ?>
                </div>
                <div class="header-text">
                    <div class="header-badge-live">
                        <span class="live-dot"></span>
                        <span>OFFICIAL TREASURY &amp; FOREX BOARD</span>
                    </div>
                    <h1 id="header-title-text" class="header-title"><?= htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p id="header-name-text" class="header-name"><?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

            <div class="header-addon">
                <div class="header-datetime-card">
                    <div class="header-date">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span><?= htmlspecialchars((string)$dateString, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="header-clock-row">
                        <span id="clock">00:00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN SIGNAGE WORKSPACE -->
    <main class="signage-workspace">
        <!-- LEFT / TOP PANEL: VIDEO OR TREASURY SPOTLIGHT + TOP 4 CURRENCY STRIP -->
        <aside class="signage-media-column">
            <div class="media-player-card">
                <?php if (!empty($playlist)): ?>
                    <?php $firstVideo = $playlist[0]; ?>
                    <video autoplay muted playsinline<?= count($playlist) === 1 ? ' loop' : ''; ?> class="video-element" id="signagePlayer">
                        <source src="<?= $firstVideo; ?>" type="video/mp4">
                    </video>
                    <div class="video-ad-overlay" style="position:absolute;bottom:10px;right:10px;display:flex;align-items:center;gap:6px;z-index:10;background:rgba(4,9,20,0.72);backdrop-filter:blur(8px);padding:5px 10px;border-radius:999px;border:1px solid rgba(255,255,255,0.16);">
                        <span id="videoTrackBadge" style="font-size:0.68rem;font-weight:800;letter-spacing:0.6px;color:#e2e8f0;padding:0 4px;">
                            AD 1/<?= count($playlist); ?>
                        </span>
                        <button type="button" id="btnVideoMute" title="Toggle Video Audio (Mute/Unmute)" style="background:transparent;border:none;color:#f8fafc;cursor:pointer;display:inline-flex;align-items:center;padding:3px;">
                            <svg id="iconAudioMuted" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <line x1="23" y1="9" x2="17" y2="15"></line>
                                <line x1="17" y1="9" x2="23" y2="15"></line>
                            </svg>
                            <svg id="iconAudioUnmuted" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            </svg>
                        </button>
                        <?php if (count($playlist) > 1): ?>
                            <button type="button" id="btnVideoNext" title="Next Promotional Video" style="background:transparent;border:none;color:#f8fafc;cursor:pointer;display:inline-flex;align-items:center;padding:3px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="5 4 15 12 5 20 5 4"></polygon>
                                    <line x1="19" y1="5" x2="19" y2="19"></line>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const playlist = <?= json_encode($playlist, JSON_UNESCAPED_SLASHES); ?>;
                            const player = document.getElementById('signagePlayer');
                            const badge = document.getElementById('videoTrackBadge');
                            const btnMute = document.getElementById('btnVideoMute');
                            const btnNext = document.getElementById('btnVideoNext');
                            const iconMuted = document.getElementById('iconAudioMuted');
                            const iconUnmuted = document.getElementById('iconAudioUnmuted');
                            let idx = 0;

                            function playIndex(nextIdx) {
                                idx = (nextIdx + playlist.length) % playlist.length;
                                player.src = playlist[idx];
                                player.load();
                                player.play().catch(function() {});
                                if (badge) {
                                    badge.textContent = 'AD ' + (idx + 1) + '/' + playlist.length;
                                }
                            }

                            if (player && playlist.length > 0) {
                                player.addEventListener('ended', function() {
                                    playIndex(idx + 1);
                                });
                            }
                            if (btnMute && player) {
                                btnMute.addEventListener('click', function() {
                                    player.muted = !player.muted;
                                    if (iconMuted && iconUnmuted) {
                                        iconMuted.style.display = player.muted ? 'inline' : 'none';
                                        iconUnmuted.style.display = player.muted ? 'none' : 'inline';
                                    }
                                });
                            }
                            if (btnNext && player && playlist.length > 1) {
                                btnNext.addEventListener('click', function() {
                                    playIndex(idx + 1);
                                });
                            }
                        });
                    </script>
                <?php else: ?>
                    <!-- BUMN Executive Treasury Visual Banner when no video is uploaded -->
                    <div class="treasury-hero-banner">
                        <div class="hero-top-row">
                            <span class="hero-tag">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                    <polyline points="17 6 23 6 23 12"></polyline>
                                </svg>
                                TREASURY MARKET DESK
                            </span>
                            <span class="hero-status">REAL-TIME COUNTER</span>
                        </div>
                        <div class="hero-center-content">
                            <img src="<?= $basePath; ?>/img/favicon.png" alt="Logo" class="hero-emblem-img">
                            <div>
                                <h2>Foreign Exchange &amp; Deposit Information</h2>
                                <p> competitive counter exchange rates and institutional time deposit yields updated directly from our Treasury Desk.</p>
                            </div>
                        </div>
                        <div class="hero-highlights-bar">
                            <div class="hero-stat-item">
                                <span>Active Currencies</span>
                                <strong><?= count($valasList ?? []); ?> Global Pairs</strong>
                            </div>
                            <div class="hero-stat-item">
                                <span>Rate Programs</span>
                                <strong><?= count($depositSlides) + 1; ?> Active Boards</strong>
                            </div>
                            <div class="hero-stat-item">
                                <span>Settlement</span>
                                <strong>Same-Day TT / BN</strong>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($spotlights)): ?>
                <div class="spotlight-grid">
                    <?php foreach ($spotlights as $sp): ?>
                        <?php
                        $buy = (float)($sp['buy_rate'] ?? 0);
                        $sell = (float)($sp['sell_rate'] ?? 0);
                        ?>
                        <div class="spotlight-card">
                            <div class="spotlight-head">
                                <div class="spotlight-curr">
                                    <?php if (!empty($sp['flag_image'])): ?>
                                        <span class="flag-wave flag-wave-sm">
                                            <img src="<?= $basePath; ?>/img/flags/svg/<?= htmlspecialchars((string)$sp['flag_image'], ENT_QUOTES, 'UTF-8'); ?>.svg" alt="" class="flag-img-sm">
                                        </span>
                                    <?php endif; ?>
                                    <span class="spotlight-code"><?= htmlspecialchars((string)($sp['currency_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <span class="spotlight-name"><?= htmlspecialchars((string)($sp['currency_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="spotlight-rates">
                                <div class="sp-rate-box">
                                    <small>BUY</small>
                                    <strong class="val-beli"><?= number_format($buy, 2, ',', '.'); ?></strong>
                                </div>
                                <div class="sp-rate-box">
                                    <small>SELL</small>
                                    <strong class="val-jual"><?= number_format($sell, 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>

        <!-- RIGHT / BOTTOM DATA STAGE: AUTO-SLIDE CAROUSEL OR SPLIT VIEW -->
        <section class="signage-data-column">
            <!-- Slide Controller Bar (Visible in Auto-Slide & Split modes) -->
            <div class="slide-control-bar">
                <div class="slide-indicator-group" id="slideIndicators"></div>
                <div class="slide-manual-controls">
                    <button type="button" class="ctrl-btn" id="btnPrevSlide" onclick="window.SignageCarousel && window.SignageCarousel.prev()" title="Previous Slide">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button type="button" class="ctrl-btn ctrl-btn-mode" id="btnTogglePause" onclick="window.SignageCarousel && window.SignageCarousel.togglePause()" title="Toggle Auto-Slide / Manual Pause">
                        <span id="slideModeBadge"><?= $slideMode === 'forex_only' ? 'PINNED' : 'AUTO'; ?></span>
                    </button>
                    <button type="button" class="ctrl-btn" id="btnNextSlide" onclick="window.SignageCarousel && window.SignageCarousel.next()" title="Next Slide">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button type="button" class="ctrl-btn" onclick="toggleSignageFullscreen()" title="Fullscreen Display">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
                    </button>
                </div>
                <div class="slide-progress-track">
                    <div class="slide-progress-fill" id="slideProgressBar"></div>
                </div>
            </div>

            <div class="slides-stage-wrapper">
                <!-- PRIMARY FOREX BOARD (Slide 0 in Auto-Slide, or Permanent Left/Top in Split & Forex-Only) -->
                <article class="signage-slide-panel slide-forex-primary active-slide" data-slide-title="<?= htmlspecialchars($forexTitle, ENT_QUOTES, 'UTF-8'); ?>" data-slide-kind="forex">
                    <div class="kurs-header-title">
                        <div class="board-title-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                            </svg>
                            <span><?= htmlspecialchars($forexTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <span class="board-unit-tag">IDR / Counter Rate</span>
                    </div>

                    <div class="table-scroll-container js-autoscroll-box" id="kursTableContainer">
                        <table class="kurs-table">
                            <thead>
                                <tr>
                                    <th style="text-align: left; padding-left: 18px;">CURRENCY</th>
                                    <th style="text-align: right;">BUY RATE (IDR)</th>
                                    <th style="text-align: right; padding-right: 18px;">SELL RATE (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($valasList)): ?>
                                    <?php foreach ($valasList as $kurs): ?>
                                        <tr>
                                            <td style="text-align: left; padding-left: 18px;">
                                                <div class="curr-cell-wrap">
                                                    <?php if (!empty($kurs['flag_image'])): ?>
                                                        <span class="flag-wave">
                                                            <img src="<?= $basePath; ?>/img/flags/svg/<?= htmlspecialchars((string)$kurs['flag_image'], ENT_QUOTES, 'UTF-8'); ?>.svg" alt="Flag" class="flag-img">
                                                        </span>
                                                    <?php endif; ?>
                                                    <div class="curr-text-group">
                                                        <span class="currency-code"><?= htmlspecialchars((string)($kurs['currency_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <span class="currency-full-name"><?= htmlspecialchars((string)($kurs['currency_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="val-beli" style="text-align: right;">
                                                Rp <?= number_format((float)($kurs['buy_rate'] ?? 0), 2, ',', '.'); ?>
                                            </td>
                                            <td class="val-jual" style="text-align: right; padding-right: 18px;">
                                                Rp <?= number_format((float)($kurs['sell_rate'] ?? 0), 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" style="text-align:center; padding: 28px;">No exchange rate data available.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <!-- DEPOSIT & INTEREST RATE SLIDES (1..N) -->
                <?php if ($slideMode !== 'forex_only' && !empty($depositSlides)): ?>
                    <?php foreach ($depositSlides as $depSlide): ?>
                        <article class="signage-slide-panel slide-deposit-panel" data-slide-title="<?= htmlspecialchars((string)$depSlide['title'], ENT_QUOTES, 'UTF-8'); ?>" data-slide-kind="deposit">
                            <div class="kurs-header-title deposit-header-accent">
                                <div class="board-title-left">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="3" y1="22" x2="21" y2="22"></line>
                                        <line x1="6" y1="18" x2="6" y2="11"></line>
                                        <line x1="10" y1="18" x2="10" y2="11"></line>
                                        <line x1="14" y1="18" x2="14" y2="11"></line>
                                        <line x1="18" y1="18" x2="18" y2="11"></line>
                                        <polygon points="12 2 20 7 4 7"></polygon>
                                    </svg>
                                    <span><?= htmlspecialchars((string)$depSlide['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <span class="board-unit-tag">Program Table #<?= (int)$depSlide['index']; ?></span>
                            </div>

                            <div class="table-scroll-container js-autoscroll-box">
                                <table class="kurs-table deposit-rate-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; padding-left: 18px;">TENOR / TERM</th>
                                            <th style="text-align: center;">INTEREST RATE (% p.a.)</th>
                                            <th style="text-align: right;">MIN. PLACEMENT</th>
                                            <th style="text-align: left; padding-right: 18px;">TIER / NOTES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($depSlide['rows'])): ?>
                                            <?php foreach ($depSlide['rows'] as $dRow): ?>
                                                <tr>
                                                    <td style="text-align: left; padding-left: 18px;">
                                                        <span class="tenor-pill"><?= htmlspecialchars((string)($dRow['tenor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="interest-rate-highlight"><?= number_format((float)($dRow['interest_rate'] ?? 0), 2, ',', '.'); ?>% <small>p.a.</small></span>
                                                    </td>
                                                    <td class="val-min-amount" style="text-align: right;">
                                                        <?= number_format((float)($dRow['min_amount'] ?? 0), 0, ',', '.'); ?>
                                                    </td>
                                                    <td class="val-desc-note" style="text-align: left; padding-right: 18px;">
                                                        <?= htmlspecialchars((string)($dRow['description'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" style="text-align:center; padding: 28px;">No rate tiers configured for this table yet.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- EXECUTIVE FOREX MATRIX GRID SLIDE (In Auto-Slide Mode when >= 4 currencies exist) -->
                <?php if ($slideMode === 'auto' && count($valasList ?? []) >= 4): ?>
                    <article class="signage-slide-panel slide-matrix-panel" data-slide-title="EXECUTIVE FOREX SUMMARY MATRIX" data-slide-kind="matrix">
                        <div class="kurs-header-title">
                            <div class="board-title-left">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                <span>EXECUTIVE FOREX SUMMARY MATRIX</span>
                            </div>
                            <span class="board-unit-tag">Quick Overview</span>
                        </div>
                        <div class="table-scroll-container js-autoscroll-box matrix-cards-stage">
                            <div class="matrix-grid-cards">
                                <?php foreach ($valasList as $mKurs): ?>
                                    <?php
                                    $mBuy = (float)($mKurs['buy_rate'] ?? 0);
                                    $mSell = (float)($mKurs['sell_rate'] ?? 0);
                                    $spreadPct = $mBuy > 0 ? (($mSell - $mBuy) / $mBuy) * 100 : 0;
                                    ?>
                                    <div class="matrix-item-card">
                                        <div class="matrix-item-top">
                                            <div class="curr-cell-wrap">
                                                <?php if (!empty($mKurs['flag_image'])): ?>
                                                    <span class="flag-wave">
                                                        <img src="<?= $basePath; ?>/img/flags/svg/<?= htmlspecialchars((string)$mKurs['flag_image'], ENT_QUOTES, 'UTF-8'); ?>.svg" alt="" class="flag-img">
                                                    </span>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="currency-code"><?= htmlspecialchars((string)($mKurs['currency_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                                                    <div class="currency-full-name"><?= htmlspecialchars((string)($mKurs['currency_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                                                </div>
                                            </div>
                                            <span class="spread-chip">Spread <?= number_format($spreadPct, 2); ?>%</span>
                                        </div>
                                        <div class="matrix-item-rates">
                                            <div>
                                                <small>BUY (IDR)</small>
                                                <strong class="val-beli">Rp <?= number_format($mBuy, 2, ',', '.'); ?></strong>
                                            </div>
                                            <div>
                                                <small>SELL (IDR)</small>
                                                <strong class="val-jual">Rp <?= number_format($mSell, 2, ',', '.'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- FOOTER MARQUEE TICKER -->
    <footer class="marquee-sticky-wrapper">
        <div class="marquee-badge">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>
            <span>INFO</span>
        </div>
        <div class="marquee-container">
            <marquee id="marquee-text" behavior="scroll" direction="left" scrollamount="6" class="marquee-element">
                <?= htmlspecialchars((string)$marquee, ENT_QUOTES, 'UTF-8'); ?>
            </marquee>
        </div>
        <div class="clock-date">
            <span id="clock-footer">LIVE</span>
        </div>
    </footer>

    <script>
        window.APP_BASE_PATH = <?= json_encode((string)$basePath, JSON_UNESCAPED_SLASHES); ?>;
        function toggleSignageFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function() {});
            } else if (document.exitFullscreen) {
                document.exitFullscreen().catch(function() {});
            }
        }
    </script>
    <script src="<?= $basePath; ?>/js/show_clock.js"></script>
    <script src="<?= $basePath; ?>/js/show_identity.js"></script>
    <script src="<?= $basePath; ?>/js/auto_scroll_table.js"></script>
    <script src="<?= $basePath; ?>/js/themes_automatic.js"></script>
</body>

</html>
