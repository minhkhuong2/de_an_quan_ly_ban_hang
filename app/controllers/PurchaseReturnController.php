<?php
// Đường dẫn file: app/controllers/PurchaseReturnController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PurchaseReturnModel.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class PurchaseReturnController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $returns = (new PurchaseReturnModel($db))->getAllReturns();
        require_once __DIR__ . '/../views/purchase_return/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $suppliers = (new SupplierModel($db))->getAllSuppliers();
        $products = (new ProductModel($db))->getAllProducts();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_id = $_POST['supplier_id'];
            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $return_prices = $_POST['return_price'] ?? [];

            if (!empty($supplier_id) && !empty($product_ids)) {
                $model = new PurchaseReturnModel($db);
                if ($model->createReturn($supplier_id, $product_ids, $quantities, $return_prices)) {
                    header("Location: index.php?action=purchase_return_list&success=1");
                    exit;
                }
            }
        }
        require_once __DIR__ . '/../views/purchase_return/add.php';
    }
}
