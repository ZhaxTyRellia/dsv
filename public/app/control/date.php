<?php
$hariIndo = [
    0 => 'Minggu',
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu'
];

$bulanIndo = [
    1 => 'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];

$hr = (int)date('w');

$tgl = date('j');

$bln = (int)date('n');

$thn = date('Y');

echo $hariIndo[$hr] . ', ' . $tgl . ' ' . $bulanIndo[$bln] . ' ' . $thn;
