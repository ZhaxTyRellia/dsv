<!-- Deposit Management Section -->
<section id="menu-section-3" class="menu-section <?= ($activeTab === 3) ? 'active' : ''; ?>">
    <div class="currency-container">

        <?php $activeDepositsCount = min($currentOptionInt, 8); ?>

        <div class="section-page-header">
            <div>
                <h2>Deposit Management</h2>
                <small>Displaying <?= $activeDepositsCount; ?> Active Deposit Table(s) (Option View: <?= $currentOptionInt; ?>)</small>
            </div>
        </div>

        <!-- MODAL EDIT DEPOSITO -->
        <dialog id="editDepositModal">
            <div class="modal-header-bar"></div>
            <div class="modal-inner-body">
                <h4 style="margin-top: 0; margin-bottom: 16px; color: var(--text-main); font-size: 1.1rem; font-weight: 800;">Edit Data Deposito</h4>
                <form method="POST" action="<?= $urlFor('deposit.edit'); ?>" style="display: flex; flex-direction: column; gap: 12px;">
                    <input type="hidden" name="deposit_index" id="edit_dep_index">
                    <input type="hidden" name="deposit_id" id="edit_dep_id">

                    <div class="form-group">
                        <label>Tenor / Jangka Waktu</label>
                        <input type="text" name="tenor" id="edit_dep_tenor" required class="valas-header-input-text">
                    </div>

                    <div class="form-group">
                        <label>Suku Bunga (% p.a)</label>
                        <input type="number" step="any" name="interest_rate" id="edit_dep_rate" required class="valas-header-input-text">
                    </div>

                    <div class="form-group">
                        <label>Minimal Penempatan (Rp)</label>
                        <input type="number" step="any" name="min_amount" id="edit_dep_min" class="valas-header-input-text">
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="description" id="edit_dep_desc" class="valas-header-input-text">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                        <button type="button" onclick="document.getElementById('editDepositModal').close();" style="padding: 9px 16px; background: var(--bg-surface-hover); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; font-weight: 700;">Cancel</button>
                        <button type="submit" name="update_deposit_item" class="btn-submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            function openEditDepositModal(depIndex, id, tenor, rate, minAmount, desc) {
                document.getElementById('edit_dep_index').value = depIndex;
                document.getElementById('edit_dep_id').value = id;
                document.getElementById('edit_dep_tenor').value = tenor;
                document.getElementById('edit_dep_rate').value = rate;
                document.getElementById('edit_dep_min').value = minAmount;
                document.getElementById('edit_dep_desc').value = desc;
                document.getElementById('editDepositModal').showModal();
            }
        </script>

        <!-- LOOPING KARTU DEPOSITO -->
        <?php for ($d = 1; $d <= $activeDepositsCount; $d++): ?>
            <?php
            $titleIndex = $d + 1;
            $cardTitle = !empty($titles['title_' . $titleIndex]) ? $titles['title_' . $titleIndex] : "Deposits " . $d;
            ?>

            <div class="deposit-card-wrapper">
                <div class="deposit-card-header">
                    <h4>
                        <span class="deposit-table-badge">Table <?= $d; ?></span>
                        <?= htmlspecialchars((string)$cardTitle); ?>
                    </h4>
                    <small class="deposit-db-tag">deposit_<?= $d; ?> &bull; title_<?= $titleIndex; ?></small>
                </div>

                <div class="deposit-inline-form-box">
                    <h5>+ Add Data <?= htmlspecialchars((string)$cardTitle); ?></h5>
                    <form action="<?= $urlFor('deposit.create'); ?>" method="POST" class="currency-form">
                        <input type="hidden" name="deposit_index" value="<?= $d; ?>">

                        <div class="form-group" style="flex: 1.3; min-width: 160px;">
                            <label>Tenor / Term</label>
                            <input type="text" name="tenor" placeholder="Example: 1 Month / 3 Months" required class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="flex: 1; min-width: 130px;">
                            <label>Interest Rate (% p.a)</label>
                            <input type="number" step="any" name="interest_rate" placeholder="0.00" required class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="flex: 1.2; min-width: 150px;">
                            <label>Min. Placement (Rp)</label>
                            <input type="number" step="any" name="min_amount" placeholder="0" class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="flex: 1.4; min-width: 160px;">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Additional notes" class="valas-header-input-text">
                        </div>

                        <button type="submit" name="submit_deposit" class="btn-submit">+ Add Data</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="currency-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Tenor</th>
                                <th>Interest Rate (% p.a)</th>
                                <th>Min. Placement</th>
                                <th>Description</th>
                                <th style="width: 140px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($depositsData[$d])): ?>
                                <?php $no = 1;
                                foreach ($depositsData[$d] as $depRow): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><strong><?= htmlspecialchars((string)$depRow['tenor']); ?></strong></td>
                                        <td><span class="deposit-rate-badge"><?= htmlspecialchars((string)$depRow['interest_rate']); ?> %</span></td>
                                        <td>Rp <?= number_format((float)($depRow['min_amount'] ?? 0), 0, ',', '.'); ?></td>
                                        <td><?= htmlspecialchars((string)($depRow['description'] ?? '-')); ?></td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons" style="justify-content: center;">
                                                <a href="#" class="btn-action-edit"
                                                    onclick="openEditDepositModal(
                                                        '<?= $d; ?>', 
                                                        '<?= $depRow['id']; ?>', 
                                                        '<?= addslashes((string)$depRow['tenor']); ?>', 
                                                        '<?= addslashes((string)$depRow['interest_rate']); ?>', 
                                                        '<?= addslashes((string)$depRow['min_amount']); ?>', 
                                                        '<?= addslashes((string)($depRow['description'] ?? '')); ?>'
                                                    ); return false;">
                                                    Edit
                                                </a>

                                                <a href="<?= $urlFor('deposit.delete', ['index' => (string)$d, 'id' => (string)$depRow['id']]); ?>" class="btn-action-delete"
                                                    onclick="return confirm('Are you sure you want to delete tenor data <?= htmlspecialchars((string)$depRow['tenor']); ?>?');">
                                                    Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 18px;">There is no deposit data in this table yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        <?php endfor; ?>

    </div>
</section>
