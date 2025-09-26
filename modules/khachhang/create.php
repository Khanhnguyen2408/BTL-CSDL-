<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sdt = trim($_POST['SoDienThoai'] ?? '');
    $ten = trim($_POST['TenKhachHang'] ?? '');

    if ($sdt === '') { $errors[] = 'Số điện thoại bắt buộc.'; }
    if ($ten === '') { $errors[] = 'Tên khách hàng bắt buộc.'; }

    if (!$errors) {
        try {
            run('INSERT INTO KhachHang(SoDienThoai, TenKhachHang) VALUES(?, ?)', [$sdt, $ten]);
            echo '<div class="alert alert-success">Đã thêm khách hàng.</div>';
        } catch (Throwable $e) {
            $errors[] = 'Không thể thêm khách hàng. Có thể SĐT đã tồn tại.';
        }
    }
}
?>
<h1 class="h4">Thêm khách hàng</h1>
<?php foreach ($errors as $err) { echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; } ?>
<form method="post" class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Số điện thoại</label>
    <input name="SoDienThoai" class="form-control" required maxlength="15">
  </div>
  <div class="col-md-6">
    <label class="form-label">Tên khách hàng</label>
    <input name="TenKhachHang" class="form-control" required maxlength="100">
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=khachhang">Quay lại</a>
  </div>
  <input type="hidden" name="_csrf" value="1">
</form>
<?php include __DIR__ . '/../../partials/footer.php';


