<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';

$by = $_GET['by'] ?? 'day';
if ($by === 'month') {
    $rows = run("SELECT DATE_FORMAT(NgayMua,'%Y-%m') AS nhom, SUM(CASE WHEN LoaiHoaDon='Ban' THEN ThanhTien ELSE -ThanhTien END) AS DoanhThu FROM HoaDon GROUP BY DATE_FORMAT(NgayMua,'%Y-%m') ORDER BY nhom DESC")->fetchAll();
} else {
    $rows = run("SELECT NgayMua AS nhom, SUM(CASE WHEN LoaiHoaDon='Ban' THEN ThanhTien ELSE -ThanhTien END) AS DoanhThu FROM HoaDon GROUP BY NgayMua ORDER BY nhom DESC")->fetchAll();
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Báo cáo doanh thu</h1>
  <div>
    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>?m=baocao&by=day">Theo ngày</a>
    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>?m=baocao&by=month">Theo tháng</a>
  </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm">
  <thead class="table-light"><tr><th>Nhóm</th><th>Doanh thu</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['nhom']); ?></td>
      <td><?php echo number_format((float)$r['DoanhThu'], 2); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php include __DIR__ . '/../../partials/footer.php';


