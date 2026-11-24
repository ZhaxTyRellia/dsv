<!-- Settings Section -->
<section id="menu-section-6" class="menu-section <?= ($activeTab === 6) ? 'active' : ''; ?>">
    <div class="custom-card">
        <div class="custom-card-header">
            <h5>Settings</h5>
        </div>
        <div class="custom-card-body">
            <form method="post" action="<?= $urlFor('identity.update'); ?>" enctype="multipart/form-data">
                <div class="identity-row">
                    <div class="identity-col-left">
                        <h6 class="identity-section-title">Identity Logo</h6>

                        <div class="identity-logo-group">
                            <label class="identity-field-label">Logo Preview</label>

                            <div class="identity-preview-wrapper">
                                <?php $logoPath = $identity['logo'] ?? ''; ?>
                                <?php if (!empty($logoPath)): ?>
                                    <div class="identity-image-container">
                                        <img id="logo-preview" src="<?= $basePath; ?>/img/logo/<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" class="identity-logo-preview">
                                    </div>
                                    <label class="identity-remove-label">
                                        <input type="checkbox" name="remove_logo" value="1" class="identity-checkbox-align">
                                        <span>Remove logo</span>
                                    </label>
                                <?php else: ?>
                                    <div class="identity-image-container">
                                        <img id="logo-preview" src="" alt="Logo Preview" class="identity-logo-preview identity-logo-preview-hidden">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <input type="file" name="logo" id="logo-input" accept="image/*" class="identity-file-input">
                            <small class="identity-small-info">Allowed: jpg, jpeg, png, gif, webp. Max 5MB.</small>
                        </div>
                    </div>

                    <div class="identity-col-right">
                        <h6 class="identity-section-title">Identity Settings</h6>

                        <div class="identity-input-group">
                            <label class="identity-field-label">Title</label>
                            <input type="text" name="title" placeholder="Enter title" required value="<?php echo htmlspecialchars((string)($identity['title'] ?? '')); ?>" class="identity-input-text">
                        </div>

                        <div class="identity-input-group">
                            <label class="identity-field-label">Name</label>
                            <input type="text" name="name" placeholder="Enter name" required value="<?php echo htmlspecialchars((string)($identity['name'] ?? '')); ?>" class="identity-input-text">
                        </div>

                        <div class="identity-input-group">
                            <label class="identity-field-label">Running Text</label>
                            <input type="text" name="marquee" placeholder="Enter running text" required value="<?php echo htmlspecialchars((string)($identity['marquee'] ?? '')); ?>" class="identity-input-text">
                        </div>

                        <div class="identity-action-group">
                            <button type="submit" name="change_identity_btn" class="identity-btn-submit">Save Identity Changes</button>
                        </div>
                    </div>
                </div>
            </form>

            <hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

            <div class="identity-row">
                <div class="identity-col-left">
                    <?php
                    $cfgDepositCount = (int)($displayConfig['deposit_count'] ?? $currentOptionInt);
                    $cfgSlideMode = (string)($displayConfig['slide_mode'] ?? 'auto');
                    $cfgSlideDuration = (int)($displayConfig['slide_duration'] ?? 10);
                    $cfgOrientation = (string)($displayConfig['orientation'] ?? 'auto');
                    ?>
                    <form action="<?= $urlFor('option.update'); ?>" method="POST" class="inner-modal-form">
                        <h6 class="identity-section-title">Signage Display &amp; Auto-Slide Engine</h6>

                        <div class="identity-input-group" style="margin-top: 12px;">
                            <label for="slide_mode" class="identity-field-label">Presentation &amp; Slide Mode</label>
                            <select name="slide_mode" id="slide_mode" class="identity-input-text">
                                <option value="auto" <?= ($cfgSlideMode === 'auto') ? 'selected' : ''; ?>>Auto-Slide Carousel (Forex Board + Deposit Slides + Matrix)</option>
                                <option value="split" <?= ($cfgSlideMode === 'split') ? 'selected' : ''; ?>>Split Executive View (Permanent Forex + Auto-Slide Deposits)</option>
                                <option value="forex_only" <?= ($cfgSlideMode === 'forex_only') ? 'selected' : ''; ?>>Pinned Forex Counter Only (Manual / Continuous Forex)</option>
                            </select>
                        </div>

                        <div class="identity-input-group">
                            <label for="identity_option_main" class="identity-field-label">Active Deposit &amp; Product Rate Tables (0-8)</label>
                            <select name="identity_option" id="identity_option_main" class="identity-input-text">
                                <option value="0" <?= ($cfgDepositCount === 0) ? 'selected' : ''; ?>>0 Tables (Forex Counter Only)</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($cfgDepositCount === $i) ? 'selected' : ''; ?>><?= $i; ?> Active Deposit Table<?= $i > 1 ? 's' : ''; ?> (View <?= $i; ?>)</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="identity-input-group" style="margin-bottom:0;">
                                <label for="slide_duration" class="identity-field-label">Slide Interval</label>
                                <select name="slide_duration" id="slide_duration" class="identity-input-text">
                                    <?php foreach ([6 => '6 Seconds', 10 => '10 Seconds (Default)', 15 => '15 Seconds', 20 => '20 Seconds', 30 => '30 Seconds'] as $sec => $lbl): ?>
                                        <option value="<?= $sec; ?>" <?= ($cfgSlideDuration === $sec) ? 'selected' : ''; ?>><?= $lbl; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="identity-input-group" style="margin-bottom:0;">
                                <label for="orientation_mode" class="identity-field-label">Screen Orientation</label>
                                <select name="orientation_mode" id="orientation_mode" class="identity-input-text">
                                    <option value="auto" <?= ($cfgOrientation === 'auto') ? 'selected' : ''; ?>>Auto-Detect Screen Ratio</option>
                                    <option value="landscape" <?= ($cfgOrientation === 'landscape') ? 'selected' : ''; ?>>Landscape 16:9 Widescreen TV</option>
                                    <option value="portrait" <?= ($cfgOrientation === 'portrait') ? 'selected' : ''; ?>>Portrait 9:16 Vertical Kiosk</option>
                                </select>
                            </div>
                        </div>

                        <div class="identity-action-group">
                            <button type="submit" name="set_identity_option" class="identity-btn-submit identity-btn-option">
                                Save Display &amp; Slide Settings
                            </button>
                        </div>
                    </form>
                </div>

                <div class="identity-col-right">
                    <form action="<?= $urlFor('database.reset'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to reset the database to factory defaults? All Valas, Deposit, Titles, and Video data will be cleared. Device license will be preserved.');">
                        <h6 class="identity-section-title">Factory Reset Database</h6>
                        <div class="form-group" style="width: 100%; margin-top: 15px;">
                            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 12px 0; line-height: 1.5;">
                                Restore all database tables to initial factory state. All currency rates, deposit tables, custom titles, and video playlists will be truncated to 0, while restoring default identity settings. Your device license will remain intact and preserved.
                            </p>
                        </div>
                        <div class="identity-action-group">
                            <button type="submit" name="reset_database_btn" class="identity-btn-submit identity-btn-danger" style="margin-top: 5px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.25);">
                                Reset to Factory Default
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

            <?php
            $licDeviceId = (string)($licenseStatus['device_id'] ?? 'Not Detected');
            $licPlatformRaw = strtolower((string)($licenseStatus['platform'] ?? 'unknown'));
            $licPlatformLabel = match ($licPlatformRaw) {
                'windows' => 'Windows Server',
                'linux' => 'Linux Server',
                'macos' => 'macOS Server',
                'android', 'android_kiosign' => 'Android Server',
                default => 'Universal Server',
            };
            $licActivatedAt = (string)($licenseStatus['activated_at'] ?? '-');
            $licFp = (string)($licenseStatus['public_key_fp'] ?? '-');
            ?>
            <div class="identity-row">
                <div class="identity-col-left">
                    <h6 class="identity-section-title">Device License Status</h6>
                    <div class="license-info-panel">
                        <div class="license-meta-row">
                            <span class="license-badge-active">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Active &amp; Verified
                            </span>
                            <span class="license-badge-platform"><?= htmlspecialchars($licPlatformLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="license-detail-line">
                            <span>Activated At:</span>
                            <strong><?= htmlspecialchars($licActivatedAt !== '' ? $licActivatedAt : '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                        </div>
                        <div class="license-detail-line">
                            <span>Key Fingerprint:</span>
                            <strong><code><?= htmlspecialchars($licFp, ENT_QUOTES, 'UTF-8'); ?></code></strong>
                        </div>
                        <div class="license-device-box">
                            <span id="settingsDeviceIdText"><?= htmlspecialchars($licDeviceId, ENT_QUOTES, 'UTF-8'); ?></span>
                            <button type="button" class="btn-copy-inline" onclick="copySettingsDeviceId(this)">Copy ID</button>
                        </div>
                    </div>
                </div>

                <div class="identity-col-right">
                    <form action="<?= $urlFor('activation.deactivate'); ?>" method="POST" onsubmit="return confirm('Unbind this device license? The application will immediately return to the activation screen and require an Activate Key to be used again.');">
                        <h6 class="identity-section-title">Unbind Device License</h6>
                        <div class="form-group" style="width: 100%; margin-top: 15px;">
                            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 12px 0; line-height: 1.5;">
                                Unbind the stored Activate Key from this device to return to the <strong>Activate Key</strong> screen (for example, before transferring a license or re-testing activation). Your Forex, Deposit, Title, and Video data will remain intact.
                            </p>
                        </div>
                        <div class="identity-action-group">
                            <button type="submit" name="deactivate_license_btn" class="identity-btn-submit identity-btn-unbind">
                                Unbind License
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copySettingsDeviceId(btn) {
    var el = document.getElementById('settingsDeviceIdText');
    if (!el) return;
    var text = (el.innerText || el.textContent || '').trim();
    if (!text || text === 'Not Detected') return;

    function done() {
        var orig = btn.innerText;
        btn.innerText = 'Copied!';
        setTimeout(function () { btn.innerText = orig; }, 1500);
    }

    function fallback() {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        ta.setSelectionRange(0, text.length);
        try {
            if (document.execCommand('copy')) {
                done();
            }
        } finally {
            document.body.removeChild(ta);
        }
    }

    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
        navigator.clipboard.writeText(text).then(done).catch(fallback);
    } else {
        fallback();
    }
}
</script>
