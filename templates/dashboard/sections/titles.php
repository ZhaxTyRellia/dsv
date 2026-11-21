<!-- Valas Form View (Update Titles) -->
<section id="menu-section-1" class="menu-section <?= ($activeTab === 1) ? 'active' : ''; ?>">
    <div class="valas-header-container">
        <div class="section-page-header">
            <div>
                <h2>Heading &amp; Table Titles</h2>
                <small>Customize permanent Forex heading and dynamic Deposit table titles</small>
            </div>
        </div>

        <form method="post" action="<?= $urlFor('titles.update'); ?>" class="valas-header-form">
            <div class="valas-header-card">
                <div class="valas-header-card-top">
                    <h5>
                        <span>&#128178;</span> Valas Title (Permanent Display)
                    </h5>
                </div>
                <div class="valas-header-card-body">
                    <div class="valas-header-input-group">
                        <small>Valas Heading Table</small>
                        <input type="text" name="title_1" value="<?php echo htmlspecialchars($titles['title_1'] ?? ''); ?>" class="valas-header-input-text" placeholder="Contoh: Informasi Kurs Valuta Asing" required>
                    </div>
                    <small style="color: var(--text-muted); font-size: 11.5px; margin-top: 6px; display: block;">*This primary title is permanent and always displayed on the Forex signage screen.</small>
                </div>
            </div>

            <div class="valas-header-card">
                <div class="valas-header-card-top">
                    <h5>
                        <span>&#128203;</span> Optional Deposit Titles (Active Option View: <?= $currentOptionInt; ?>)
                    </h5>
                </div>
                <div class="valas-header-card-body">
                    <div class="valas-header-form-grid">
                        <?php for ($k = 2; $k <= 9; $k++): ?>
                            <?php $isDisabled = ($k > ($currentOptionInt + 1)); ?>
                            <div class="valas-header-input-group" style="<?= $isDisabled ? 'opacity: 0.45;' : ''; ?>">
                                <small>Title <?= $k; ?> <?= $isDisabled ? '<span style="color:var(--rose);">(Disabled)</span>' : ''; ?></small>
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

            <button type="submit" name="update_titles" class="valas-header-btn-save">
                Save Title Changes
            </button>
        </form>
    </div>
</section>
