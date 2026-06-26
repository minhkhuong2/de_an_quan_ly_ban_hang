<?php
// ÄÆ°á»ng dáº«n file: app/core/App.php

class App
{
    // Báº¢NG Äá»ŠNH TUYáº¾N: Ãnh xáº¡ action tá»« URL sang Ä‘Ãºng Controller vÃ  HÃ m xá»­ lÃ½
    protected $routes = [
        // --- TÃ i khoáº£n & Tá»•ng quan ---
        'login'            => ['AuthController', 'login'],
        'register'         => ['AuthController', 'register'],
        'logout'           => ['AuthController', 'logout'],
        'dashboard'        => ['DashboardController', 'index'],

        // --- Sáº£n pháº©m & Danh má»¥c ---
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
        // ÄÃ‚Y CHÃNH LÃ€ DÃ’NG FIX Lá»–I 404 Cá»¦A CHÃšNG TA:
        'quick_update_stock' => ['ProductController', 'quick_update_stock'],

        // Quáº£n lÃ½ Báº£ng giÃ¡ sáº£n pháº©m nÃ¢ng cao
        'product_price'             => ['PriceListController', 'index'],
        'add_price_list'            => ['PriceListController', 'create'],
        'store_price_list'          => ['PriceListController', 'store'],
        'price_list_add_items'      => ['PriceListController', 'add_items'],
        'store_price_list_items'    => ['PriceListController', 'store_items'],
        'price_list_detail'         => ['PriceListController', 'edit'],
        'api_update_price_item'     => ['PriceListController', 'update_item_price'],
        'api_delete_price_item'     => ['PriceListController', 'delete_item'],

        // --- Sá»• Quá»¹ & Káº¿ toÃ¡n DÃ²ng tiá»n ---
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

        // --- Quáº£n lÃ½ LÃ½ do Thu/Chi ---
        'fund_reasons'       => ['FundReasonController', 'index'],
        'store_fund_reason'  => ['FundReasonController', 'store'],
        'delete_fund_reason' => ['FundReasonController', 'delete'],
        'update_fund_reason' => ['FundReasonController', 'update'],
        'print_cashbook'     => ['FundReasonController', 'print_cashbook'],
        // --- Káº¿ toÃ¡n sá»‘ quá»¹ ---
        'fund_dashboard'        => ['FundController', 'dashboard'],
        'export_cashbook'       => ['FundController', 'export'],

        // --- Äá»‘i tÃ¡c ---
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

        // --- á»¨ng dá»¥ng Quáº£n lÃ½ CÃ´ng ná»£ ---
        'debt_app_list'         => ['DebtManagementController', 'index'],
        'debt_app_detail'       => ['DebtManagementController', 'detail'],
        'debt_app_adjust'       => ['DebtManagementController', 'adjust'],

        // --- Quáº£n lÃ½ Kho ---
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

        // Quáº£n lÃ½ Chi nhÃ¡nh (Kho lÆ°u trá»¯)
        'branch_list'           => ['BranchController', 'index'],
        'store_branch'          => ['BranchController', 'store'],
        'toggle_branch_status'  => ['BranchController', 'toggle_status'],
        'transfer_branch_data'  => ['BranchController', 'transfer_and_delete'],
        'update_branch_priority' => ['BranchController', 'update_priority'],

        // --- Quáº£n lÃ½ IMEI (CÅ©) ---
        'list'             => ['ImeiController', 'list'],
        'add'              => ['ImeiController', 'add'],
        'sell'             => ['ImeiController', 'sell'],
        'warranty'         => ['ImeiController', 'warranty'],
        'returnItem'       => ['ImeiController', 'returnItem'],
        'search'           => ['ImeiController', 'search'],
        // Quáº£n lÃ½ NhÃ¢n viÃªn
        'staff_list'      => ['StaffController', 'list'],
        'add_staff'       => ['StaffController', 'add'],
        'edit_staff'      => ['StaffController', 'edit'],
        'delete_staff'    => ['StaffController', 'delete'],
        'activate_staff'  => ['StaffController', 'activate'],

        // Quáº£n lÃ½ Váº­n chuyá»ƒn (Express)
        'shipping_list'   => ['ShippingController', 'list'],
        'add_shipping'    => ['ShippingController', 'add'],
        'edit_shipping'   => ['ShippingController', 'edit'],
        'delete_shipping' => ['ShippingController', 'delete'],

        // Quáº£n lÃ½ Khuyáº¿n máº¡i
        'promo_list'   => ['PromotionController', 'list'],
        'add_promo'    => ['PromotionController', 'add'],
        'bulk_action_promo' => ['PromotionController', 'bulkAction'],
        'view_promo'   => ['PromotionController', 'detail'], // Xem chi tiáº¿t
        'edit_promo'   => ['PromotionController', 'edit'],   // Sá»­a
        'copy_promo'   => ['PromotionController', 'duplicate'],
        'promo_settings'   => ['PromotionController', 'settings'],

        // Quáº£n lÃ½ Cáº¥u hÃ¬nh (Settings)
        'settings'          => ['SettingController', 'pos_settings'],
        'pos_settings'      => ['SettingController', 'pos_settings'],
        'save_pos_settings' => ['SettingController', 'save_pos_settings'],
        // --- Cáº¥u hÃ¬nh há»‡ thá»‘ng ---
        'settings_hub'        => ['SettingController', 'index'],
        'store_settings'      => ['SettingController', 'store_info'],
        'update_store_info'   => ['SettingController', 'update_store_info'],
        // --- Cáº¥u hÃ¬nh Thuáº¿ ---
        'tax_settings'        => ['TaxSettingController', 'index'],
        'update_tax_settings' => ['TaxSettingController', 'update'],
        // --- Cáº¥u hÃ¬nh Äá»‘i tÃ¡c váº­n chuyá»ƒn ---
        'shipping_settings'     => ['ShippingController', 'index'],
        'update_shipping_pkg'   => ['ShippingController', 'update_package'],
        'add_shipping_rate'     => ['ShippingController', 'add_rate'],
        'delete_shipping_rate'  => ['ShippingController', 'delete_rate'],

        // Quáº£n lÃ½ Nguá»“n Ä‘Æ¡n hÃ ng tÃ¹y chá»‰nh
        'order_sources'         => ['OrderSourceController', 'index'],
        'store_order_source'    => ['OrderSourceController', 'store'],
        'update_order_source'   => ['OrderSourceController', 'update'],
        'toggle_source_status'  => ['OrderSourceController', 'toggle_status'],
        'delete_order_source'   => ['OrderSourceController', 'delete'],

        // Cáº¥u hÃ¬nh quy trÃ¬nh xá»­ lÃ½ Ä‘Æ¡n hÃ ng
        'order_settings'        => ['OrderSettingController', 'index'],
        'save_order_settings'   => ['OrderSettingController', 'save'],

        // --- Quáº£n lÃ½ ÄÆ¡n hÃ ng (Online) ---
        'order_list'       => ['OrderController', 'list'],
        'create_order'     => ['OrderController', 'create'],
        'calculate_api'    => ['OrderController', 'calculate_api'],
        'store_order'      => ['OrderController', 'store'],
        'view_order'       => ['OrderController', 'view'],
        'print_order'      => ['OrderController', 'print'],
        'update_order_ship'  => ['OrderController', 'update_shipping'],
        'collect_order_pay'  => ['OrderController', 'collect_payment'],
        'cancel_order'       => ['OrderController', 'cancel'],
        'quick_add_customer' => ['OrderController', 'quick_add_customer'],
        'store_online_order' => ['OrderController', 'store_online_order'],
        'save_order_filter'     => ['OrderController', 'save_filter'],
        'bulk_ship_orders'      => ['OrderController', 'bulk_ship'],
        'bulk_order_actions'    => ['OrderController', 'bulk_actions'],
        'export_orders'         => ['OrderController', 'export_orders'],

        // --- Xá»­ lÃ½ ÄÆ¡n hÃ ng (TiÃªu chuáº©n Há»‡ thá»‘ng ) ---
        'order_processing'          => ['OrderProcessingController', 'index'],
        'order_processing_confirm'  => ['OrderProcessingController', 'confirm_bulk'],
        'order_processing_pack'     => ['OrderProcessingController', 'request_pack_bulk'],
        'order_processing_packed'   => ['OrderProcessingController', 'mark_packed_bulk'],
        'order_processing_handover' => ['OrderProcessingController', 'handover_bulk'],
        'order_processing_print'    => ['OrderProcessingController', 'print_docs'],

        // --- Quáº£n lÃ½ Váº­n ÄÆ¡n ---
        'shipment_list'         => ['ShipmentController', 'index'],
        'update_shipment_status' => ['ShipmentController', 'update_status_bulk'],
        'reconcile_shipments'   => ['ShipmentController', 'reconcile_bulk'],

        // Cáº¥u hÃ¬nh PhÆ°Æ¡ng thá»©c thanh toÃ¡n
        'payment_methods'           => ['PaymentMethodController', 'index'],
        'toggle_payment_method'     => ['PaymentMethodController', 'toggle'],
        'save_payment_config'       => ['PaymentMethodController', 'save_config'],
        'disconnect_payment_method' => ['PaymentMethodController', 'disconnect'],

        // --- BÃ¡o CÃ¡o ---
        'end_of_day_report' => ['ReportController', 'end_of_day'],


        // --- POS BÃ¡n hÃ ng ---
        'pos' => ['OrderController', 'pos'],
        'scan_imei'        => ['OrderController', 'scanImei'],
        'checkout'         => ['OrderController', 'checkout']

    ];

    public function __construct()
    {
        // Kiá»ƒm tra vÃ  khá»Ÿi Ä‘á»™ng Session náº¿u chÆ°a kÃ­ch hoáº¡t
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Láº¥y action tá»« URL, náº¿u khÃ´ng cÃ³ thÃ¬ máº·c Ä‘á»‹nh hiá»ƒn thá»‹ trang tá»•ng quan
        $action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

        // Kiá»ƒm tra báº£o máº­t Ä‘Äƒng nháº­p
        $public_actions = ['login', 'register'];
        if (!isset($_SESSION['user']) && !in_array($action, $public_actions)) {
            header("Location: index.php?action=login");
            exit;
        }

        // Äá»‹nh tuyáº¿n xá»­ lÃ½ tá»± Ä‘á»™ng gá»i Controller
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
                        echo "<h2 style='color: red; padding: 20px;'>Lá»—i: HÃ m {$methodName} khÃ´ng tá»“n táº¡i trong {$controllerName}!</h2>";
                    }
                } else {
                    echo "<h2 style='color: red; padding: 20px;'>Lá»—i: Lá»›p {$controllerName} khÃ´ng tá»“n táº¡i!</h2>";
                }
            } else {
                echo "<h2 style='color: red; padding: 20px;'>Lá»—i: KhÃ´ng tÃ¬m tháº¥y file Controller táº¡i Ä‘Æ°á»ng dáº«n {$controllerFile}!</h2>";
            }
        } else {
            echo "<h2 style='color: red; text-align:center; padding: 50px;'>404 - Trang khÃ´ng tá»“n táº¡i!</h2>";
        }
    }
}

