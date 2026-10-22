<!-- Valas Form View (Update Valas) -->
<section id="menu-section-2" class="menu-section <?= ($activeTab === 2) ? 'active' : ''; ?>">
    <dialog id="editExchangeModal" style="border: none; border-radius: 12px; padding: 28px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); width: 450px; max-width: 90%;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #1e293b; font-family: sans-serif;">Edit Exchange Rate</h3>

        <form action="/dsv/valas/edit" method="POST" id="editValasForm" style="display: flex; flex-direction: column; gap: 16px; font-family: sans-serif;">
            <input type="hidden" id="edit_id" name="id">

            <div class="form-group">
                <label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Currency Code &amp; Flag:</label>
                <select id="edit_currency_code" name="currency_data" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px; width: 100%;">
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

            <div class="form-group" style="display: flex; flex-direction: column;">
                <label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Currency Name:</label>
                <input type="text" id="edit_currency_name" name="currency_name" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column;">
                <label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Buying Rate:</label>
                <input type="number" id="edit_buy_rate" name="buy_rate" step="any" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column;">
                <label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Selling Rate:</label>
                <input type="number" id="edit_sell_rate" name="sell_rate" step="any" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
                <button type="button" onclick="document.getElementById('editExchangeModal').close();" style="padding: 10px 18px; background-color: #f1f5f9; color: #475569; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" style="padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Update Data</button>
            </div>
        </form>
    </dialog>

    <div class="currency-container">
        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Management Forex</h2>
        </div>
        <div class="form-section">
            <h3 class="section-title">Add New Currency Rates</h3>
            <form action="/dsv/valas" method="POST" class="currency-form">
                <div class="form-group fg-code">
                    <label for="currencyCodeAndFlag">Currency &amp; Flag:</label>
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
                    <label for="currencyName">Currency:</label>
                    <input type="text" id="currencyName" name="currency_name" placeholder="Example: US Dollar" required>
                </div>

                <div class="form-group fg-rate">
                    <label for="sellingRate">Selling Rate:</label>
                    <input type="number" id="sellingRate" name="sell_rate" step="any" placeholder="0" required>
                </div>

                <div class="form-group fg-rate">
                    <label for="buyingRate">Buying Rate:</label>
                    <input type="number" id="buyingRate" name="buy_rate" step="any" placeholder="0" required>
                </div>

                <button type="submit" name="submit_kurs" class="btn-submit">Save</button>
            </form>
        </div>

        <hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

        <div class="table-section">
            <h3 class="section-title">Currency Exchange Rate List</h3>
            <div class="table-responsive">
                <table class="currency-table">
                    <thead>
                        <tr>
                            <th>Flags</th>
                            <th>Currency Code</th>
                            <th>Currency Name</th>
                            <th>Buy Rate</th>
                            <th>Sell Rate</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($valasList)): ?>
                            <?php foreach ($valasList as $row_valas): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row_valas['flag_image'])): ?>
                                            <img src="/dsv/app/img/flags/svg/<?php echo htmlspecialchars((string)$row_valas['flag_image']); ?>.svg"
                                                alt="<?php echo htmlspecialchars((string)$row_valas['currency_code']); ?> Flag"
                                                style="width: 30px; height: auto; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); vertical-align: middle;">
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 12px; font-style: italic;">No Flag</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars((string)$row_valas['currency_code']); ?></td>
                                    <td><?php echo htmlspecialchars((string)$row_valas['currency_name']); ?></td>
                                    <td>Rp <?php echo number_format((float)($row_valas['buy_rate'] ?? 0), 2, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format((float)($row_valas['sell_rate'] ?? 0), 2, ',', '.'); ?></td>
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

                                            <a href="/dsv/valas/delete/<?php echo $row_valas['id']; ?>" class="btn-action-delete"
                                                onclick="return confirm('Are you sure you want to delete the exchange rate for <?php echo htmlspecialchars((string)$row_valas['currency_code']); ?>?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">No exchange rate data available.</td></tr>
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
