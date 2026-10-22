<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/bridge.php';

$response = [
    'logoUrl' => '',
    'title'   => '',
    'name'    => '',
    'marquee' => ''
];

$identity = mysqli_query($bridge, "SELECT * FROM identity LIMIT 1");
if ($identity && mysqli_num_rows($identity) > 0) {
    $row = mysqli_fetch_assoc($identity);
    $response['logoUrl'] = $row['logo'] ? htmlspecialchars($row['logo']) : '';
    $response['title']   = $row['title'] ?? '';
    $response['name']    = $row['name'] ?? '';
    $response['marquee']    = $row['marquee'] ?? '';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
