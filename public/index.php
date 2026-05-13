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
} elseif ($action == 'product_list') {
    $productController->list();
} elseif ($action == 'add_product') {
    $productController->add();
} elseif ($action == 'edit_product') {
    $productController->edit();
} elseif ($action == 'delete_product') {
    $productController->delete();
} elseif ($action == 'product_price') {
    $productController->price();
} elseif ($action == 'add_price') {        // THÊM ROUTE NÀY CHO BẢNG GIÁ
    $productController->add_price();

    // === CÁC ĐƯỜNG DẪN DÀNH CHO DANH MỤC ===
} elseif ($action == 'product_category') {
    $productController->category_list();     // Nối vào hàm danh sách
} elseif ($action == 'add_category') {
    $productController->add_category();      // Nối vào hàm thêm
} elseif ($action == 'edit_category') {
    $productController->edit_category();     // Nối vào hàm sửa
} elseif ($action == 'delete_category') {
    $productController->delete_category();   // Nối vào hàm xóa

    // === CÁC ĐƯỜNG DẪN DÀNH CHO BÁN HÀNG (POS) ===
} elseif ($action == 'pos') {
    $orderController->pos();
} elseif ($action == 'scan_imei') {
    $orderController->scanImei();
} elseif ($action == 'checkout') {
    $orderController->checkout();

    // Lỗi 404
} else {
    echo "<h2 style='color: red; padding: 20px;'>Trang không tồn tại!</h2>";
}
