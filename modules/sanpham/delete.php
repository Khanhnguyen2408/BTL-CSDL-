<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if ($id !== '') {
        // Không cho xóa nếu tồn tại trong ChiTietHoaDon
        $exists = run('SELECT 1 FROM ChiTietHoaDon WHERE MaSanPham=? LIMIT 1', [$id])->fetch();
        if (!$exists) {
            run('DELETE FROM SanPham WHERE MaSanPham=?', [$id]);
        }
    }
}
header('Location: ' . BASE_URL . '?m=sanpham');
exit;


