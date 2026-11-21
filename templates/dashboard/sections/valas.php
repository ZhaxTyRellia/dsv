<!-- Valas Form View (Update Valas) -->
<section id="menu-section-2" class="menu-section <?= ($activeTab === 2) ? 'active' : ''; ?>">
    <dialog id="editExchangeModal">
        <div class="modal-header-bar"></div>
        <div class="modal-inner-body">
            <h3 style="margin-top: 0; margin-bottom: 18px; color: var(--text-main); font-size: 1.15rem; font-weight: 800;">Edit Exchange Rate</h3>

            <form action="<?= $urlFor('valas.edit'); ?>" method="POST" id="editValasForm" style="display: flex; flex-direction: column; gap: 14px;">
                <input type="hidden" id="edit_id" name="id">

                <div class="form-group">
                    <label>Currency Code &amp; Flag</label>
                    <select id="edit_currency_code" name="currency_data" style="width: 100%;">
                        <option value="" disabled>Select Currency...</option>
                        <optgroup label="Popular">
                            <option value="IDR|id">IDR - Indonesian Rupiah (ID)</option>
                            <option value="USD|us">USD - US Dollar (US)</option>
                            <option value="EUR|eu">EUR - Euro (EU)</option>
                            <option value="GBP|gb">GBP - British Pound Sterling (GB)</option>
                            <option value="JPY|jp">JPY - Japanese Yen (JP)</option>
                            <option value="SGD|sg">SGD - Singapore Dollar (SG)</option>
                            <option value="AUD|au">AUD - Australian Dollar (AU)</option>
                            <option value="CNY|cn">CNY - Chinese Yuan (CN)</option>
                            <option value="HKD|hk">HKD - Hong Kong Dollar (HK)</option>
                            <option value="MYR|my">MYR - Malaysian Ringgit (MY)</option>
                            <option value="SAR|sa">SAR - Saudi Riyal (SA)</option>
                        </optgroup>
                        <optgroup label="All Global">
                            <option value="AED|ae">AED - United Arab Emirates Dirham (AE)</option>
                            <option value="AFN|af">AFN - Afghan Afghani (AF)</option>
                            <option value="ALL|al">ALL - Albanian Lek (AL)</option>
                            <option value="AMD|am">AMD - Armenian Dram (AM)</option>
                            <option value="ARS|ar">ARS - Argentine Peso (AR)</option>
                            <option value="AWG|aw">AWG - Aruban Florin (AW)</option>
                            <option value="AZN|az">AZN - Azerbaijani Manat (AZ)</option>
                            <option value="BND|bn">BND - Brunei Dollar (BN)</option>
                            <option value="BRL|br">BRL - Brazilian Real (BR)</option>
                            <option value="CAD|ca">CAD - Canadian Dollar (CA)</option>
                            <option value="CHF|ch">CHF - Swiss Franc (CH)</option>
                            <option value="CNY|cn">CNY - Chinese Yuan (CN)</option>
                            <option value="INR|in">INR - Indian Rupee (IN)</option>
                            <option value="KRW|kr">KRW - South Korean Won (KR)</option>
                            <option value="MYR|my">MYR - Malaysian Ringgit (MY)</option>
                            <option value="NZD|nz">NZD - New Zealand Dollar (NZ)</option>
                            <option value="PHP|ph">PHP - Philippine Peso (PH)</option>
                            <option value="THB|th">THB - Thai Baht (TH)</option>
                            <option value="TWD|tw">TWD - New Taiwan Dollar (TW)</option>
                            <option value="VND|vn">VND - Vietnamese Dong (VN)</option>
                            <option value="ZAR|za">ZAR - South African Rand (ZA)</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label>Currency Name</label>
                    <input type="text" id="edit_currency_name" name="currency_name" required>
                </div>

                <div class="form-group">
                    <label>Buying Rate</label>
                    <input type="number" id="edit_buy_rate" name="buy_rate" step="any" required>
                </div>

                <div class="form-group">
                    <label>Selling Rate</label>
                    <input type="number" id="edit_sell_rate" name="sell_rate" step="any" required>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                    <button type="button" onclick="document.getElementById('editExchangeModal').close();" style="padding: 10px 18px; background: var(--bg-surface-hover); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; font-weight: 700;">Cancel</button>
                    <button type="submit" class="btn-submit">Update Data</button>
                </div>
            </form>
        </div>
    </dialog>

    <div class="currency-container">
        <div class="section-page-header">
            <div>
                <h2>Management Forex (Valas)</h2>
                <small>Add and manage real-time foreign currency buying &amp; selling rates</small>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">+ Add New Currency Rate</h3>
            <form action="<?= $urlFor('valas.create'); ?>" method="POST" class="currency-form">
                <div class="form-group fg-code">
                    <label for="currencyCodeAndFlag">Currency &amp; Flag</label>
                    <select id="currencyCodeAndFlag" name="currency_data" required>
                        <option value="" disabled selected>Select Currency &amp; Country...</option>
                        <optgroup label="Popular Options">
                            <option value="IDR|id">IDR - Indonesian Rupiah (ID)</option>
                            <option value="USD|us">USD - US Dollar (US)</option>
                            <option value="EUR|eu">EUR - Euro (EU)</option>
                            <option value="GBP|gb">GBP - British Pound Sterling (GB)</option>
                            <option value="JPY|jp">JPY - Japanese Yen (JP)</option>
                            <option value="SGD|sg">SGD - Singapore Dollar (SG)</option>
                            <option value="AUD|au">AUD - Australian Dollar (AU)</option>
                            <option value="CNY|cn">CNY - Chinese Yuan (CN)</option>
                            <option value="HKD|hk">HKD - Hong Kong Dollar (HK)</option>
                            <option value="MYR|my">MYR - Malaysian Ringgit (MY)</option>
                            <option value="SAR|sa">SAR - Saudi Riyal (SA)</option>
                        </optgroup>
                        <optgroup label="All Global Options">
                            <option value="AED|ae">AED - United Arab Emirates Dirham (AE)</option>
                            <option value="AFN|af">AFN - Afghan Afghani (AF)</option>
                            <option value="ALL|al">ALL - Albanian Lek (AL)</option>
                            <option value="AMD|am">AMD - Armenian Dram (AM)</option>
                            <option value="ARS|ar">ARS - Argentine Peso (AR)</option>
                            <option value="BND|bn">BND - Brunei Dollar (BN)</option>
                            <option value="BRL|br">BRL - Brazilian Real (BR)</option>
                            <option value="CAD|ca">CAD - Canadian Dollar (CA)</option>
                            <option value="CHF|ch">CHF - Swiss Franc (CH)</option>
                            <option value="INR|in">INR - Indian Rupee (IN)</option>
                            <option value="KRW|kr">KRW - South Korean Won (KR)</option>
                            <option value="NZD|nz">NZD - New Zealand Dollar (NZ)</option>
                            <option value="PHP|ph">PHP - Philippine Peso (PH)</option>
                            <option value="THB|th">THB - Thai Baht (TH)</option>
                            <option value="TWD|tw">TWD - New Taiwan Dollar (TW)</option>
                            <option value="VND|vn">VND - Vietnamese Dong (VN)</option>
                            <option value="ZAR|za">ZAR - South African Rand (ZA)</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group fg-name">
                    <label for="currencyName">Currency Name</label>
                    <input type="text" id="currencyName" name="currency_name" placeholder="Example: US Dollar" required>
                </div>

                <div class="form-group fg-rate">
                    <label for="sellingRate">Selling Rate</label>
                    <input type="number" id="sellingRate" name="sell_rate" step="any" placeholder="0" required>
                </div>

                <div class="form-group fg-rate">
                    <label for="buyingRate">Buying Rate</label>
                    <input type="number" id="buyingRate" name="buy_rate" step="any" placeholder="0" required>
                </div>

                <button type="submit" name="submit_kurs" class="btn-submit">Save Rate</button>
            </form>
        </div>

        <div class="table-section">
            <h3 class="section-title">Currency Exchange Rate List</h3>
            <div class="table-responsive">
                <table class="currency-table">
                    <thead>
                        <tr>
                            <th>Flag</th>
                            <th>Code</th>
                            <th>Currency Name</th>
                            <th>Buy Rate</th>
                            <th>Sell Rate</th>
                            <th style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($valasList)): ?>
                            <?php foreach ($valasList as $row_valas): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row_valas['flag_image'])): ?>
                                            <img src="<?= $basePath; ?>/img/flags/svg/<?php echo htmlspecialchars((string)$row_valas['flag_image']); ?>.svg"
                                                alt="<?php echo htmlspecialchars((string)$row_valas['currency_code']); ?> Flag"
                                                style="width: 32px; height: auto; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.12); vertical-align: middle;">
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 12px; font-style: italic;">No Flag</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="code-pill"><?php echo htmlspecialchars((string)$row_valas['currency_code']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars((string)$row_valas['currency_name']); ?></strong></td>
                                    <td><span class="rate-buy">Rp <?php echo number_format((float)($row_valas['buy_rate'] ?? 0), 2, ',', '.'); ?></span></td>
                                    <td><span class="rate-sell">Rp <?php echo number_format((float)($row_valas['sell_rate'] ?? 0), 2, ',', '.'); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="#" class="btn-action-edit"
                                                onclick="openEditValasModal(
                                                    '<?php echo $row_valas['id']; ?>', 
                                                    '<?php echo addslashes((string)$row_valas['currency_code']); ?>', 
                                                    '<?php echo addslashes((string)$row_valas['currency_name']); ?>', 
                                                    '<?php echo (float)$row_valas['buy_rate']; ?>', 
                                                    '<?php echo (float)$row_valas['sell_rate']; ?>',
                                                    '<?php echo addslashes((string)$row_valas['flag_image']); ?>'
                                                ); return false;">
                                                Edit
                                            </a>

                                            <a href="<?= $urlFor('valas.delete', ['id' => (string)$row_valas['id']]); ?>" class="btn-action-delete"
                                                onclick="return confirm('Are you sure you want to delete the exchange rate for <?php echo htmlspecialchars((string)$row_valas['currency_code']); ?>?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; padding: 20px; color: var(--text-muted);">No exchange rate data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
function openEditValasModal(id, code, name, buy, sell, flag) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_currency_name').value = name;
    document.getElementById('edit_buy_rate').value = buy;
    document.getElementById('edit_sell_rate').value = sell;

    const selectElement = document.getElementById('edit_currency_code');
    const combinedVal = code.toUpperCase() + '|' + flag.toLowerCase();
    selectElement.value = combinedVal;

    document.getElementById('editExchangeModal').showModal();
}
</script>
