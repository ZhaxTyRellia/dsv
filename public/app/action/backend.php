<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/bridge.php';

$row = [];
$titleRow = [];
$currentOption = '';
$logoUrl = '';
$modalOutput = "";

$identity = mysqli_query($bridge, "SELECT * FROM identity LIMIT 1");
if ($identity && mysqli_num_rows($identity) > 0) {
    $row = mysqli_fetch_assoc($identity);
    $currentOption = $row['options'] ?? '';
    $logoPath = $row['logo'] ?? '';
    $logoUrl = $logoPath ? htmlspecialchars($logoPath) : '';
    $title = $row['title'] ?? '';
    $name = $row['name'] ?? '';
    $marquee = $row['marquee'] ?? '';
}

// $total_titles = (int)$currentOption > 0 ? (int)$currentOption : 9;
$currentOptionInt = (int)$currentOption > 0 ? (int)$currentOption : 9;

$total_dynamic_titles = $currentOptionInt;

$titleQuery = mysqli_query($bridge, "SELECT title_1, title_2, title_3, title_4, title_5, title_6, title_7, title_8, title_9 FROM titles ORDER BY id_information DESC LIMIT 1");
if ($titleQuery && mysqli_num_rows($titleQuery) > 0) {
    $titleRow = mysqli_fetch_assoc($titleQuery);
}

$depositsData = [];
for ($i = 1; $i <= 8; $i++) {
    $tableName = "deposit_" . $i;
    $depositsData[$i] = [];
    $qDep = mysqli_query($bridge, "SELECT * FROM `{$tableName}` ORDER BY id ASC");
    if ($qDep) {
        while ($dRow = mysqli_fetch_assoc($qDep)) {
            $depositsData[$i][] = $dRow;
        }
    }
}

include __DIR__ . '/identity.php';
include __DIR__ . '/view.php';
include __DIR__ . '/title.php';
include __DIR__ . '/valas.php';
include __DIR__ . '/deposit.php';
include __DIR__ . '/videos.php';
include __DIR__ . '/theme.php';

if (!function_exists('renderModals')) {
    function renderModals()
    {
        global $modalOutput;
        echo $modalOutput;
    }
}
