<?php
// Đường dẫn: app/controllers/InventoryCheckController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/InventoryCheckModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class InventoryCheckController
{

    // Xem danh sách phiếu kiểm
    public function list()
    {
        $db = (new Database())->getConnection();
        $checkModel = new InventoryCheckModel($db);
        $checks = $checkModel->getAllChecks();
        require_once __DIR__ . '/../views/inventory_check/list.php';
    }

    // Tạo phiếu và Cân bằng
    public function add()
    {
        $db = (new Database())->getConnection();
        $checkModel = new InventoryCheckModel($db);
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $branch = $_POST['branch'] ?? 'Cửa hàng chính';
            $employee = $_POST['employee'] ?? 'Admin';
            $note = $_POST['note'] ?? '';

            $product_ids = $_POST['product_id'] ?? [];
            $system_stocks = $_POST['system_stock'] ?? [];
            $actual_stocks = $_POST['actual_stock'] ?? [];
            $reasons = $_POST['reason'] ?? [];

            $products = [];

            for ($i = 0; $i < count($product_ids); $i++) {
                if (!empty($product_ids[$i])) {
                    $sys = (int)$system_stocks[$i];
                    $act = (int)$actual_stocks[$i];
                    $diff = $act - $sys; // Tính chênh lệch

                    $products[] = [
                        'product_id' => $product_ids[$i],
                        'system_stock' => $sys,
                        'actual_stock' => $act,
                        'difference' => $diff,
                        'reason' => $reasons[$i]
                    ];
                }
            }

            if (!empty($products)) {
                if ($checkModel->createAndBalance($branch, $employee, $note, $products)) {
                    header("Location: index.php?action=inventory_check_list&success=1");
                    exit;
                }
            }
        }

        // Lấy danh sách sản phẩm để chọn
        $allProducts = $productModel->getAllProducts();
        require_once __DIR__ . '/../views/inventory_check/add.php';
    }
    // Sửa phiếu kiểm kho
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $checkModel = new InventoryCheckModel($db);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employee = $_POST['employee'];
            $note = $_POST['note'];

            if ($checkModel->updateCheck($id, $employee, $note)) {
                header("Location: index.php?action=inventory_check_list&success_edit=1");
                exit;
            }
        }

        $check = $checkModel->getCheckById($id);
        $details = $checkModel->getCheckDetails($id);

        if (!$check) {
            header("Location: index.php?action=inventory_check_list");
            exit;
        }

        require_once __DIR__ . '/../views/inventory_check/edit.php';
    }

    // Xóa phiếu kiểm kho
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new Database())->getConnection();
            $checkModel = new InventoryCheckModel($db);
            $checkModel->deleteCheck($id);
        }
        header("Location: index.php?action=inventory_check_list&deleted=1");
        exit;
    }
}
