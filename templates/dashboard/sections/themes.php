<!-- Themes Configuration Section -->
<section id="menu-section-4" class="menu-section <?= ($activeTab === 4) ? 'active' : ''; ?>">
    <div class="custom-card">
        <div class="custom-card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <div>
                <h5>Signage Theme &amp; Visual Presets</h5>
                <small style="color:var(--text-muted);font-size:0.82rem;">Choose a manual BUMN institutional theme or enable automatic theme rotation</small>
            </div>
            <a href="<?= $urlFor('signage.show'); ?>" target="_blank" class="btn-submit" style="text-decoration:none;display:inline-flex;align-items:center;gap:7px;height:38px;padding:0 16px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                Open Live Signage
            </a>
        </div>
        <div class="custom-card-body">
            <form method="post" action="<?= $urlFor('themes.update'); ?>">
                <div class="identity-row theme-setting-row">

                    <!-- Left Column: Radio Options -->
                    <div class="identity-col-left theme-col-left">
                        <h6 class="identity-section-title">Select Active Signage Theme</h6>

                        <?php $currentTheme = $identity['themes'] ?? 'show-0.css'; ?>

                        <div class="theme-card-container">

                            <!-- Option 0: BUMN Sovereign Navy & Gold -->
                            <label id="card-theme-show-0.css" class="theme-card-label <?= ($currentTheme === 'show-0.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-0.css"
                                        <?= ($currentTheme === 'show-0.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-0.css')">
                                    <div>
                                        <strong class="theme-card-title">BUMN Sovereign Navy &amp; Gold</strong>
                                        <small class="theme-card-subtitle">Mandiri / BNI / Ministry Executive Palette</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 1: Nusantara Crimson & Platinum -->
                            <label id="card-theme-show-1.css" class="theme-card-label <?= ($currentTheme === 'show-1.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-1.css"
                                        <?= ($currentTheme === 'show-1.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-1.css')">
                                    <div>
                                        <strong class="theme-card-title">Nusantara Crimson &amp; Platinum</strong>
                                        <small class="theme-card-subtitle">Bank Jatim / BPD Regional Executive Theme</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 2: Corporate Cobalt Blue & Orange -->
                            <label id="card-theme-show-2.css" class="theme-card-label <?= ($currentTheme === 'show-2.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-2.css"
                                        <?= ($currentTheme === 'show-2.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-2.css')">
                                    <div>
                                        <strong class="theme-card-title">Corporate Cobalt Blue &amp; Orange</strong>
                                        <small class="theme-card-subtitle">BRI / National Enterprise Banking Theme</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 3: Syariah Emerald & Champagne Gold -->
                            <label id="card-theme-show-3.css" class="theme-card-label <?= ($currentTheme === 'show-3.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-3.css"
                                        <?= ($currentTheme === 'show-3.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-3.css')">
                                    <div>
                                        <strong class="theme-card-title">Syariah Emerald &amp; Champagne Gold</strong>
                                        <small class="theme-card-subtitle">BSI / Modern Islamic Treasury Theme</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 4: Automatic Theme Rotation (System Auto-Cycle) -->
                            <label id="card-theme-auto-cycle" class="theme-card-label <?= ($currentTheme === 'auto-cycle') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="auto-cycle"
                                        <?= ($currentTheme === 'auto-cycle') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'auto-cycle')">
                                    <div>
                                        <strong class="theme-card-title">Automatic Theme Rotation (System Auto)</strong>
                                        <small class="theme-card-subtitle">Automatically cycles through all 4 BUMN themes every 25s</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                        </div>

                        <div class="theme-action-wrapper">
                            <button type="submit" name="save_theme_setting" class="theme-btn-save">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Theme Setting
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Live Interactive Preview (Portrait & Landscape Switcher) -->
                    <div class="identity-col-right theme-col-right">
                        <div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;border-bottom:2px solid var(--border-color);padding-bottom:8px;">
                            <h6 class="identity-section-title" style="border:none;margin:0;padding:0;">Signage Live Preview</h6>
                            <div style="display:flex;gap:6px;">
                                <button type="button" id="btnPrevPortrait" onclick="setPreviewOrientation('portrait')" class="btn-action-edit" style="padding:4px 10px;font-size:11.5px;">9:16 Kiosk</button>
                                <button type="button" id="btnPrevLandscape" onclick="setPreviewOrientation('landscape')" class="btn-action-edit" style="padding:4px 10px;font-size:11.5px;">16:9 TV</button>
                            </div>
                        </div>
                        <div class="theme-preview-container" id="themePreviewContainer" style="transition:width 0.25s ease, height 0.25s ease;">
                            <iframe id="themePreviewFrame"
                                class="theme-preview-iframe"
                                src="<?= $urlFor('signage.show'); ?>?preview_theme=<?= htmlspecialchars((string)$currentTheme); ?>&amp;preview_orientation=portrait">
                            </iframe>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
