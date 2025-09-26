<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/nav.php';

$module = $_GET['m'] ?? '';
switch ($module) {
    case 'khachhang':
        require __DIR__ . '/modules/khachhang/list.php';
        break;
    case 'sanpham':
        require __DIR__ . '/modules/sanpham/list.php';
        break;
    case 'hoadon':
        require __DIR__ . '/modules/hoadon/list.php';
        break;
    case 'baocao':
        require __DIR__ . '/modules/baocao/doanhthu.php';
        break;
    default:
        echo '<h1 class="h4">Dashboard</h1>';
        echo '<div class="row g-3">';
        echo '  <div class="col-md-3"><a class="btn btn-primary w-100" href="./?m=khachhang">Khách hàng</a></div>';
        echo '  <div class="col-md-3"><a class="btn btn-primary w-100" href="./?m=sanpham">Sản phẩm</a></div>';
        echo '  <div class="col-md-3"><a class="btn btn-primary w-100" href="./?m=hoadon">Hóa đơn</a></div>';
        echo '  <div class="col-md-3"><a class="btn btn-primary w-100" href="./?m=baocao">Báo cáo</a></div>';
        echo '</div>';
}

include __DIR__ . '/partials/footer.php';

