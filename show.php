<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/bridge.php';

$queryTheme = mysqli_query($bridge, "SELECT themes FROM identity WHERE id = 1 LIMIT 1");
$rowTheme = mysqli_fetch_assoc($queryTheme);

if (isset($_GET['preview_theme'])) {
	$activeTheme = preg_replace('/[^a-zA-Z0-9_.-]/', '', $_GET['preview_theme']);
} else {
	$activeTheme = $rowTheme['themes'] ?? 'show-0.css';
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Display Signage</title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/dsv/app/img/favicon.png">
	<link id="theme-stylesheet" rel="stylesheet" type="text/css" href="/dsv/app/css/<?= htmlspecialchars($activeTheme); ?>">
</head>

<body>
	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/validation.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/action/backend.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/control/preloading.php';
	?>

	<!-- HEADER -->
	<header class="main-header">
		<div class="header-container">
			<div class="header-identity">
				<div id="logo-container">
					<?php if (!empty($logoUrl)): ?>
						<img id="header-logo-img" src="/dsv/app/img/logo/<?php echo $logoUrl; ?>" alt="Logo" class="header-logo">
					<?php endif; ?>
				</div>
				<div class="header-text">
					<h1 id="header-title-text" class="header-title"><?php echo htmlspecialchars($title ?? ''); ?></h1>
					<p id="header-name-text" class="header-name"><?php echo htmlspecialchars($name ?? ''); ?></p>
				</div>
			</div>
			<div class="header-addon">
				<span class="header-date">
					<?php include $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/control/date.php'; ?>
				</span>
			</div>
		</div>
	</header>

	<!-- CONTENT -->
	<div class="main-content-wrapper">
		<!-- Videos -->
		<div class="content-video">
			<?php
			$videoQuery = mysqli_query($bridge, "SELECT file_name FROM videos ORDER BY create_at ASC, id ASC");

			$playlist = [];
			if ($videoQuery && mysqli_num_rows($videoQuery) > 0) {
				while ($videoRow = mysqli_fetch_assoc($videoQuery)) {
					if (!empty($videoRow['file_name'])) {
						$playlist[] = '/dsv/app/videos/' . htmlspecialchars($videoRow['file_name']);
					}
				}
			}

			if (!empty($playlist)) {
				$firstVideo = $playlist[0];
			?>
				<video autoplay muted playsinline class="video-element" id="signagePlayer">
					<source src="<?php echo $firstVideo; ?>" type="video/mp4">
				</video>

				<script type="text/javascript">
					document.addEventListener('DOMContentLoaded', function() {
						const playlist = <?php echo json_encode($playlist); ?>;
						const player = document.getElementById('signagePlayer');
						let currentVideoIndex = 0;

						player.addEventListener('ended', function() {
							currentVideoIndex++;

							if (currentVideoIndex >= playlist.length) {
								currentVideoIndex = 0;
							}

							player.src = playlist[currentVideoIndex];
							player.load();
							player.play().catch(function(error) {
								console.log("Autoplay diblokir browser atau error: ", error);
							});
						});
					});
				</script>
			<?php
			} else {
				echo '<div style="color: #fff; padding: 20px;"></div>';
			}
			?>
		</div>

		<!-- Valas -->
		<div class="content-kurs">
			<div class="kurs-header-title">
				<?php echo htmlspecialchars($titleRow['title_1'] ?? ''); ?>
			</div>
			<div class="table-scroll-container" id="kursTableContainer">
				<table class="kurs-table">
					<thead>
						<tr>
							<th>Mata Uang</th>
							<th>Nilai Jual</th>
							<th>Nilai Beli</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$valasQuery = mysqli_query($bridge, "SELECT * FROM valas ORDER BY id ASC");

						if ($valasQuery && mysqli_num_rows($valasQuery) > 0) {
							while ($kurs = mysqli_fetch_assoc($valasQuery)) {
						?>
								<tr>
									<td>
										<?php if (!empty($kurs['flag_image'])): ?>
											<img src="/dsv/app/img/flags/svg/<?php echo htmlspecialchars($kurs['flag_image']); ?>.svg" alt="Flag" class="flag-img">
										<?php endif; ?>
										<span class="currency-code"><?php echo htmlspecialchars($kurs['currency_code'] ?? ''); ?></span>
										<span class="currency-full-name">| <?php echo htmlspecialchars($kurs['currency_name'] ?? ''); ?></span>
									</td>
									<td class="val-beli">
										Rp <?php echo number_format($kurs['buy_rate'] ?? 0, 2, ',', '.'); ?>
									</td>
									<td class="val-jual">
										Rp <?php echo number_format($kurs['sell_rate'] ?? 0, 2, ',', '.'); ?>
									</td>
								</tr>
						<?php
							}
						} else {
							echo '<tr><td colspan="3" style="text-align:center; padding: 20px;">Tidak ada data kurs saat ini.</td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- FOOTER -->
	<div class="marquee-sticky-wrapper">
		<div class="marquee-container">
			<marquee id="marquee-text" behavior="scroll" direction="left" scrollamount="6" class="marquee-element">
				<?php echo htmlspecialchars($row['marquee']); ?>
			</marquee>
		</div>
		<div class="clock-date">
			<span id="clock">00:00:00</span>
		</div>
	</div>

	<script src="app/js/show_clock.js"></script>
	<script src="app/js/show_identity.js"></script>
	<script src="app/js/auto_scroll.js"></script>
	<script src="app/js/themes_automatic.js"></script>

</body>

</html>