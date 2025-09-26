<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$id = $_GET['id'] ?? '';
$sp = null;
if ($id !== '') {
    $sp = run('SELECT * FROM SanPham WHERE MaSanPham=?', [$id])->fetch();
}
if (!$sp) { echo '<div class="alert alert-danger">Không tìm thấy sản phẩm.</div>'; include __DIR__ . '/../../partials/footer.php'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['TenSanPham'] ?? '');
    $sl = (int)($_POST['SoLuong'] ?? 0);
    if ($ten === '') { $errors[] = 'Tên sản phẩm bắt buộc.'; }
    if (!$errors) {
        run('UPDATE SanPham SET TenSanPham=?, SoLuong=? WHERE MaSanPham=?', [$ten, $sl, $id]);
        echo '<div class="alert alert-success">Đã cập nhật.</div>';
        $sp['TenSanPham'] = $ten; $sp['SoLuong'] = $sl;
    }
}
?>
<h1 class="h4">Sửa sản phẩm</h1>
<?php foreach ($errors as $err) { echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; } ?>
<form method="post" class="row g-3">
  <div class="col-md-3"><label class="form-label">Mã</label><input class="form-control" value="<?php echo htmlspecialchars($sp['MaSanPham']); ?>" disabled></div>
  <div class="col-md-6"><label class="form-label">Tên</label><input name="TenSanPham" class="form-control" value="<?php echo htmlspecialchars($sp['TenSanPham']); ?>" required></div>
  <div class="col-md-3"><label class="form-label">Số lượng</label><input name="SoLuong" type="number" class="form-control" value="<?php echo (int)$sp['SoLuong']; ?>" min="0"></div>
  <div class="col-12"><button class="btn btn-primary">Lưu</button> <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=sanpham">Quay lại</a></div>
  <input type="hidden" name="_csrf" value="1">
</form>
<?php include __DIR__ . '/../../partials/footer.php';


