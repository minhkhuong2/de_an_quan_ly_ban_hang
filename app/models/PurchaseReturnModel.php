<?php
// Đường dẫn file: app/models/PurchaseReturnModel.php
class PurchaseReturnModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllReturns()
    {
        $query = "SELECT pr.*, s.supplier_name 
                  FROM purchase_returns pr 
                  LEFT JOIN suppliers s ON pr.supplier_id = s.id 
                  ORDER BY pr.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReturn($supplier_id, $product_ids, $quantities, $return_prices)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo mã phiếu trả (VD: PRE123456)
            $return_code = 'PRE' . time();

            // Tính tổng tiền hoàn trả
            $total_amount = 0;
            for ($i = 0; $i < count($product_ids); $i++) {
                $total_amount += ($quantities[$i] * $return_prices[$i]);
            }

            // 2. Lưu Phiếu trả hàng
            $queryOrder = "INSERT INTO purchase_returns (return_code, supplier_id, total_return_amount) VALUES (?, ?, ?)";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([$return_code, $supplier_id, $total_amount]);
            $return_id = $this->conn->lastInsertId();

            // 3. Lưu chi tiết & TRỪ Tồn kho
            $queryDetail = "INSERT INTO purchase_return_details (purchase_return_id, product_id, quantity, return_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            // Chú ý: Dùng dấu TRỪ (-) để giảm tồn kho khi trả hàng
            $queryUpdateStock = "UPDATE products SET stock = stock - ?, available = available - ? WHERE id = ?";
            $stmtUpdateStock = $this->conn->prepare($queryUpdateStock);

            for ($i = 0; $i < count($product_ids); $i++) {
                $p_id = $product_ids[$i];
                $qty = $quantities[$i];
                $price = $return_prices[$i];

                if (!empty($p_id) && $qty > 0) {
                    $stmtDetail->execute([$return_id, $p_id, $qty, $price]);
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
