<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

$rows = run('SELECT SoDienThoai, TenKhachHang FROM KhachHang ORDER BY TenKhachHang')->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=khachhang.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['SoDienThoai', 'TenKhachHang']);
foreach ($rows as $r) { fputcsv($out, [$r['SoDienThoai'], $r['TenKhachHang']]); }
fclose($out);
exit;


