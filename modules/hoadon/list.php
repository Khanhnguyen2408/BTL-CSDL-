<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$loai = $_GET['loai'] ?? '';

$conds = [];$params = [];
if ($from !== '') { $conds[] = 'NgayMua >= ?'; $params[] = $from; }
if ($to !== '')   { $conds[] = 'NgayMua <= ?'; $params[] = $to; }
if ($loai !== '') { $conds[] = 'LoaiHoaDon = ?'; $params[] = $loai; }
$where = $conds ? ('WHERE ' . implode(' AND ', $conds)) : '';

$rows = run('SELECT h.*, k.TenKhachHang FROM HoaDon h LEFT JOIN KhachHang k ON k.SoDienThoai=h.SoDienThoai ' . $where . ' ORDER BY MaHoaDon DESC', $params)->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Hóa đơn</h1>
  <div>
    <a class="btn btn-success" href="<?php echo BASE_URL; ?>modules/hoadon/export_csv.php">Xuất CSV</a>
    <a class="btn btn-primary" href="<?php echo BASE_URL; ?>modules/hoadon/create.php">Tạo hóa đơn</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get" action="<?php echo BASE_URL; ?>">
  <input type="hidden" name="m" value="hoadon">
  <div class="col-auto"><input type="date" class="form-control" name="from" value="<?php echo htmlspecialchars($from); ?>" placeholder="Từ ngày"></div>
  <div class="col-auto"><input type="date" class="form-control" name="to" value="<?php echo htmlspecialchars($to); ?>" placeholder="Đến ngày"></div>
  <div class="col-auto">
    <select name="loai" class="form-select">
      <option value="">-- Loại --</option>
      <option value="Nhap" <?php echo $loai==='Nhap'?'selected':''; ?>>Nhập</option>
      <option value="Ban" <?php echo $loai==='Ban'?'selected':''; ?>>Bán</option>
    </select>
  </div>
  <div class="col-auto"><button class="btn btn-outline-secondary">Lọc</button></div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle">
  <thead class="table-light"><tr><th>Mã</th><th>Ngày</th><th>Loại</th><th>Khách hàng</th><th>Thanh tiền</th><th>Thao tác</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td>#<?php echo (int)$r['MaHoaDon']; ?></td>
        <td><?php echo htmlspecialchars($r['NgayMua']); ?></td>
        <td><?php echo htmlspecialchars($r['LoaiHoaDon']); ?></td>
        <td><?php echo htmlspecialchars($r['TenKhachHang'] ?? ''); ?></td>
        <td><?php echo number_format((float)$r['ThanhTien'], 2); ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>modules/hoadon/view.php?id=<?php echo $r['MaHoaDon']; ?>">Xem</a>
          <form method="post" action="<?php echo BASE_URL; ?>modules/hoadon/delete.php" style="display:inline" onsubmit="return confirm('Xóa hóa đơn?');">
            <input type="hidden" name="id" value="<?php echo (int)$r['MaHoaDon']; ?>">
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>




