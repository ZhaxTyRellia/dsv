<!-- Themes Configuration Section -->
<section id="menu-section-4" class="menu-section <?= ($activeTab === 4) ? 'active' : ''; ?>">
    <div class="custom-card">
        <div class="custom-card-header">
            <h5>Theme &amp; Template Settings</h5>
        </div>
        <div class="custom-card-body">
            <form method="post" action="<?= $urlFor('themes.update'); ?>">
                <div class="identity-row theme-setting-row">

                    <!-- Left Column: Radio Options -->
                    <div class="identity-col-left theme-col-left">
                        <h6 class="identity-section-title" style="display: grid; justify-content: center; font-size: 0.8rem;">Select Active Theme</h6>

                        <?php $currentTheme = $identity['themes'] ?? 'show-0.css'; ?>

                        <div class="theme-card-container">

                            <!-- Option 0 (Default) -->
                            <label id="card-theme-show-0.css" class="theme-card-label <?= ($currentTheme === 'show-0.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-0.css"
                                        <?= ($currentTheme === 'show-0.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-0.css')">
                                    <div>
                                        <strong class="theme-card-title">(Default)</strong>
                                        <small class="theme-card-subtitle">Default Themes</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 1 (Bank Jatim) -->
                            <label id="card-theme-show-1.css" class="theme-card-label <?= ($currentTheme === 'show-1.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-1.css"
                                        <?= ($currentTheme === 'show-1.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-1.css')">
                                    <div>
                                        <strong class="theme-card-title">Bank Jatim (Modern)</strong>
                                        <small class="theme-card-subtitle">Bank Jatim Theme</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                            <!-- Option 2 (BRI) -->
                            <label id="card-theme-show-2.css" class="theme-card-label <?= ($currentTheme === 'show-2.css') ? 'active-theme' : ''; ?>">
                                <div class="theme-card-content">
                                    <input type="radio" name="theme_layout" value="show-2.css"
                                        <?= ($currentTheme === 'show-2.css') ? 'checked' : ''; ?>
                                        onchange="selectThemeCard(this, 'show-2.css')">
                                    <div>
                                        <strong class="theme-card-title">Bank Rakyat Indonesia (Modern)</strong>
                                        <small class="theme-card-subtitle">BRI Theme</small>
                                    </div>
                                </div>
                                <span class="badge-status">Active</span>
                            </label>

                        </div>

                        <!-- Tombol Simpan -->
                        <div class="theme-action-wrapper">
                            <button type="submit" name="save_theme_setting" class="theme-btn-save">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Theme Setting
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Live Preview -->
                    <div class="identity-col-right theme-col-right">
                        <h6 class="identity-section-title" style="display: grid; justify-content: center; font-size: 0.8rem;">Signage Live Preview (Portrait)</h6>
                        <div class="theme-preview-container">
                            <iframe id="themePreviewFrame"
                                class="theme-preview-iframe"
                                src="<?= $urlFor('signage.show'); ?>?preview_theme=<?= htmlspecialchars((string)$currentTheme); ?>">
                            </iframe>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
