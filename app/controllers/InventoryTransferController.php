<?php
// Đường dẫn: app/controllers/InventoryTransferController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/InventoryTransferModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class InventoryTransferController
{

    public function list()
    {
        $db = (new Database())->getConnection();
        $transModel = new InventoryTransferModel($db);

        // Lấy dữ liệu tìm kiếm & lọc từ URL (nếu có)
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $transfers = $transModel->getAllTransfers($search, $status);
        require_once __DIR__ . '/../views/inventory_transfer/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $transModel = new InventoryTransferModel($db);
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $from_branch = $_POST['from_branch'];
            $to_branch = $_POST['to_branch'];
            $employee = $_POST['employee'] ?? 'Admin';
            $note = $_POST['note'] ?? '';

            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $products = [];

            for ($i = 0; $i < count($product_ids); $i++) {
                if (!empty($product_ids[$i])) {
                    $products[] = [
                        'product_id' => $product_ids[$i],
                        'quantity' => (int)$quantities[$i]
                    ];
                }
            }

            if (!empty($products)) {
                $id = $transModel->createTransfer($from_branch, $to_branch, $employee, $note, $products);
                if ($id) {
                    header("Location: index.php?action=view_transfer&id=$id&success=1");
                    exit;
                }
            }
        }
        $allProducts = $productModel->getAllProducts();
        require_once __DIR__ . '/../views/inventory_transfer/add.php';
    }

    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $transModel = new InventoryTransferModel($db);

        $transfer = $transModel->getTransferById($id);
        if (!$transfer) {
            header("Location: index.php?action=transfer_list");
            exit;
        }
        $details = $transModel->getTransferDetails($id);

        require_once __DIR__ . '/../views/inventory_transfer/detail.php';
    }

    // Nơi xử lý bấm nút Đang chuyển / Nhận hàng
    public function updateStatus()
    {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';
        $db = (new Database())->getConnection();
        $transModel = new InventoryTransferModel($db);

        if ($status == 'start') {
            $transModel->startTransfer($id);
        } elseif ($status == 'receive') {
            $transModel->receiveTransfer($id);
        }
        header("Location: index.php?action=view_transfer&id=$id&updated=1");
        exit;
    }
}
