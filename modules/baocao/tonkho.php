<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$rows = run('SELECT MaSanPham, TenSanPham, SoLuong FROM SanPham ORDER BY TenSanPham')->fetchAll();
?>
<h1 class="h4 mb-3">Báo cáo tồn kho</h1>
<div class="table-responsive">
<table class="table table-bordered table-sm">
  <thead class="table-light"><tr><th>Mã</th><th>Tên</th><th>Tồn kho</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['MaSanPham']); ?></td>
      <td><?php echo htmlspecialchars($r['TenSanPham']); ?></td>
      <td><?php echo (int)$r['SoLuong']; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  </table>
</div>

<?php include __DIR__ . '/../../partials/footer.php';


