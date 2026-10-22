<!DOCTYPE HTML>
<html lang="en">

<head>
	<title>Dashboard</title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/dsv/app/img/favicon.png">
	<style type="text/css">
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/css/dashboard.css'; ?>
	</style>
	<style type="text/css">
		<?php
		$doc_root = $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/css/';
		include $doc_root . 'section1.css';
		include $doc_root . 'section2.css';
		include $doc_root . 'section3.css';
		include $doc_root . 'section4.css';
		include $doc_root . 'section5.css';
		include $doc_root . 'section6.css';
		?>
	</style>
	<script type="text/javascript">
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/js/toast.js'; ?>
	</script>
</head>

<body>
	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/validation.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/action/backend.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/control/preloading.php';
	?>

	<button class="sidebar-toggle-btn" onclick="toggleSidebar()">&#9776;</button>

	<div class="sidebar-wrapper" id="sidebarMenu">
		<!-- Sidebar Logo -->
		<div class="sidebar-logo">
			<span class="topbar-logo-icon">&#x26ED;</span>
			<span class="topbar-header-text">Dashboard</span>
		</div>

		<!-- Clock Badge -->
		<div id="digital-clock" class="digital-clock-badge"></div>

		<!-- Sidebar Navigation -->
		<nav class="sidebar-nav">
			<button class="nav-item-btn active" onclick="switchMenu(1)">
				<span class="nav-icon">&#x1F516;</span>Update Titles
			</button>
			<button class="nav-item-btn" onclick="switchMenu(2)">
				<span class="nav-icon">&#36;</span>Update Valas
			</button>
			<button class="nav-item-btn" onclick="switchMenu(3)">
				<span class="nav-icon">&#127974;</span>Update Deposito
			</button>
			<button class="nav-item-btn" onclick="switchMenu(4)">
				<span class="nav-icon">&#x1F3A8;</span>Themes
			</button>
			<button class="nav-item-btn" onclick="switchMenu(5)">
				<span class="nav-icon">&#x1F4FC;</span>Videos
			</button>
			<button class="nav-item-btn" onclick="switchMenu(6)">
				<span class="nav-icon">&#9881;</span>Setting
			</button>
		</nav>

		<!-- Sidebar Footer Actions -->
		<div class="sidebar-footer-actions">
			<button id="theme-toggle-btn" class="icon-btn-sidebar" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
				🌙 Dark
			</button>
			<a href="show" class="valas-link-sidebar" title="Show Valas View">&#x26F6; Show Valas</a>
		</div>

		<!-- Display Current Option Badge -->
		<?php if ($currentOption): ?>
			<div class="topbar-badge-option-wrapper">
				<span class="topbar-badge-option">
					<svg width="20" height="20" fill="none" viewBox="0 0 24 24" class="topbar-badge-svg">
						<circle cx="12" cy="12" r="12" fill="#38bdf8" />
						<text x="12" y="16" text-anchor="middle" font-size="13" fill="#fff" font-family="monospace" font-weight="bold"><?php echo htmlspecialchars($currentOption); ?></text>
					</svg>
					Option: <b><?php echo htmlspecialchars($currentOption); ?> View</b>
				</span>
			</div>
		<?php endif; ?>

		<!-- Sidebar Footer License & Device Status -->
		<div class="sidebar-license-footer">
			<div class="license-status-wrapper">
				<span class="license-label">Status:</span>
				<?php if (isset($is_licensed) && $is_licensed): ?>
					<span class="badge-verified" title="License is valid and verified">
						<span class="badge-icon">✓</span> Verified
					</span>
				<?php else: ?>
					<span class="badge-unverified" title="Device is not activated">
						⚠ Unactivated
					</span>
				<?php endif; ?>
			</div>
			<div class="device-id-wrapper">
				<span class="device-label">Device ID:</span>
				<span class="device-id-code" id="sidebarDeviceId" title="<?php echo htmlspecialchars($device_id ?? 'Not Detected'); ?>">
					<?php echo htmlspecialchars($device_id ? substr($device_id, 0, 13) . '...' : 'NONE'); ?>
				</span>
			</div>
		</div>
	</div>

	<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

	<div class="main-content-wrapper">

		<!-- Valas Form View (Update Titles) -->
		<section id="menu-section-1" class="menu-section active">
			<div class="valas-header-container" style="display: flex; flex-direction: column; gap: 20px;">

				<form method="post" class="valas-header-form">

					<div class="valas-header-card" style="border-left: 4px solid #2563eb; background: #ffffff;">
						<div class="valas-header-card-top" style="background: #eff6ff; padding: 12px 16px; border-bottom: 1px solid #dbeafe;">
							<h5 style="margin: 0; color: #1e40af; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
								<span>&#128178;</span> Valas Title (Permanen Display)
							</h5>
						</div>
						<div class="valas-header-card-body" style="padding: 16px;">
							<div class="valas-header-input-group" style="margin-bottom: 0;">
								<small style="color: #475569; font-weight: 600;">(Valas Heading Tables):</small>
								<input type="text" name="title_1" value="<?php echo htmlspecialchars($titleRow['title_1'] ?? ''); ?>" class="valas-header-input-text" placeholder="Contoh: Informas Kurs Valuta Asing" required>
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
									<?php $isDisabled = ($k > ($total_dynamic_titles + 1)); ?>
									<div class="valas-header-input-group" style="margin-bottom: 0; <?= $isDisabled ? 'opacity: 0.4;' : ''; ?>">
										<small style="font-weight: 600;">Title <?= $k; ?>: <?= $isDisabled ? '<span style="color:#ef4444;">(Disabled)</span>' : ''; ?></small>
										<input type="text" name="title_<?= $k; ?>"
											value="<?php echo htmlspecialchars($titleRow['title_' . $k] ?? ''); ?>"
											class="valas-header-input-text"
											<?= $isDisabled ? 'readonly' : ''; ?>
											placeholder="<?= $isDisabled ? 'Disabled in current Option View' : 'Enter Title ' . $k; ?>">
									</div>
								<?php endfor; ?>

							</div>
						</div>
					</div>

					<button type="submit" name="update_titles" class="valas-header-btn-save" style="margin-top: 15px; width: 100%;">
						Save Change
					</button>

				</form>

			</div>
		</section>

		<!-- Valas Form View (Update Valas) -->
		<section id="menu-section-2" class="menu-section">
			<dialog id="editExchangeModal" style="border: none; border-radius: 12px; padding: 28px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); width: 450px; max-width: 90%;">
				<h3 style="margin-top: 0; margin-bottom: 20px; color: #1e293b; font-family: sans-serif;">Edit Exchange Rate</h3>

				<div style="display: flex; flex-direction: column; gap: 16px; font-family: sans-serif;">
					<input type="hidden" id="edit_id">

					<div class="form-group">
						<label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Currency Code &amp; Flag:</label>
						<select id="edit_currency_code" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
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
						<input type="text" id="edit_currency_name" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
					</div>

					<div class="form-group" style="display: flex; flex-direction: column;">
						<label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Buying Rate:</label>
						<input type="number" id="edit_buy_rate" step="any" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
					</div>

					<div class="form-group" style="display: flex; flex-direction: column;">
						<label style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Selling Rate:</label>
						<input type="number" id="edit_sell_rate" step="any" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px;">
					</div>

					<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
						<button onclick="document.getElementById('editExchangeModal').close();" style="padding: 10px 18px; background-color: #f1f5f9; color: #475569; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
						<button onclick="submitEdit();" style="padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Update Data</button>
					</div>
				</div>
			</dialog>
			<div class="currency-container">
				<div style="margin-bottom: 20px;">
					<h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Management Forex</h2>
				</div>
				<div class="form-section">

					<h3 class="section-title">Add New Currency Rates</h3>
					<form action="" method="POST" class="currency-form">
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
									<option value="AN|an">AN - Netherlands Antilles (AN)</option>
									<option value="AOA|ao">AOA - Angolan Kwanza (AO)</option>
									<option value="ARS|ar">ARS - Argentine Peso (AR)</option>
									<option value="AWG|aw">AWG - Aruban Florin (AW)</option>
									<option value="AZN|az">AZN - Azerbaijani Manat (AZ)</option>
									<option value="BAM|ba">BAM - Bosnia-Herzegovina Convertible Mark (BA)</option>
									<option value="BBD|bb">BBD - Barbadian Dollar (BB)</option>
									<option value="BDT|bd">BDT - Bangladeshi Taka (BD)</option>
									<option value="BGN|bg">BGN - Bulgarian Lev (BG)</option>
									<option value="BHD|bh">BHD - Bahraini Dinar (BH)</option>
									<option value="BIF|bi">BIF - Burundian Franc (BI)</option>
									<option value="BMD|bm">BMD - Bermudan Dollar (BM)</option>
									<option value="BND|bn">BND - Brunei Dollar (BN)</option>
									<option value="BOB|bo">BOB - Bolivian Boliviano (BO)</option>
									<option value="BRL|br">BRL - Brazilian Real (BR)</option>
									<option value="BSD|bs">BSD - Bahamian Dollar (BS)</option>
									<option value="BTN|bt">BTN - Bhutanese Ngultrum (BT)</option>
									<option value="BWP|bw">BWP - Botswanan Pula (BW)</option>
									<option value="BYN|by">BYN - Belarusian Ruble (BY)</option>
									<option value="BZD|bz">BZD - Belize Dollar (BZ)</option>
									<option value="CAD|ca">CAD - Canadian Dollar (CA)</option>
									<option value="CDF|cd">CDF - Congolese Franc (CD)</option>
									<option value="CHF|ch">CHF - Swiss Franc (CH)</option>
									<option value="CLP|cl">CLP - Chilean Peso (CL)</option>
									<option value="COP|co">COP - Colombian Peso (CO)</option>
									<option value="CRC|cr">CRC - Costa Rican Colón (CR)</option>
									<option value="CUP|cu">CUP - Cuban Peso (CU)</option>
									<option value="CVE|cv">CVE - Cape Verdean Escudo (CV)</option>
									<option value="CZK|cz">CZK - Czech Koruna (CZ)</option>
									<option value="DJF|dj">DJF - Djiboutian Franc (DJ)</option>
									<option value="DKK|dk">DKK - Danish Krone (DK)</option>
									<option value="DOP|do">DOP - Dominican Peso (DO)</option>
									<option value="DZD|dz">DZD - Algerian Dinar (DZ)</option>
									<option value="EGP|eg">EGP - Egyptian Pound (EG)</option>
									<option value="ERN|er">ERN - Eritrean Nakfa (ER)</option>
									<option value="ETB|et">ETB - Ethiopian Birr (ET)</option>
									<option value="FJD|fj">FJD - Fijian Dollar (FJ)</option>
									<option value="FKP|fk">FKP - Falkland Islands Pound (FK)</option>
									<option value="FOK|fo">FOK - Faroese Króna (FO)</option>
									<option value="GEL|ge">GEL - Georgian Lari (GE)</option>
									<option value="GGP|gg">GGP - Guernsey Pound (GG)</option>
									<option value="GHS|gh">GHS - Ghanaian Cedi (GH)</option>
									<option value="GIP|gi">GIP - Gibraltar Pound (GI)</option>
									<option value="GMD|gm">GMD - Gambian Dalasi (GM)</option>
									<option value="GNF|gn">GNF - Guinean Franc (GN)</option>
									<option value="GTQ|gt">GTQ - Guatemalan Quetzal (GT)</option>
									<option value="GYD|gy">GYD - Guyanaese Dollar (GY)</option>
									<option value="HNL|hn">HNL - Honduran Lempira (HN)</option>
									<option value="HRK|hr">HRK - Croatian Kuna (HR)</option>
									<option value="HTG|ht">HTG - Haitian Gourde (HT)</option>
									<option value="HUF|hu">HUF - Hungarian Forint (HU)</option>
									<option value="ILS|il">ILS - Israeli New Shekel (IL)</option>
									<option value="IMP|im">IMP - Isle of Man Pound (IM)</option>
									<option value="INR|in">INR - Indian Rupee (IN)</option>
									<option value="IQD|iq">IQD - Iraqi Dinar (IQ)</option>
									<option value="IRR|ir">IRR - Iranian Rial (IR)</option>
									<option value="ISK|is">ISK - Icelandic Króna (IS)</option>
									<option value="JE|je">JE - Jersey (JE)</option>
									<option value="JMD|jm">JMD - Jamaican Dollar (JM)</option>
									<option value="JOD|jo">JOD - Jordanian Dinar (JO)</option>
									<option value="KES|ke">KES - Kenyan Shilling (KE)</option>
									<option value="KGS|kg">KGS - Kyrgyzstani Som (KG)</option>
									<option value="KHR|kh">KHR - Cambodian Riel (KH)</option>
									<option value="KID|ki">KID - Kiribati Dollar (KI)</option>
									<option value="KMF|km">KMF - Comorian Franc (KM)</option>
									<option value="KPW|kp">KPW - North Korean Won (KP)</option>
									<option value="KRW|kr">KRW - South Korean Won (KR)</option>
									<option value="KWD|kw">KWD - Kuwaiti Dinar (KW)</option>
									<option value="KYD|ky">KYD - Cayman Islands Dollar (KY)</option>
									<option value="KZT|kz">KZT - Kazakhstani Tenge (KZ)</option>
									<option value="LAK|la">LAK - Laotian Kip (LA)</option>
									<option value="LBP|lb">LBP - Lebanese Pound (LB)</option>
									<option value="LKR|lk">LKR - Sri Lankan Rupee (LK)</option>
									<option value="LRD|lr">LRD - Liberian Dollar (LR)</option>
									<option value="LSL|ls">LSL - Lesotho Loti (LS)</option>
									<option value="LYD|ly">LYD - Libyan Dinar (LY)</option>
									<option value="MAD|ma">MAD - Moroccan Dirham (MA)</option>
									<option value="MDL|md">MDL - Moldovan Leu (MD)</option>
									<option value="MGA|mg">MGA - Malagasy Ariary (MG)</option>
									<option value="MKD|mk">MKD - Macedonian Denar (MK)</option>
									<option value="MMK|mm">MMK - Myanmar Kyat (MM)</option>
									<option value="MNT|mn">MNT - Mongolian Tugrik (MN)</option>
									<option value="MOP|mo">MOP - Macanese Pataca (MO)</option>
									<option value="MRU|mr">MRU - Mauritanian Ouguiya (MR)</option>
									<option value="MUR|mu">MUR - Mauritian Rupee (MU)</option>
									<option value="MVR|mv">MVR - Maldivian Rufiyaa (MV)</option>
									<option value="MWK|mw">MWK - Malawian Kwacha (MW)</option>
									<option value="MXN|mx">MXN - Mexican Peso (MX)</option>
									<option value="MZN|mz">MZN - Mozambican Metical (MZ)</option>
									<option value="NA|na">NA - Namibian Dollar (NA)</option>
									<option value="NGN|ng">NGN - Nigerian Naira (NG)</option>
									<option value="NIO|ni">NIO - Nicaraguan Córdoba (NI)</option>
									<option value="NOK|no">NOK - Norwegian Krone (NO)</option>
									<option value="NPR|np">NPR - Nepalese Rupee (NP)</option>
									<option value="NZD|nz">NZD - New Zealand Dollar (NZ)</option>
									<option value="OMR|om">OMR - Omani Rial (OM)</option>
									<option value="PAB|pa">PAB - Panamanian Balboa (PA)</option>
									<option value="PEN|pe">PEN - Peruvian Sol (PE)</option>
									<option value="PGK|pg">PGK - Papua New Guinean Kina (PG)</option>
									<option value="PHP|ph">PHP - Philippine Peso (PH)</option>
									<option value="PKR|pk">PKR - Pakistani Rupee (PK)</option>
									<option value="PLN|pl">PLN - Polish Zloty (PL)</option>
									<option value="PYG|py">PYG - Paraguayan Guarani (PY)</option>
									<option value="QAR|qa">QAR - Qatari Rial (QA)</option>
									<option value="RON|ro">RON - Romanian Leu (RO)</option>
									<option value="RSD|rs">RSD - Serbian Dinar (RS)</option>
									<option value="RUB|ru">RUB - Russian Ruble (RU)</option>
									<option value="RWF|rw">RWF - Rwandan Franc (RW)</option>
									<option value="SBD|sb">SBD - Solomon Islands Dollar (SB)</option>
									<option value="SCR|sc">SCR - Seychellois Rupee (SC)</option>
									<option value="SDG|sd">SDG - Sudanese Pound (SD)</option>
									<option value="SEK|se">SEK - Swedish Krona (SE)</option>
									<option value="SHP|sh">SHP - St. Helena Pound (SH)</option>
									<option value="SL|sl">SL - Sierra Leone (SL)</option>
									<option value="SOS|so">SOS - Somali Shilling (SO)</option>
									<option value="SRD|sr">SRD - Surinamese Dollar (SR)</option>
									<option value="SSP|ss">SSP - South Sudanese Pound (SS)</option>
									<option value="STN|st">STN - São Tomé &amp; Príncipe Dobra (ST)</option>
									<option value="SYP|sy">SYP - Syrian Pound (SY)</option>
									<option value="SZL|sz">SZL - Swazi Lilangeni (SZ)</option>
									<option value="THB|th">THB - Thai Baht (TH)</option>
									<option value="TJS|tj">TJS - Tajikistani Somoni (TJ)</option>
									<option value="TMT|tm">TMT - Turkmenistani Manat (TM)</option>
									<option value="TND|tn">TND - Tunisian Dinar (TN)</option>
									<option value="TOP|to">TOP - Tongan Pa&#x27;anga (TO)</option>
									<option value="TRY|tr">TRY - Turkish Lira (TR)</option>
									<option value="TTD|tt">TTD - Trinidad &amp; Tobago Dollar (TT)</option>
									<option value="TVD|tv">TVD - Tuvaluan Dollar (TV)</option>
									<option value="TWD|tw">TWD - New Taiwan Dollar (TW)</option>
									<option value="TZS|tz">TZS - Tanzanian Shilling (TZ)</option>
									<option value="UAH|ua">UAH - Ukrainian Hryvnia (UA)</option>
									<option value="UGX|ug">UGX - Ugandan Shilling (UG)</option>
									<option value="UYU|uy">UYU - Uruguayan Peso (UY)</option>
									<option value="UZS|uz">UZS - Uzbekistani Som (UZ)</option>
									<option value="VES|ve">VES - Venezuelan Bolívar Soberano (VE)</option>
									<option value="VND|vn">VND - Vietnamese Dong (VN)</option>
									<option value="VU|vu">VU - Vanuatu Vatu (VU)</option>
									<option value="WST|ws">WST - Samoan Tala (WS)</option>
									<option value="ZA|za">ZA - South African Rand (ZA)</option>
									<option value="ZMW|zm">ZMW - Zambian Kwacha (ZM)</option>
									<option value="ZWL|zw">ZWL - Zimbabwean Dollar (ZW)</option>
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
								<?php
								$tampil = mysqli_query($bridge, "SELECT * FROM valas");
								if ($tampil && mysqli_num_rows($tampil) > 0) {
									while ($row_valas = mysqli_fetch_array($tampil)) {
								?>
										<tr>
											<td>
												<?php if (!empty($row_valas['flag_image'])): ?>
													<img src="/dsv/app/img/flags/svg/<?php echo htmlspecialchars($row_valas['flag_image']); ?>.svg"
														alt="<?php echo htmlspecialchars($row_valas['currency_code']); ?> Flag"
														style="width: 30px; height: auto; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); vertical-align: middle;">
												<?php else: ?>
													<span style="color: #94a3b8; font-size: 12px; font-style: italic;">No Flag</span>
												<?php endif; ?>
											</td>
											<td><?php echo htmlspecialchars($row_valas['currency_code']); ?></td>
											<td><?php echo htmlspecialchars($row_valas['currency_name']); ?></td>
											<td><?php echo htmlspecialchars($row_valas['buy_rate']); ?></td>
											<td><?php echo htmlspecialchars($row_valas['sell_rate']); ?></td>
											<td>
												<div class="action-buttons">
													<a href="#" class="btn-action-edit"
														onclick="editExchange(
													'<?php echo $row_valas['id']; ?>', 
													'<?php echo addslashes($row_valas['currency_code']); ?>', 
													'<?php echo addslashes($row_valas['currency_name']); ?>', 
													'<?php echo addslashes($row_valas['buy_rate']); ?>', 
													'<?php echo addslashes($row_valas['sell_rate']); ?>',
													'<?php echo addslashes($row_valas['flag_image']); ?>'
												); return false;">
														Edit
													</a>

													<a href="?action=delete&id=<?php echo $row_valas['id']; ?>" class="btn-action-delete"
														onclick="return confirm('Are you sure you want to delete the exchange rate for <?php echo $row_valas['currency_code']; ?>?');">
														Delete
													</a>
												</div>
											</td>
										</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='6' style='text-align:center;'>No exchange rate data available.</td></tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<!-- Deposit Management Section -->
		<section id="menu-section-3" class="menu-section">
			<div class="currency-container">

				<?php
				$activeDepositsCount = min($currentOptionInt, 8);
				?>

				<div style="margin-bottom: 20px;">
					<h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Deposit Management</h2>
					<small style="color: #64748b;">Display <?= $activeDepositsCount; ?> Active Deposits Table (As per Option View: <?= $currentOptionInt; ?>)</small>
				</div>

				<!-- MODAL & SCRIPT EDIT DEPOSITO (Diletakkan di luar loop agar ID tidak terduplikasi) -->
				<dialog id="editDepositModal" style="border: none; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); width: 420px; max-width: 90%;">
					<h4 style="margin-top: 0; margin-bottom: 16px; color: #1e293b;">Edit Data Deposito</h4>
					<form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px;">
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
					$cardTitle = !empty($titleRow['title_' . $titleIndex]) ? $titleRow['title_' . $titleIndex] : "Deposits " . $d;
					?>

					<div class="deposit-card-wrapper" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

						<div style="border-bottom: 2px solid #3b82f6; padding-bottom: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
							<h4 style="margin: 0; color: #1e3a8a; font-size: 1rem;">
								<span style="background: #3b82f6; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 8px;">Table <?= $d; ?></span>
								<?= htmlspecialchars($cardTitle); ?>
							</h4>
							<small style="color: #94a3b8; font-family: monospace;">[db: deposit_<?= $d; ?> | title_<?= $titleIndex; ?>]</small>
						</div>

						<div class="form-section" style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
							<h5 style="margin-top: 0; margin-bottom: 10px; color: #334155; font-size: 0.85rem;">+ Add Data <?= htmlspecialchars($cardTitle); ?></h5>
							<form action="" method="POST" class="currency-form">
								<input type="hidden" name="deposit_index" value="<?= $d; ?>">

								<div class="form-group" style="margin-bottom: 8px;">
									<label style="font-size: 12px;">Tenor / Term:</label>
									<input type="text" name="tenor" placeholder="Example: 1 Month / 3 Months" required class="forex-header-input-text">
								</div>

								<div class="form-group" style="margin-bottom: 8px;">
									<label style="font-size: 12px;">Interest Rate (% p.a):</label>
									<input type="number" step="any" name="interest_rate" placeholder="0.00" required class="forex-header-input-text">
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
													<td><strong><?= htmlspecialchars($depRow['tenor']); ?></strong></td>
													<td><?= htmlspecialchars($depRow['interest_rate']); ?> %</td>
													<td>Rp <?= number_format($depRow['min_amount'], 0, ',', '.'); ?></td>
													<td><?= htmlspecialchars($depRow['description'] ?? '-'); ?></td>
													<td style="text-align: center;">
														<div class="action-buttons" style="justify-content: center; gap: 6px;">
															<a href="#" class="btn-action-edit"
																onclick="openEditDepositModal(
                                           '<?= $d; ?>', 
                                           '<?= $depRow['id']; ?>', 
                                           '<?= addslashes($depRow['tenor']); ?>', 
                                           '<?= addslashes($depRow['interest_rate']); ?>', 
                                           '<?= addslashes($depRow['min_amount']); ?>', 
                                           '<?= addslashes($depRow['description'] ?? ''); ?>'
                                       ); return false;">
																Edit
															</a>

															<a href="?action=delete_deposit&dep_index=<?= $d; ?>&id=<?= $depRow['id']; ?>" class="btn-action-delete"
																onclick="return confirm('Are you sure you want to delete tenor data <?= htmlspecialchars($depRow['tenor']); ?>?');">
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

		<!-- Themes Configuration Section -->
		<section id="menu-section-4" class="menu-section">
			<div class="custom-card">
				<div class="custom-card-header">
					<h5>Theme & Template Settings</h5>
				</div>
				<div class="custom-card-body">
					<form method="post" action="">
						<div class="identity-row theme-setting-row">

							<!-- Left Column: Radio Options -->
							<div class="identity-col-left theme-col-left">
								<h6 class="identity-section-title" style="display: grid; justify-content: center; font-size: 0.8rem;">Select Active Theme</h6>

								<?php
								$currentTheme = $rowSettings['themes'] ?? $row['themes'] ?? 'show-0.css';
								?>

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

								<!-- Tombol Simpan Modern dengan Ikon -->
								<div class="theme-action-wrapper">
									<button type="submit" name="save_theme_setting" class="theme-btn-save">
										<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
											<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
										</svg>
										Save Theme Setting
									</button>
								</div>
							</div>

							<!-- Right Column: Vertical / Portrait Live Preview -->
							<div class="identity-col-right theme-col-right">
								<h6 class="identity-section-title" style="display: grid; justify-content: center; font-size: 0.8rem;">Signage Live Preview (Portrait)</h6>
								<div class="theme-preview-container">
									<iframe id="themePreviewFrame"
										class="theme-preview-iframe"
										src="show.php?preview_theme=<?= htmlspecialchars($currentTheme); ?>">
									</iframe>
								</div>
							</div>

						</div>
					</form>
				</div>
			</div>
		</section>

		<!-- Videos Management Section -->
		<section id="menu-section-5" class="menu-section">
			<div class="currency-container">
				<div style="margin-bottom: 20px;">
					<h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Management Videos</h2>
				</div>
				<div class="form-section">
					<h3 class="section-title">Upload New Signage Video</h3>
					<form action="" method="POST" enctype="multipart/form-data" class="currency-form">

						<div class="form-group" style="width: 100%; display: flex; flex-direction: column;">
							<label for="videoFile" style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Choose Video File:</label>
							<input type="file" id="videoFile" name="video_file" accept="video/mp4" required style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px; background: #fff;">
							<small style="color: #64748b; margin-top: 4px;">Supported Format: .mp4 only (Max Size: 50MB)</small>
						</div>

						<button type="submit" name="submit_video" class="btn-submit" style="margin-top: 15px;">Upload Video</button>
					</form>
				</div>

				<hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

				<div class="table-section">
					<h3 class="section-title">Signage Video Playlists</h3>
					<div class="table-responsive">
						<table class="currency-table">
							<thead>
								<tr>
									<th style="width: 80px;">ID</th>
									<th>Video Preview / File Name</th>
									<th>Uploaded Date</th>
									<th style="width: 120px; text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$tampilVideo = mysqli_query($bridge, "SELECT * FROM videos ORDER BY id DESC");
								if ($tampilVideo && mysqli_num_rows($tampilVideo) > 0) {
									$no = 1;
									while ($row_video = mysqli_fetch_array($tampilVideo)) {
								?>
										<tr>
											<td><?php echo $no++; ?></td>
											<td>
												<div style="display: flex; align-items: center; gap: 15px;">
													<video width="120" preload="metadata" class="preview-video-trigger" style="border-radius: 6px; background: #000; box-shadow: 0 1px 3px rgba(0,0,0,0.2); cursor: pointer;" title="Klik untuk putar/jeda dengan suara">
														<source src="/dsv/app/videos/<?php echo htmlspecialchars($row_video['file_name']); ?>" type="video/mp4">
													</video>
													<span style="font-family: monospace; font-weight: bold; color: #334155;">
														<?php echo htmlspecialchars($row_video['file_name']); ?>
													</span>
												</div>
											</td>
											<td>
												<?php
												echo (!empty($row_video['create_at'])) ? date('d M Y', strtotime($row_video['create_at'])) : '-';
												?>
											</td>
											<td style="text-align: center;">
												<div class="action-buttons" style="justify-content: center;">
													<a href="?action=delete_video&id=<?php echo $row_video['id']; ?>" class="btn-action-delete"
														onclick="return confirm('Are you sure you want to delete the video <?php echo $row_video['file_name']; ?>? This action cannot be undone.');">
														Delete
													</a>
												</div>
											</td>
										</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #64748b;'>No signage video available. Please upload one.</td></tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<!-- Settings Section -->
		<section id="menu-section-6" class="menu-section">
			<div class="custom-card">
				<div class="custom-card-header">
					<h5>Settings</h5>
				</div>
				<div class="custom-card-body">
					<form method="post" enctype="multipart/form-data">
						<div class="identity-row">
							<div class="identity-col-left">
								<h6 class="identity-section-title">Identity Logo</h6>

								<div class="identity-logo-group">
									<label class="identity-field-label">Logo Preview</label>

									<div class="identity-preview-wrapper">
										<?php if ($logoUrl): ?>
											<div class="identity-image-container">
												<img id="logo-preview" src="/dsv/app/img/logo/<?php echo $logoUrl; ?>" alt="Logo" class="identity-logo-preview">
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
									<input type="text" name="title" placeholder="Enter title" required value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>" class="identity-input-text">
								</div>

								<div class="identity-input-group">
									<label class="identity-field-label">Name</label>
									<input type="text" name="name" placeholder="Enter name" required value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" class="identity-input-text">
								</div>

								<div class="identity-input-group">
									<label class="identity-field-label">Running Text</label>
									<input type="text" name="marquee" placeholder="Enter running text" required value="<?php echo htmlspecialchars($row['marquee'] ?? ''); ?>" class="identity-input-text">
								</div>

								<div class="identity-action-group">
									<button type="submit" name="change_identity_btn" class="identity-btn-submit">Save Identity Changes</button>
								</div>
							</div>
						</div>
					</form>
					<hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">
					<form action="" method="POST" class="inner-modal-form">
						<div class="identity-row">
							<div class="identity-col-left">
								<h6 class="identity-section-title">Display Layout Option</h6>
								<div class="form-group" style="width: 100%; margin-top: 15px;">
									<label false="identity_option_main" class="inner-modal-label-block" style="margin-bottom: 8px; text-align: left; display: block; font-size: 0.85rem; color: #475569; font-weight: 600;">Select View Option (1-8)</label>
									<select name="identity_option" id="identity_option_main" class="identity-input-text" style="width: 100%;">
										<?php for ($i = 1; $i <= 8; $i++): ?>
											<option value="<?= $i; ?>" <?= ((isset($currentOption) ? $currentOption : 1) == $i) ? 'selected' : ''; ?>>View <?= $i; ?></option>
										<?php endfor; ?>
									</select>
								</div>
								<div class="identity-action-group">
									<button type="submit" name="set_identity_option" class="identity-btn-submit" style="margin-top: 15px;">
										Save Option
									</button>
								</div>
							</div>
							<div class="identity-col-right">
							</div>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>

	<?php renderModals(); ?>
	<div id="toast-container"></div>

	<script src="app/js/sidebar.js"></script>
	<script src="app/js/clock.js"></script>
	<script src="app/js/scrolling.js"></script>
	<script src="app/js/preview.js"></script>
	<script src="app/js/valas.js"></script>
	<script src="app/js/darkmode.js"></script>
	<script src="app/js/videos.js"></script>
	<script src="app/js/themes.js"></script>
</body>

</html>