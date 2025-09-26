<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10; $offset = ($page - 1) * $limit;

$where = '';
$params = [];
if ($q !== '') {
    $where = 'WHERE MaSanPham LIKE ? OR TenSanPham LIKE ?';
    $params = ['%'.$q.'%', '%'.$q.'%'];
}

$total = run('SELECT COUNT(*) c FROM SanPham ' . $where, $params)->fetch()['c'];
$rows = run('SELECT * FROM SanPham ' . $where . ' ORDER BY TenSanPham LIMIT '.$limit.' OFFSET '.$offset, $params)->fetchAll();
$pages = (int)ceil($total / $limit);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Sản phẩm</h1>
  <div>
    <a class="btn btn-success" href="<?php echo BASE_URL; ?>modules/sanpham/export_csv.php">Xuất CSV</a>
    <a class="btn btn-primary" href="<?php echo BASE_URL; ?>modules/sanpham/create.php">Thêm mới</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get" action="<?php echo BASE_URL; ?>">
  <input type="hidden" name="m" value="sanpham">
  <div class="col-auto"><input name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Tìm mã/tên"></div>
  <div class="col-auto"><button class="btn btn-outline-secondary">Tìm</button></div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle">
  <thead class="table-light"><tr><th>Mã</th><th>Tên</th><th>Tồn kho</th><th style="width:160px">Thao tác</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['MaSanPham']); ?></td>
        <td><?php echo htmlspecialchars($r['TenSanPham']); ?></td>
        <td><?php echo (int)$r['SoLuong']; ?></td>
        <td>
          <a class="btn btn-sm btn-warning" href="<?php echo BASE_URL; ?>modules/sanpham/edit.php?id=<?php echo urlencode($r['MaSanPham']); ?>">Sửa</a>
          <form method="post" action="<?php echo BASE_URL; ?>modules/sanpham/delete.php" style="display:inline" onsubmit="return confirm('Xóa sản phẩm?');">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($r['MaSanPham']); ?>">
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<nav>
  <ul class="pagination">
    <?php for ($i=1; $i<=$pages; $i++): ?>
      <li class="page-item <?php echo $i==$page?'active':''; ?>">
        <a class="page-link" href="<?php echo BASE_URL; ?>?m=sanpham&q=<?php echo urlencode($q); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

<?php include __DIR__ . '/../../partials/footer.php';


