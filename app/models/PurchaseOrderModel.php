<?php
// Đường dẫn file: app/models/PurchaseOrderModel.php
class PurchaseOrderModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách phiếu nhập
    public function getAllOrders()
    {
        $query = "SELECT po.*, s.supplier_name 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.id 
                  ORDER BY po.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo phiếu nhập mới & Cập nhật Tồn kho (Dùng Transaction để đảm bảo an toàn dữ liệu)
    public function createOrder($supplier_id, $product_ids, $quantities, $import_prices)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo mã phiếu nhập ngẫu nhiên (VD: PON123456)
            $order_code = 'PON' . time();

            // Tính tổng tiền
            $total_amount = 0;
            for ($i = 0; $i < count($product_ids); $i++) {
                $total_amount += ($quantities[$i] * $import_prices[$i]);
            }

            // 2. Lưu vào bảng purchase_orders
            $queryOrder = "INSERT INTO purchase_orders (order_code, supplier_id, total_amount) VALUES (?, ?, ?)";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([$order_code, $supplier_id, $total_amount]);
            $order_id = $this->conn->lastInsertId();

            // 3. Lưu chi tiết & Cập nhật Tồn kho
            $queryDetail = "INSERT INTO purchase_order_details (purchase_order_id, product_id, quantity, import_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            $queryUpdateStock = "UPDATE products SET stock = stock + ?, available = available + ? WHERE id = ?";
            $stmtUpdateStock = $this->conn->prepare($queryUpdateStock);

            for ($i = 0; $i < count($product_ids); $i++) {
                $p_id = $product_ids[$i];
                $qty = $quantities[$i];
                $price = $import_prices[$i];

                if (!empty($p_id) && $qty > 0) {
                    // Lưu chi tiết
                    $stmtDetail->execute([$order_id, $p_id, $qty, $price]);
                    // CỘNG TỒN KHO THỰC TẾ
                    $stmtUpdateStock->execute([$qty, $qty, $p_id]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
