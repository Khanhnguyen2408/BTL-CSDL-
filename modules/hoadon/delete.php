<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        transaction(function(PDO $pdo) use ($id) {
            // Hoàn kho nếu là hóa đơn Bán, giảm kho nếu là Nhập
            $h = $pdo->prepare('SELECT LoaiHoaDon FROM HoaDon WHERE MaHoaDon=?');
            $h->execute([$id]);
            $row = $h->fetch();
            if ($row) {
                $ct = $pdo->prepare('SELECT MaSanPham, SoLuong FROM ChiTietHoaDon WHERE MaHoaDon=?');
                $ct->execute([$id]);
                $items = $ct->fetchAll();
                foreach ($items as $it) {
                    if ($row['LoaiHoaDon'] === 'Ban') {
                        $pdo->prepare('UPDATE SanPham SET SoLuong = SoLuong + ? WHERE MaSanPham=?')->execute([$it['SoLuong'],$it['MaSanPham']]);
                    } else {
                        $pdo->prepare('UPDATE SanPham SET SoLuong = SoLuong - ? WHERE MaSanPham=?')->execute([$it['SoLuong'],$it['MaSanPham']]);
                    }
                }
            }
            $pdo->prepare('DELETE FROM ChiTietHoaDon WHERE MaHoaDon=?')->execute([$id]);
            $pdo->prepare('DELETE FROM HoaDon WHERE MaHoaDon=?')->execute([$id]);
        });
    }
}
header('Location: ' . BASE_URL . '?m=hoadon');
exit;


