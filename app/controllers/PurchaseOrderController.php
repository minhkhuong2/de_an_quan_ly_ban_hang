<?php
// Đường dẫn file: app/controllers/PurchaseOrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PurchaseOrderModel.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class PurchaseOrderController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $orders = (new PurchaseOrderModel($db))->getAllOrders();
        require_once __DIR__ . '/../views/purchase_order/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();

        // Lấy danh sách Nhà cung cấp và Sản phẩm để đổ vào Dropdown
        $suppliers = (new SupplierModel($db))->getAllSuppliers();
        $products = (new ProductModel($db))->getAllProducts();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_id = $_POST['supplier_id'];
            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $import_prices = $_POST['import_price'] ?? [];

            if (!empty($supplier_id) && !empty($product_ids)) {
                $model = new PurchaseOrderModel($db);
                if ($model->createOrder($supplier_id, $product_ids, $quantities, $import_prices)) {
                    header("Location: index.php?action=purchase_list&success=1");
                    exit;
                }
            }
        }
        require_once __DIR__ . '/../views/purchase_order/add.php';
    }
}
