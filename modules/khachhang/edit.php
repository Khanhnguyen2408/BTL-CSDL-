<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$id = $_GET['id'] ?? '';
$kh = null;
if ($id !== '') {
    $kh = run('SELECT * FROM KhachHang WHERE SoDienThoai=?', [$id])->fetch();
}
if (!$kh) { echo '<div class="alert alert-danger">Không tìm thấy khách hàng.</div>'; include __DIR__ . '/../../partials/footer.php'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['TenKhachHang'] ?? '');
    if ($ten === '') { $errors[] = 'Tên khách hàng bắt buộc.'; }
    if (!$errors) {
        run('UPDATE KhachHang SET TenKhachHang=? WHERE SoDienThoai=?', [$ten, $id]);
        echo '<div class="alert alert-success">Đã cập nhật.</div>';
        $kh['TenKhachHang'] = $ten;
    }
}
?>
<h1 class="h4">Sửa khách hàng</h1>
<?php foreach ($errors as $err) { echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; } ?>
<form method="post" class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Số điện thoại</label>
    <input class="form-control" value="<?php echo htmlspecialchars($kh['SoDienThoai']); ?>" disabled>
  </div>
  <div class="col-md-6">
    <label class="form-label">Tên khách hàng</label>
    <input name="TenKhachHang" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($kh['TenKhachHang']); ?>">
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=khachhang">Quay lại</a>
  </div>
  <input type="hidden" name="_csrf" value="1">
</form>
<?php include __DIR__ . '/../../partials/footer.php';


