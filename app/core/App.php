<?php
// Đường dẫn file: app/core/App.php

class App
{
    // BẢNG ĐỊNH TUYẾN: Ánh xạ action từ URL sang đúng Controller và Hàm xử lý
    protected $routes = [
        // --- Tài khoản & Tổng quan ---
        'login'            => ['AuthController', 'login'],
        'register'         => ['AuthController', 'register'],
        'logout'           => ['AuthController', 'logout'],
        'dashboard'        => ['DashboardController', 'index'],

        // --- Sản phẩm & Danh mục ---
        'product_list'     => ['ProductController', 'list'],
        'add_product'      => ['ProductController', 'add'],
        'add_conversion'   => ['ProductController', 'add_conversion'],
        'add_combo'        => ['ProductController', 'add_combo'],
        'edit_product' => ['ProductController', 'edit'],
        'delete_product'   => ['ProductController', 'delete'],
        'product_category' => ['ProductController', 'category_list'],
        'add_category'     => ['ProductController', 'add_category'],
        'edit_category'    => ['ProductController', 'edit_category'],
        'delete_category'  => ['ProductController', 'delete_category'],
        'product_price'    => ['ProductController', 'price'],
        'add_price'        => ['ProductController', 'add_price'],
        // ĐÂY CHÍNH LÀ DÒNG FIX LỖI 404 CỦA CHÚNG TA:
        'quick_update_stock' => ['ProductController', 'quick_update_stock'],

        // --- Đối tác ---
        'customer_list'   => ['CustomerController', 'list'],
        'add_customer'    => ['CustomerController', 'add'],
        'edit_customer'   => ['CustomerController', 'edit'],
        'delete_customer' => ['CustomerController', 'delete'],
        'supplier_list' => ['SupplierController', 'list'],
        'add_supplier'  => ['SupplierController', 'add'],
        'edit_supplier'   => ['SupplierController', 'edit'],
        'delete_supplier' => ['SupplierController', 'delete'],

        // --- Quản lý Kho ---
        'inventory_list'   => ['InventoryController', 'list'],
        'update_stock'     => ['InventoryController', 'update_stock'],
        'purchase_list'    => ['PurchaseOrderController', 'list'],
        'add_purchase'     => ['PurchaseOrderController', 'add'],

        'inventory_check_list' => ['InventoryCheckController', 'list'],
        'add_inventory_check'  => ['InventoryCheckController', 'add'], // <--- Phải có dấu phẩy ở đây

        'view_purchase'    => ['PurchaseOrderController', 'detail'],
        'cancel_purchase'  => ['PurchaseOrderController', 'cancel'],
        'receive_purchase' => ['PurchaseOrderController', 'receiveForm'],
        'process_receive'  => ['PurchaseOrderController', 'processReceive'],
        'direct_receive'   => ['PurchaseOrderController', 'direct_receive'],
        'receipt_list'     => ['PurchaseOrderController', 'receiptList'],
        'pay_purchase' => ['PurchaseOrderController', 'pay'],
        'edit_inventory_check'   => ['InventoryCheckController', 'edit'],
        'delete_inventory_check' => ['InventoryCheckController', 'delete'],
        'transfer_list'     => ['InventoryTransferController', 'list'],
        'add_transfer'      => ['InventoryTransferController', 'add'],
        'view_transfer'     => ['InventoryTransferController', 'detail'],
        'update_transfer'   => ['InventoryTransferController', 'updateStatus'],
        'edit_purchase'    => ['PurchaseOrderController', 'edit'],
        'delete_purchase'  => ['PurchaseOrderController', 'delete'],
        'purchase_return_list'    => ['PurchaseReturnController', 'list'],
        'add_purchase_return'     => ['PurchaseReturnController', 'add'],
        'process_purchase_return' => ['PurchaseReturnController', 'process'],
        'add_direct_return'       => ['PurchaseReturnController', 'addDirect'],

        // Quản lý Chi nhánh (Kho lưu trữ)
        'branch_list'   => ['BranchController', 'list'],
        'add_branch'    => ['BranchController', 'add'],
        'edit_branch'   => ['BranchController', 'edit'],
        'delete_branch' => ['BranchController', 'delete'],

        // --- Quản lý IMEI (Cũ) ---
        'list'             => ['ImeiController', 'list'],
        'add'              => ['ImeiController', 'add'],
        'sell'             => ['ImeiController', 'sell'],
        'warranty'         => ['ImeiController', 'warranty'],
        'returnItem'       => ['ImeiController', 'returnItem'],
        'search'           => ['ImeiController', 'search'],
        // Quản lý Nhân viên
        'staff_list'      => ['StaffController', 'list'],
        'add_staff'       => ['StaffController', 'add'],
        'edit_staff'      => ['StaffController', 'edit'],
        'delete_staff'    => ['StaffController', 'delete'],
        'activate_staff'  => ['StaffController', 'activate'],

        // Quản lý Vận chuyển (Express)
        'shipping_list'   => ['ShippingController', 'list'],
        'add_shipping'    => ['ShippingController', 'add'],
        'edit_shipping'   => ['ShippingController', 'edit'],
        'delete_shipping' => ['ShippingController', 'delete'],

        // Quản lý Khuyến mại
        'promo_list'   => ['PromotionController', 'list'],
        'add_promo'    => ['PromotionController', 'add'],
        'bulk_action_promo' => ['PromotionController', 'bulkAction'],
        'view_promo'   => ['PromotionController', 'detail'], // Xem chi tiết
        'edit_promo'   => ['PromotionController', 'edit'],   // Sửa
        'copy_promo'   => ['PromotionController', 'duplicate'],
        'promo_settings'   => ['PromotionController', 'settings'],

        // Quản lý Cấu hình (Settings)
        'settings'          => ['SettingController', 'index'],
        'general_settings'  => ['SettingController', 'general'],

        // --- Quản lý Đơn hàng (Online) ---
        'order_list'       => ['OrderController', 'list'],
        'create_order'     => ['OrderController', 'create'],
        'calculate_api'    => ['OrderController', 'calculate_api'],


        // --- POS Bán hàng ---
        'pos'              => ['OrderController', 'pos'],
        'scan_imei'        => ['OrderController', 'scanImei'],
        'checkout'         => ['OrderController', 'checkout']

    ];

    public function __construct()
    {
        // Kiểm tra và khởi động Session nếu chưa kích hoạt
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Lấy action từ URL, nếu không có thì mặc định hiển thị trang tổng quan
        $action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

        // Kiểm tra bảo mật đăng nhập
        $public_actions = ['login', 'register'];
        if (!isset($_SESSION['user']) && !in_array($action, $public_actions)) {
            header("Location: index.php?action=login");
            exit;
        }

        // Định tuyến xử lý tự động gọi Controller
        if (array_key_exists($action, $this->routes)) {
            $controllerName = $this->routes[$action][0];
            $methodName = $this->routes[$action][1];

            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        $controller->{$methodName}();
                    } else {
                        echo "<h2 style='color: red; padding: 20px;'>Lỗi: Hàm {$methodName} không tồn tại trong {$controllerName}!</h2>";
                    }
                } else {
                    echo "<h2 style='color: red; padding: 20px;'>Lỗi: Lớp {$controllerName} không tồn tại!</h2>";
                }
            } else {
                echo "<h2 style='color: red; padding: 20px;'>Lỗi: Không tìm thấy file Controller tại đường dẫn {$controllerFile}!</h2>";
            }
        } else {
            echo "<h2 style='color: red; text-align:center; padding: 50px;'>404 - Trang không tồn tại!</h2>";
        }
    }
}
