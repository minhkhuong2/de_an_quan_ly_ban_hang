<?php
// Đường dẫn file: app/controllers/InventoryCheckController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/InventoryCheckModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class InventoryCheckController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $checks = (new InventoryCheckModel($db))->getAllChecks();
        require_once __DIR__ . '/../views/inventory_check/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $products = (new ProductModel($db))->getProductsWithStock(); // Lấy SP và tồn kho hiện tại

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $product_ids = $_POST['product_id'] ?? [];
            $system_stocks = $_POST['system_stock'] ?? [];
            $actual_stocks = $_POST['actual_stock'] ?? [];
            $discrepancies = $_POST['discrepancy'] ?? [];

            if (!empty($product_ids)) {
                $model = new InventoryCheckModel($db);
                if ($model->createCheck($product_ids, $system_stocks, $actual_stocks, $discrepancies)) {
                    header("Location: index.php?action=inventory_check_list&success=1");
                    exit;
                }
            }
        }
        require_once __DIR__ . '/../views/inventory_check/add.php';
    }
}
