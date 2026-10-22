<!-- Deposit Management Section -->
<section id="menu-section-3" class="menu-section <?= ($activeTab === 3) ? 'active' : ''; ?>">
    <div class="currency-container">

        <?php $activeDepositsCount = min($currentOptionInt, 8); ?>

        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Deposit Management</h2>
            <small style="color: #64748b;">Display <?= $activeDepositsCount; ?> Active Deposits Table (As per Option View: <?= $currentOptionInt; ?>)</small>
        </div>

        <!-- MODAL EDIT DEPOSITO -->
        <dialog id="editDepositModal" style="border: none; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); width: 420px; max-width: 90%;">
            <h4 style="margin-top: 0; margin-bottom: 16px; color: #1e293b;">Edit Data Deposito</h4>
            <form method="POST" action="<?= $urlFor('deposit.edit'); ?>" style="display: flex; flex-direction: column; gap: 12px;">
                <input type="hidden" name="deposit_index" id="edit_dep_index">
                <input type="hidden" name="deposit_id" id="edit_dep_id">

                <div>
                    <label style="font-size: 12px; font-weight: 600; color: #475569;">Tenor / Jangka Waktu:</label>
                    <input type="text" name="tenor" id="edit_dep_tenor" required class="valas-header-input-text" style="width: 100%;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 600; color: #475569;">Suku Bunga (% p.a):</label>
                    <input type="number" step="any" name="interest_rate" id="edit_dep_rate" required class="valas-header-input-text" style="width: 100%;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 600; color: #475569;">Minimal Penempatan (Rp):</label>
                    <input type="number" step="any" name="min_amount" id="edit_dep_min" class="valas-header-input-text" style="width: 100%;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 600; color: #475569;">Keterangan:</label>
                    <input type="text" name="description" id="edit_dep_desc" class="valas-header-input-text" style="width: 100%;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('editDepositModal').close();" style="padding: 8px 16px; background: #f1f5f9; border: none; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit" name="update_deposit_item" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Save Changes</button>
                </div>
            </form>
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

            <div class="deposit-card-wrapper" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

                <div style="border-bottom: 2px solid #3b82f6; padding-bottom: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0; color: #1e3a8a; font-size: 1rem;">
                        <span style="background: #3b82f6; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 8px;">Table <?= $d; ?></span>
                        <?= htmlspecialchars((string)$cardTitle); ?>
                    </h4>
                    <small style="color: #94a3b8; font-family: monospace;">[db: deposit_<?= $d; ?> | title_<?= $titleIndex; ?>]</small>
                </div>

                <div class="form-section" style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="margin-top: 0; margin-bottom: 10px; color: #334155; font-size: 0.85rem;">+ Add Data <?= htmlspecialchars((string)$cardTitle); ?></h5>
                    <form action="<?= $urlFor('deposit.create'); ?>" method="POST" class="currency-form">
                        <input type="hidden" name="deposit_index" value="<?= $d; ?>">

                        <div class="form-group" style="margin-bottom: 8px;">
                            <label style="font-size: 12px;">Tenor / Term:</label>
                            <input type="text" name="tenor" placeholder="Example: 1 Month / 3 Months" required class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="margin-bottom: 8px;">
                            <label style="font-size: 12px;">Interest Rate (% p.a):</label>
                            <input type="number" step="any" name="interest_rate" placeholder="0.00" required class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="margin-bottom: 8px;">
                            <label style="font-size: 12px;">Minimum Placement (Rp):</label>
                            <input type="number" step="any" name="min_amount" placeholder="0" class="valas-header-input-text">
                        </div>

                        <div class="form-group" style="margin-bottom: 8px;">
                            <label style="font-size: 12px;">Description:</label>
                            <input type="text" name="description" placeholder="Additional notes" class="valas-header-input-text">
                        </div>

                        <button type="submit" name="submit_deposit" class="btn-submit" style="height: 38px; margin-top: 5px;">Add Data</button>
                    </form>
                </div>

                <div class="table-section">
                    <div class="table-responsive">
                        <table class="currency-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tenor</th>
                                    <th>Interest Rate (% p.a)</th>
                                    <th>Min. Placement</th>
                                    <th>Description</th>
                                    <th style="width: 130px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($depositsData[$d])): ?>
                                    <?php $no = 1;
                                    foreach ($depositsData[$d] as $depRow): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><strong><?= htmlspecialchars((string)$depRow['tenor']); ?></strong></td>
                                            <td><?= htmlspecialchars((string)$depRow['interest_rate']); ?> %</td>
                                            <td>Rp <?= number_format((float)($depRow['min_amount'] ?? 0), 0, ',', '.'); ?></td>
                                            <td><?= htmlspecialchars((string)($depRow['description'] ?? '-')); ?></td>
                                            <td style="text-align: center;">
                                                <div class="action-buttons" style="justify-content: center; gap: 6px;">
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
                                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 15px;">There is no deposit data in this table yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        <?php endfor; ?>

    </div>
</section>
