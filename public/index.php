<?php
// Đường dẫn file: public/index.php

// Khai báo các Controller
require_once __DIR__ . '/../app/controllers/ImeiController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
$imeiController = new ImeiController();
$productController = new ProductController();
$dashboardController = new DashboardController();

// Đặt mặc định khi vừa vào web sẽ mở trang Dashboard
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Bộ định tuyến (Routing)
if ($action == 'dashboard') {
    // Gọi qua Controller thay vì require trực tiếp View như cũ
    $dashboardController->index(); // DÒNG SỬA MỚI
} elseif ($action == 'list') {
    $imeiController->list(); // CHỖ NÀY PHẢI LÀ list() CHỨ KHÔNG PHẢI getList()
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
} elseif ($action == 'add_product') {
    $productController->add();
} else {
    echo "Trang không tồn tại!";
}
