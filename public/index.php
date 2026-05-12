<?php
// Đường dẫn file: public/index.php

// 1. Khai báo các Controller
require_once __DIR__ . '/../app/controllers/ImeiController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';

// 2. Khởi tạo đối tượng
$imeiController = new ImeiController();
$productController = new ProductController();
$dashboardController = new DashboardController();
$orderController = new OrderController();

// 3. Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// 4. Bộ định tuyến (Routing)
if ($action == 'dashboard') {
    $dashboardController->index();
} elseif ($action == 'list') {
    $imeiController->list();
} elseif ($action == 'add') {
    $imeiController->add();
} elseif ($action == 'sell') {
    $imeiController->sell();
} elseif ($action == 'warranty') {
    $imeiController->warranty();
} elseif ($action == 'returnItem') {
    $imeiController->returnItem();
} elseif ($action == 'search') {
    $imeiController->search();

    // === CÁC ĐƯỜNG DẪN DÀNH CHO SẢN PHẨM ===
} elseif ($action == 'add_product') {
    $productController->add();
} elseif ($action == 'product_list') {     // <-- ĐÃ BỔ SUNG Ở ĐÂY
    $productController->list();
} elseif ($action == 'product_category') { // <-- ĐÃ BỔ SUNG Ở ĐÂY
    $productController->category();
} elseif ($action == 'product_price') {
    $productController->price();
} elseif ($action == 'pos') {
    $orderController->pos();
} elseif ($action == 'scan_imei') {        // THÊM DÒNG NÀY (Nhận request quét mã)
    $orderController->scanImei();
} elseif ($action == 'scan_imei') {
    $orderController->scanImei();
} elseif ($action == 'checkout') {         // THÊM DÒNG NÀY
    $orderController->checkout();          // THÊM DÒNG NÀY
} else {
    echo "<h2 style='color: red; padding: 20px;'>Trang không tồn tại!</h2>";
}
