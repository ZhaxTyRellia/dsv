<!-- Valas Form View (Update Titles) -->
<section id="menu-section-1" class="menu-section <?= ($activeTab === 1) ? 'active' : ''; ?>">
    <div class="valas-header-container" style="display: flex; flex-direction: column; gap: 20px;">
        <form method="post" action="/dsv/titles" class="valas-header-form">
            <div class="valas-header-card" style="border-left: 4px solid #2563eb; background: #ffffff;">
                <div class="valas-header-card-top" style="background: #eff6ff; padding: 12px 16px; border-bottom: 1px solid #dbeafe;">
                    <h5 style="margin: 0; color: #1e40af; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <span>&#128178;</span> Valas Title (Permanen Display)
                    </h5>
                </div>
                <div class="valas-header-card-body" style="padding: 16px;">
                    <div class="valas-header-input-group" style="margin-bottom: 0;">
                        <small style="color: #475569; font-weight: 600;">(Valas Heading Tables):</small>
                        <input type="text" name="title_1" value="<?php echo htmlspecialchars($titles['title_1'] ?? ''); ?>" class="valas-header-input-text" placeholder="Contoh: Informasi Kurs Valuta Asing" required>
                    </div>
                    <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">*This title is permanent and always displayed on the Forex screen.</small>
                </div>
            </div>

            <div class="valas-header-card" style="margin-top: 15px;">
                <div class="valas-header-card-top" style="padding: 12px 16px;">
                    <h5 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Optional Titles (Option View: <?= $currentOptionInt; ?>)</h5>
                </div>
                <div class="valas-header-card-body" style="padding: 16px;">
                    <div class="valas-header-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                        <?php for ($k = 2; $k <= 9; $k++): ?>
                            <?php $isDisabled = ($k > ($currentOptionInt + 1)); ?>
                            <div class="valas-header-input-group" style="margin-bottom: 0; <?= $isDisabled ? 'opacity: 0.4;' : ''; ?>">
                                <small style="font-weight: 600;">Title <?= $k; ?>: <?= $isDisabled ? '<span style="color:#ef4444;">(Disabled)</span>' : ''; ?></small>
                                <input type="text" name="title_<?= $k; ?>"
                                    value="<?php echo htmlspecialchars($titles['title_' . $k] ?? ''); ?>"
                                    class="valas-header-input-text"
                                    <?= $isDisabled ? 'readonly' : ''; ?>
                                    placeholder="<?= $isDisabled ? 'Disabled in current Option View' : 'Enter Title ' . $k; ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_titles" class="valas-header-btn-save" style="margin-top: 15px; width: 100%;">
                Save Changes
            </button>
        </form>
    </div>
</section>
