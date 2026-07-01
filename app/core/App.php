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
        'add_price'        => ['ProductController', 'add_price'],
        // ĐÂY CHÍNH LÀ DÒNG FIX LỖI 404 CỦA CHÚNG TA:
        'quick_update_stock' => ['ProductController', 'quick_update_stock'],

        // Quản lý Bảng giá sản phẩm nâng cao
        'product_price'             => ['PriceListController', 'index'],
        'add_price_list'            => ['PriceListController', 'create'],
        'store_price_list'          => ['PriceListController', 'store'],
        'price_list_add_items'      => ['PriceListController', 'add_items'],
        'store_price_list_items'    => ['PriceListController', 'store_items'],
        'price_list_detail'         => ['PriceListController', 'edit'],
        'api_update_price_item'     => ['PriceListController', 'update_item_price'],
        'api_delete_price_item'     => ['PriceListController', 'delete_item'],

        // --- Sổ Quỹ & Kế toán Dòng tiền ---
        'fund_transfers'        => ['FundTransferController', 'index'],
        'create_fund_transfer'  => ['FundTransferController', 'create'],
        'store_fund_transfer'   => ['FundTransferController', 'store'],
        'fund_transfer_detail'  => ['FundTransferController', 'detail'],
        'update_fund_transfer'  => ['FundTransferController', 'update'],
        'api_delete_fund'       => ['FundTransferController', 'delete_single'],
        'api_delete_bulk_fund'  => ['FundTransferController', 'delete_bulk'],
        'create_receipt' => ['ReceiptController', 'create'],
        'store_receipt'  => ['ReceiptController', 'store'],
        'create_expense' => ['ExpenseController', 'create'],
        'store_expense'  => ['ExpenseController', 'store'],
        'api_export_debt'      => ['CustomerController', 'export_debt'],
        'expense_list'   => ['ExpenseController', 'index'],
        'expense_detail' => ['ExpenseController', 'detail'],
        'update_expense' => ['ExpenseController', 'update'],
        'api_delete_expense' => ['ExpenseController', 'delete'],
        'receipt_list'          => ['ReceiptController', 'index'],
        'receipt_detail'        => ['ReceiptController', 'detail'],
        'update_receipt'        => ['ReceiptController', 'update'],
        'api_delete_receipt'    => ['ReceiptController', 'delete'],

        // --- Quản lý Lý do Thu/Chi ---
        'fund_reasons'       => ['FundReasonController', 'index'],
        'store_fund_reason'  => ['FundReasonController', 'store'],
        'delete_fund_reason' => ['FundReasonController', 'delete'],
        'update_fund_reason' => ['FundReasonController', 'update'],
        'print_cashbook'     => ['FundReasonController', 'print_cashbook'],
        // --- Kế toán số quỹ ---
        'fund_dashboard'        => ['FundController', 'dashboard'],
        'export_cashbook'       => ['FundController', 'export'],

        // --- Đối tác ---
        'customer_list'   => ['CustomerController', 'list'],
        'add_customer'    => ['CustomerController', 'add'],
        'edit_customer'   => ['CustomerController', 'edit'],
        'delete_customer' => ['CustomerController', 'delete'],
        'supplier_list' => ['SupplierController', 'list'],
        'add_supplier'  => ['SupplierController', 'add'],
        'edit_supplier'   => ['SupplierController', 'edit'],
        'delete_supplier' => ['SupplierController', 'delete'],
        'create_customer_group' => ['CustomerGroupController', 'create'],
        'store_customer_group'  => ['CustomerGroupController', 'store'],
        'api_export_customers'  => ['CustomerGroupController', 'export_customers'],
        'customer_groups'       => ['CustomerGroupController', 'index'],
        'customer_group_detail' => ['CustomerGroupController', 'detail'],
        'add_group_members'     => ['CustomerGroupController', 'store_members'],
        'customer_debt'        => ['CustomerController', 'debt_history'],
        'adjust_customer_debt' => ['CustomerController', 'adjust_debt'],

        // --- Ứng dụng Quản lý Công nợ ---
        'debt_app_list'         => ['DebtManagementController', 'index'],
        'debt_app_detail'       => ['DebtManagementController', 'detail'],
        'debt_app_adjust'       => ['DebtManagementController', 'adjust'],

        // --- Quản lý Kho ---
        'inventory_list'   => ['InventoryController', 'list'],
        'update_stock'     => ['InventoryController', 'update_stock'],
        'purchase_list'    => ['PurchaseOrderController', 'list'],
        'add_purchase'     => ['PurchaseOrderController', 'add'],

        'inventory_check_list' => ['InventoryCheckController', 'list'],
        'add_inventory_check'  => ['InventoryCheckController', 'add'],

        'view_purchase'    => ['PurchaseOrderController', 'detail'],
        'cancel_purchase'  => ['PurchaseOrderController', 'cancel'],
        'receive_purchase' => ['PurchaseOrderController', 'receiveForm'],
        'process_receive'  => ['PurchaseOrderController', 'processReceive'],
        'direct_receive'   => ['PurchaseOrderController', 'direct_receive'],
        'po_receipt_list'  => ['PurchaseOrderController', 'receiptList'],
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
        'po_list' => ['PurchaseOrderController', 'list'],

        // Quản lý Chi nhánh (Kho lưu trữ)
        'branch_list'           => ['BranchController', 'index'],
        'store_branch'          => ['BranchController', 'store'],
        'toggle_branch_status'  => ['BranchController', 'toggle_status'],
        'transfer_branch_data'  => ['BranchController', 'transfer_and_delete'],
        'update_branch_priority' => ['BranchController', 'update_priority'],

        // --- Quản lý IMEI (Cũ) ---
        'list'             => ['ImeiController', 'list'],
        'add'              => ['ImeiController', 'add'],
        'sell'             => ['ImeiController', 'sell'],
        'warranty'         => ['ImeiController', 'warranty'],
        'returnItem'       => ['ImeiController', 'returnItem'],
        'search'           => ['ImeiController', 'search'],
        // Quản lý Biên bản bàn giao (Handover)
        'handover_list'    => ['HandoverController', 'index'],
        'create_handover'  => ['HandoverController', 'create'],
        'store_handover'   => ['HandoverController', 'store'],
        'handover_detail'  => ['HandoverController', 'detail'],
        'delete_handover'  => ['HandoverController', 'delete'],
        'confirm_handover' => ['HandoverController', 'confirm'],

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
        'settings'          => ['SettingController', 'pos_settings'],
        'pos_settings'      => ['SettingController', 'pos_settings'],
        'save_pos_settings' => ['SettingController', 'save_pos_settings'],
        // --- Cấu hình hệ thống ---
        'settings_hub'        => ['SettingController', 'index'],
        'store_settings'      => ['SettingController', 'store_info'],
        'update_store_info'   => ['SettingController', 'update_store_info'],
        // --- Cấu hình Thuế ---
        'tax_settings'        => ['TaxSettingController', 'index'],
        'update_tax_settings' => ['TaxSettingController', 'update'],
        // --- Cấu hình Đối tác vận chuyển ---
        'shipping_settings'     => ['ShippingController', 'index'],
        'update_shipping_pkg'   => ['ShippingController', 'update_package'],
        'add_shipping_rate'     => ['ShippingController', 'add_rate'],
        'delete_shipping_rate'  => ['ShippingController', 'delete_rate'],

        // Quản lý Nguồn đơn hàng tùy chỉnh
        'order_sources'         => ['OrderSourceController', 'index'],
        'store_order_source'    => ['OrderSourceController', 'store'],
        'update_order_source'   => ['OrderSourceController', 'update'],
        'toggle_source_status'  => ['OrderSourceController', 'toggle_status'],
        'delete_order_source'   => ['OrderSourceController', 'delete'],

        // Cấu hình quy trình xử lý đơn hàng
        'order_settings'        => ['OrderSettingController', 'index'],
        'save_order_settings'   => ['OrderSettingController', 'save'],

        // --- Quản lý Đơn hàng (Online) ---
        'order_list'       => ['OrderController', 'list'],
        'create_order'     => ['OrderController', 'create'],
        
        // --- Quản lý Đơn hàng nháp ---
        'draft_list'       => ['DraftOrderController', 'index'],
        'copy_draft'       => ['DraftOrderController', 'copy'],
        'delete_draft'     => ['DraftOrderController', 'delete_bulk'],
        'update_draft_tags'=> ['DraftOrderController', 'update_tags'],

        'edit_order'       => ['OrderController', 'edit'],
        'update_order'     => ['OrderController', 'update_order'],
        'confirm_order'    => ['OrderController', 'confirm_order'],
        'calculate_api'    => ['OrderController', 'calculate_api'],
        'store_order'      => ['OrderController', 'store'],
        'view_order'       => ['OrderController', 'view'],
        'print_order'      => ['OrderController', 'print_order'],
        'print_orders'     => ['OrderController', 'print_orders'],
        'update_order_ship'  => ['OrderController', 'update_shipping'],
        'collect_order_pay'  => ['OrderController', 'collect_payment'],
        'cancel_order'       => ['OrderController', 'cancel_order'],
        'archive_order'      => ['OrderController', 'archive_order'],
        'delete_order'       => ['OrderController', 'delete_order'],
        'update_order_meta'  => ['OrderController', 'update_order_meta'],
        'quick_add_customer' => ['OrderController', 'quick_add_customer'],
        'store_online_order' => ['OrderController', 'store_online_order'],
        'save_order_filter'     => ['OrderController', 'save_filter'],
        'bulk_ship_orders'      => ['OrderController', 'bulk_ship'],
        'bulk_order_actions'    => ['OrderController', 'bulk_actions'],
        'export_orders'         => ['OrderController', 'export_orders'],

        // --- Xử lý Đơn hàng (Tiêu chuẩn Hệ thống ) ---
        'order_processing'          => ['OrderProcessingController', 'index'],
        'order_processing_confirm'  => ['OrderProcessingController', 'confirm_bulk'],
        'order_processing_pack'     => ['OrderProcessingController', 'request_pack_bulk'],
        'order_processing_packed'   => ['OrderProcessingController', 'mark_packed_bulk'],
        'order_processing_handover' => ['OrderProcessingController', 'handover_bulk'],
        'order_processing_print'    => ['OrderProcessingController', 'print_docs'],
        'order_processing_advanced_action' => ['OrderProcessingController', 'advanced_action'],

        // --- Quản lý Vận Đơn ---
        'shipment_list'         => ['ShipmentController', 'list'],
        'update_shipment_status' => ['ShipmentController', 'change_status'],
        'reconcile_shipments'   => ['ShipmentController', 'reconcile'],
        'cancel_shipment'       => ['ShipmentController', 'cancel_shipment'],
        'print_shipping'        => ['ShipmentController', 'print_shipping'],
        'print_handover_slip'   => ['ShipmentController', 'print_handover'],

        // Cấu hình Phương thức thanh toán
        'payment_methods'           => ['PaymentMethodController', 'index'],
        'toggle_payment_method'     => ['PaymentMethodController', 'toggle'],
        'save_payment_config'       => ['PaymentMethodController', 'save_config'],
        'disconnect_payment_method' => ['PaymentMethodController', 'disconnect'],

        // --- Báo Cáo ---
        'end_of_day_report' => ['ReportController', 'end_of_day'],


        // --- POS Bán hàng ---
        'pos' => ['OrderController', 'pos'],
        'scan_imei'        => ['OrderController', 'scanImei'],
        'checkout'         => ['OrderController', 'checkout'],

        // --- Đổi/Trả hàng ---
        'return_order_list' => ['ReturnOrderController', 'index'],
        'create_return'     => ['ReturnOrderController', 'create'],
        'store_return'      => ['ReturnOrderController', 'store'],
        'return_detail'     => ['ReturnOrderController', 'detail'],
        'receive_return'    => ['ReturnOrderController', 'receive_items'],
        'refund_return'     => ['ReturnOrderController', 'refund'],
        'cancel_return'     => ['ReturnOrderController', 'cancel'],

        // --- Đơn chưa hoàn tất ---
        'incomplete_list'   => ['IncompleteOrderController', 'index'],
        'incomplete_detail' => ['IncompleteOrderController', 'detail'],
        'incomplete_send_email' => ['IncompleteOrderController', 'send_email'],
        'incomplete_bulk'   => ['IncompleteOrderController', 'bulk_action'],
        'incomplete_export' => ['IncompleteOrderController', 'export'],
        
        // --- Hóa đơn điện tử ---
        'invoice_list'      => ['EInvoiceController', 'index'],
        'request_invoice'   => ['EInvoiceController', 'request_invoice'],
        'issue_invoice'     => ['EInvoiceController', 'issue_invoice'],
        'bulk_issue_invoice'=> ['EInvoiceController', 'bulk_issue'],

        // --- Cấu hình ---
        'checkout_settings' => ['SettingsController', 'checkout']
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
