<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/bridge.php';

// Ambil nilai themes dari database
$query = mysqli_query($bridge, "SELECT themes FROM identity WHERE id = 1 LIMIT 1");
$row = mysqli_fetch_assoc($query);

$currentTheme = $row['themes'] ?? 'show-0.css';

// Kembalikan response JSON
echo json_encode([
    'status' => 'success',
    'theme'  => $currentTheme
]);
exit;
