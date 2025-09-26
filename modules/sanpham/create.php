<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma = trim($_POST['MaSanPham'] ?? '');
    $ten = trim($_POST['TenSanPham'] ?? '');
    $sl  = (int)($_POST['SoLuong'] ?? 0);
    if ($ma === '') { $errors[] = 'Mã sản phẩm bắt buộc.'; }
    if ($ten === '') { $errors[] = 'Tên sản phẩm bắt buộc.'; }
    if (!$errors) {
        try {
            run('INSERT INTO SanPham(MaSanPham, TenSanPham, SoLuong) VALUES(?,?,?)', [$ma, $ten, $sl]);
            echo '<div class="alert alert-success">Đã thêm sản phẩm.</div>';
        } catch (Throwable $e) {
            $errors[] = 'Không thể thêm. Có thể mã đã tồn tại.';
        }
    }
}
?>
<h1 class="h4">Thêm sản phẩm</h1>
<?php foreach ($errors as $err) { echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; } ?>
<form method="post" class="row g-3">
  <div class="col-md-3"><label class="form-label">Mã sản phẩm</label><input name="MaSanPham" class="form-control" maxlength="10" required></div>
  <div class="col-md-6"><label class="form-label">Tên sản phẩm</label><input name="TenSanPham" class="form-control" maxlength="100" required></div>
  <div class="col-md-3"><label class="form-label">Số lượng</label><input name="SoLuong" type="number" class="form-control" value="0" min="0"></div>
  <div class="col-12">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=sanpham">Quay lại</a>
  </div>
  <input type="hidden" name="_csrf" value="1">
</form>
<?php include __DIR__ . '/../../partials/footer.php';


