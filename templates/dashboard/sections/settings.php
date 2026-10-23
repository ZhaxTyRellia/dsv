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
                    <form action="<?= $urlFor('option.update'); ?>" method="POST" class="inner-modal-form">
                        <h6 class="identity-section-title">Display Layout Option</h6>
                        <div class="form-group" style="width: 100%; margin-top: 15px;">
                            <label for="identity_option_main" class="inner-modal-label-block" style="margin-bottom: 8px; text-align: left; display: block; font-size: 0.85rem; color: #475569; font-weight: 600;">Select View Option (1-8)</label>
                            <select name="identity_option" id="identity_option_main" class="identity-input-text" style="width: 100%;">
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($currentOptionInt == $i) ? 'selected' : ''; ?>>View <?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="identity-action-group">
                            <button type="submit" name="set_identity_option" class="identity-btn-submit" style="margin-top: 15px;">
                                Save Option
                            </button>
                        </div>
                    </form>
                </div>

                <div class="identity-col-right">
                    <form action="<?= $urlFor('database.reset'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to reset the database to factory defaults? All Valas, Deposit, Titles, and Video data will be cleared.');">
                        <h6 class="identity-section-title">Factory Reset Database</h6>
                        <div class="form-group" style="width: 100%; margin-top: 15px;">
                            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 12px 0; line-height: 1.5;">
                                Restore all database tables to initial factory state. All currency rates, deposit tables, custom titles, and video playlists will be truncated to 0, while restoring default identity settings.
                            </p>
                        </div>
                        <div class="identity-action-group">
                            <button type="submit" name="reset_database_btn" class="identity-btn-submit" style="margin-top: 5px; background-color: #dc2626;">
                                Reset to Factory Default
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
