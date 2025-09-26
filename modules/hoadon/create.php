<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/nav.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sdt = trim($_POST['SoDienThoai'] ?? '');
    $ngay = trim($_POST['NgayMua'] ?? date('Y-m-d'));
    $loai = $_POST['LoaiHoaDon'] ?? 'Ban';
    $items = $_POST['items'] ?? [];

    if (!in_array($loai, ['Nhap','Ban'], true)) { $errors[] = 'Loại hóa đơn không hợp lệ.'; }
    if ($ngay === '') { $errors[] = 'Ngày mua bắt buộc.'; }
    if ($loai === 'Ban' && $sdt === '') { $errors[] = 'Bán hàng cần có khách hàng.'; }
    if (!$items || !is_array($items)) { $errors[] = 'Cần ít nhất một dòng hàng.'; }

    // Làm sạch items
    $clean = [];
    foreach ($items as $it) {
        $ma = trim($it['MaSanPham'] ?? '');
        $sl = (int)($it['SoLuong'] ?? 0);
        $gia = (float)($it['DonGia'] ?? 0);
        if ($ma !== '' && $sl > 0 && $gia >= 0) {
            $clean[] = ['MaSanPham'=>$ma,'SoLuong'=>$sl,'DonGia'=>$gia];
        }
    }
    if (!$clean) { $errors[] = 'Dòng hàng không hợp lệ.'; }

    if (!$errors) {
        try {
            $newId = transaction(function(PDO $pdo) use ($sdt,$ngay,$loai,$clean) {
                // Insert master
                $stmt = $pdo->prepare('INSERT INTO HoaDon(SoDienThoai, NgayMua, LoaiHoaDon, ThanhTien) VALUES(?,?,?,0)');
                $stmt->execute([$sdt ?: null, $ngay, $loai]);
                $mahd = (int)$pdo->lastInsertId();

                $sum = 0;
                foreach ($clean as $it) {
                    // Nếu bán: kiểm tra tồn kho
                    if ($loai === 'Ban') {
                        $stok = (int)$pdo->prepare('SELECT SoLuong FROM SanPham WHERE MaSanPham=?')->execute([$it['MaSanPham']]) ?: null;
                        $q = $pdo->prepare('SELECT SoLuong FROM SanPham WHERE MaSanPham=?');
                        $q->execute([$it['MaSanPham']]);
                        $row = $q->fetch();
                        if (!$row || (int)$row['SoLuong'] < $it['SoLuong']) {
                            throw new Exception('Tồn kho không đủ cho mã: '.$it['MaSanPham']);
                        }
                    }
                    $tong = $it['SoLuong'] * $it['DonGia'];
                    $sum += $tong;
                    $pdo->prepare('INSERT INTO ChiTietHoaDon(MaHoaDon,MaSanPham,SoLuong,DonGia,TongTien) VALUES(?,?,?,?,?)')
                        ->execute([$mahd,$it['MaSanPham'],$it['SoLuong'],$it['DonGia'],$tong]);

                    // Cập nhật tồn kho
                    if ($loai === 'Nhap') {
                        $pdo->prepare('UPDATE SanPham SET SoLuong = SoLuong + ? WHERE MaSanPham=?')->execute([$it['SoLuong'],$it['MaSanPham']]);
                    } else {
                        $pdo->prepare('UPDATE SanPham SET SoLuong = SoLuong - ? WHERE MaSanPham=?')->execute([$it['SoLuong'],$it['MaSanPham']]);
                    }
                }
                // Update master total
                $pdo->prepare('UPDATE HoaDon SET ThanhTien=? WHERE MaHoaDon=?')->execute([$sum,$mahd]);
                return $mahd;
            });
            header('Location: ' . BASE_URL . 'modules/hoadon/view.php?id=' . $newId);
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Lưu hóa đơn thất bại: ' . $e->getMessage();
        }
    }
}

$khach = run('SELECT SoDienThoai, TenKhachHang FROM KhachHang ORDER BY TenKhachHang')->fetchAll();
$sp = run('SELECT MaSanPham, TenSanPham FROM SanPham ORDER BY TenSanPham')->fetchAll();
?>
<h1 class="h4">Tạo hóa đơn</h1>
<?php foreach ($errors as $err) { echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; } ?>
<form method="post" class="vstack gap-3">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Ngày</label>
      <input type="date" name="NgayMua" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Loại</label>
      <select name="LoaiHoaDon" class="form-select">
        <option value="Nhap">Nhập</option>
        <option value="Ban">Bán</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Khách hàng (SĐT)</label>
      <input list="kh_list" name="SoDienThoai" class="form-control" placeholder="090... (để trống nếu Nhập)">
      <datalist id="kh_list">
        <?php foreach ($khach as $k): ?>
          <option value="<?php echo htmlspecialchars($k['SoDienThoai']); ?>"><?php echo htmlspecialchars($k['TenKhachHang']); ?></option>
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <div>
    <table class="table table-bordered align-middle" id="items">
      <thead class="table-light"><tr><th style="width:260px">Sản phẩm</th><th style="width:120px">Số lượng</th><th style="width:140px">Đơn giá</th><th>Tổng</th><th style="width:40px"></th></tr></thead>
      <tbody></tbody>
      <tfoot>
        <tr><td colspan="5"><button type="button" class="btn btn-outline-primary" onclick="addRow()">+ Thêm dòng</button></td></tr>
      </tfoot>
    </table>
  </div>

  <div>
    <button class="btn btn-primary">Lưu hóa đơn</button>
    <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>?m=hoadon">Quay lại</a>
  </div>
  <input type="hidden" name="_csrf" value="1">
</form>

<script>
const PRODUCTS = <?php echo json_encode($sp, JSON_UNESCAPED_UNICODE); ?>;
function addRow(){
  const tbody=document.querySelector('#items tbody');
  const tr=document.createElement('tr');
  tr.innerHTML=`
    <td>
      <input list="sp_list" name="items[00][MaSanPham]" class="form-control" required>
      <datalist id="sp_list">
        ${PRODUCTS.map(p=>`<option value="${p.MaSanPham}">${p.TenSanPham}</option>`).join('')}
      </datalist>
    </td>
    <td><input type="number" step="1" min="1" value="1" name="items[00][SoLuong]" class="form-control" oninput="recalc(this)"></td>
    <td><input type="number" step="0.01" min="0" value="0" name="items[00][DonGia]" class="form-control" oninput="recalc(this)"></td>
    <td class="text-end">0.00</td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">x</button></td>`;
  tbody.appendChild(tr);
}
function recalc(el){
  const tr=el.closest('tr');
  const qty=parseFloat(tr.querySelector('input[name="items[][SoLuong]"]').value)||0;
  const price=parseFloat(tr.querySelector('input[name="items[][DonGia]"]').value)||0;
  tr.querySelector('td.text-end').textContent=(qty*price).toFixed(2);
}
addRow();
</script>
<?php include __DIR__ . '/../../partials/footer.php';


