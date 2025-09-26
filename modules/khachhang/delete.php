<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if ($id !== '') {
        try {
            run('DELETE FROM KhachHang WHERE SoDienThoai=?', [$id]);
        } catch (Throwable $e) {
            // Có thể bị ràng buộc FK từ HoaDon
        }
    }
}
header('Location: ' . BASE_URL . '?m=khachhang');
exit;


