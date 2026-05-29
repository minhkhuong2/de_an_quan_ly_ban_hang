<?php
// Đường dẫn: app/models/InventoryModel.php
class InventoryModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getInventoryList($filters = [])
    {
        // Lấy dữ liệu kho từ bảng products. 
        // (Ghi chú cho đồ án: Đang giao dịch và Đang về kho tạm set = 0 vì cần kết nối với bảng Đơn hàng và Nhập hàng)
        $query = "SELECT id, product_name, sku, barcode, unit, stock as ton_kho, available as co_the_ban, 
                         0 as dang_giao_dich, 0 as dang_ve_kho, base_price, cost_price, image 
                  FROM products WHERE 1=1 ";
        $params = [];

        // 1. Lọc theo từ khóa (Tên, SKU, Barcode)
        if (!empty($filters['search'])) {
            $query .= " AND (product_name LIKE ? OR sku LIKE ? OR barcode LIKE ?) ";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }

        // 2. Lọc theo khoảng Tồn kho (Từ ... Đến ...)
        if (isset($filters['stock_min']) && $filters['stock_min'] !== '') {
            $query .= " AND stock >= ? ";
            $params[] = (int)$filters['stock_min'];
        }
        if (isset($filters['stock_max']) && $filters['stock_max'] !== '') {
            $query .= " AND stock <= ? ";
            $params[] = (int)$filters['stock_max'];
        }

        $query .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
