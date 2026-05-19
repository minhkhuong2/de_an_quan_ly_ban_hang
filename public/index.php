<?php
// Đường dẫn file: public/index.php
session_start();

// 1. Khai báo các Controller
require_once __DIR__ . '/../app/controllers/ImeiController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CustomerController.php';
require_once __DIR__ . '/../app/controllers/InventoryController.php';
require_once __DIR__ . '/../app/controllers/SupplierController.php'; // Gọi Controller Nhà cung cấp

// 2. Khởi tạo đối tượng
$imeiController = new ImeiController();
$productController = new ProductController();
$dashboardController = new DashboardController();
$orderController = new OrderController();
$authController = new AuthController();
$customerController = new CustomerController();
$inventoryController = new InventoryController();
$supplierController = new SupplierController(); // Khởi tạo Nhà cung cấp

// 3. Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// === KIỂM TRA BẢO MẬT ĐĂNG NHẬP ===
$public_actions = ['login', 'register'];
if (!isset($_SESSION['user']) && !in_array($action, $public_actions)) {
    header("Location: index.php?action=login");
    exit;
}

// 4. BỘ ĐỊNH TUYẾN CHÍNH (ROUTING)
if ($action == 'login') {
    $authController->login();
} elseif ($action == 'register') {
    $authController->register();
} elseif ($action == 'logout') {
    $authController->logout();
} elseif ($action == 'dashboard') {
    $dashboardController->index();
}

// === SẢN PHẨM & DANH MỤC ===
elseif ($action == 'product_list') {
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
} elseif ($action == 'product_category') {
    $productController->category_list();
} elseif ($action == 'add_category') {
    $productController->add_category();
} elseif ($action == 'edit_category') {
    $productController->edit_category();
} elseif ($action == 'delete_category') {
    $productController->delete_category();
}

// === KHÁCH HÀNG ===
elseif ($action == 'customer_list') {
    $customerController->list();
} elseif ($action == 'add_customer') {
    $customerController->add();
} elseif ($action == 'delete_customer') {
    $customerController->delete();
}

// === NHÀ CUNG CẤP (MỚI) ===
elseif ($action == 'supplier_list') {
    $supplierController->list();
} elseif ($action == 'add_supplier') {
    $supplierController->add();
} elseif ($action == 'delete_supplier') {
    $supplierController->delete();
}

// === TỒN KHO NÂNG CAO ===
elseif ($action == 'inventory_list') {
    $inventoryController->list();
} elseif ($action == 'update_stock') {
    $inventoryController->update_stock();
}

// === IMEI & KHO (CŨ) ===
elseif ($action == 'list') {
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
}

// === BÁN HÀNG (POS) ===
elseif ($action == 'pos') {
    $orderController->pos();
} elseif ($action == 'scan_imei') {
    $orderController->scanImei();
} elseif ($action == 'checkout') {
    $orderController->checkout();
} else {
    echo "<h2 style='color: red; padding: 20px;'>Trang không tồn tại!</h2>";
}
