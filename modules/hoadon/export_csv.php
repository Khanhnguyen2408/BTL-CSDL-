<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

$rows = run('SELECT MaHoaDon, NgayMua, LoaiHoaDon, ThanhTien FROM HoaDon ORDER BY MaHoaDon DESC')->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=hoadon.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['MaHoaDon','NgayMua','LoaiHoaDon','ThanhTien']);
foreach ($rows as $r) { fputcsv($out, [$r['MaHoaDon'],$r['NgayMua'],$r['LoaiHoaDon'],$r['ThanhTien']]); }
fclose($out);
exit;


