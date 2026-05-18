<?php
// Đường dẫn file: public/index.php
session_start(); // Khởi động Session để lưu trạng thái đăng nhập

// 1. Khai báo các Controller
require_once __DIR__ . '/../app/controllers/ImeiController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// 2. Khởi tạo đối tượng
$imeiController = new ImeiController();
$productController = new ProductController();
$dashboardController = new DashboardController();
$orderController = new OrderController();
$authController = new AuthController();

// 3. Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// === KIỂM TRA BẢO MẬT ĐĂNG NHẬP ===
$public_actions = ['login', 'register'];
if (!isset($_SESSION['user']) && !in_array($action, $public_actions)) {
    header("Location: index.php?action=login");
    exit;
}

// 4. Bộ định tuyến (Routing)
if ($action == 'login') {
    $authController->login();
} elseif ($action == 'register') {
    $authController->register();
} elseif ($action == 'logout') {
    $authController->logout();
} elseif ($action == 'dashboard') {
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
} elseif ($action == 'add_price') {
    $productController->add_price();

    // === CÁC ĐƯỜNG DẪN DÀNH CHO DANH MỤC (ĐÃ SỬA LỖI Ở ĐÂY) ===
} elseif ($action == 'product_category') {
    $productController->category_list(); // <- Gọi đúng hàm category_list()
} elseif ($action == 'add_category') {
    $productController->add_category();
} elseif ($action == 'edit_category') {
    $productController->edit_category();
} elseif ($action == 'delete_category') {
    $productController->delete_category();

    // === CÁC ĐƯỜNG DẪN DÀNH CHO BÁN HÀNG (POS) ===
} elseif ($action == 'pos') {
    $orderController->pos();
} elseif ($action == 'scan_imei') {
    $orderController->scanImei();
} elseif ($action == 'checkout') {
    $orderController->checkout();
} else {
    echo "<h2 style='color: red; padding: 20px;'>Trang không tồn tại!</h2>";
}
