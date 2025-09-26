<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$id = (int)($_GET['id'] ?? 0);
$h = run('SELECT h.*, k.TenKhachHang FROM HoaDon h LEFT JOIN KhachHang k ON k.SoDienThoai=h.SoDienThoai WHERE MaHoaDon=?', [$id])->fetch();
if (!$h) { echo '<div class="alert alert-danger">Không tìm thấy hóa đơn.</div>'; include __DIR__ . '/../../partials/footer.php'; exit; }
$ct = run('SELECT c.*, s.TenSanPham FROM ChiTietHoaDon c JOIN SanPham s ON s.MaSanPham=c.MaSanPham WHERE c.MaHoaDon=?', [$id])->fetchAll();
?>
<h1 class="h5">Hóa đơn #<?php echo $h['MaHoaDon']; ?></h1>
<div class="row g-2 mb-3">
  <div class="col">Ngày: <strong><?php echo htmlspecialchars($h['NgayMua']); ?></strong></div>
  <div class="col">Loại: <strong><?php echo htmlspecialchars($h['LoaiHoaDon']); ?></strong></div>
  <div class="col">Khách hàng: <strong><?php echo htmlspecialchars($h['TenKhachHang'] ?? ''); ?></strong></div>
  <div class="col">Tổng: <strong><?php echo number_format((float)$h['ThanhTien'],2); ?></strong></div>
  <div class="col text-end"><a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=hoadon">Quay lại</a></div>
  </div>

<div class="table-responsive">
<table class="table table-bordered table-sm">
  <thead class="table-light"><tr><th>Mã</th><th>Tên</th><th>Số lượng</th><th>Đơn giá</th><th>Tổng</th></tr></thead>
  <tbody>
    <?php foreach ($ct as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['MaSanPham']); ?></td>
        <td><?php echo htmlspecialchars($r['TenSanPham']); ?></td>
        <td><?php echo (int)$r['SoLuong']; ?></td>
        <td><?php echo number_format((float)$r['DonGia'],2); ?></td>
        <td><?php echo number_format((float)$r['TongTien'],2); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php include __DIR__ . '/../../partials/footer.php';


