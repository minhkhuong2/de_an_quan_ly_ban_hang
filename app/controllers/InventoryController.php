<?php
// Đường dẫn file: app/controllers/InventoryController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class InventoryController
{

    // 1. Hiển thị trang Quản lý Tồn Kho
    public function list()
    {
        $db = (new Database())->getConnection();

        // Viết thẳng Query ở đây cho nhanh để load danh sách kho
        $query = "SELECT id, product_name, sku, barcode, unit, image, stock, available, trading, incoming FROM products ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $inventories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/inventory/list.php';
    }

    // 2. Chỉnh sửa tồn kho nhanh (Bút chì)
    public function update_stock()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $id = $_POST['product_id'];
            $new_stock = $_POST['new_stock'];

            // Có thể bán = Tồn kho - Đang giao dịch (Công thức chuẩn kho)
            $query = "UPDATE products SET stock = :new_stock, available = (:new_stock - trading) WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':new_stock', $new_stock);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: index.php?action=inventory_list&success=1");
                exit;
            }
        }
    }
}
