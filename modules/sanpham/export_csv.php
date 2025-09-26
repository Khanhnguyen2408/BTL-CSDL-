<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

$rows = run('SELECT MaSanPham, TenSanPham, SoLuong FROM SanPham ORDER BY TenSanPham')->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sanpham.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['MaSanPham', 'TenSanPham', 'SoLuong']);
foreach ($rows as $r) { fputcsv($out, [$r['MaSanPham'], $r['TenSanPham'], $r['SoLuong']]); }
fclose($out);
exit;


