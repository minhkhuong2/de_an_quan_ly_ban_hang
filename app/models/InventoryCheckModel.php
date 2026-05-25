<?php
// Đường dẫn file: app/models/InventoryCheckModel.php
class InventoryCheckModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllChecks()
    {
        $query = "SELECT * FROM inventory_checks ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCheck($product_ids, $system_stocks, $actual_stocks, $discrepancies)
    {
        try {
            $this->conn->beginTransaction();

            $check_code = 'PKK' . time(); // Mã phiếu kiểm kho

            $total_actual = 0;
            $total_discrepancy = 0;
            for ($i = 0; $i < count($product_ids); $i++) {
                $total_actual += $actual_stocks[$i];
                $total_discrepancy += $discrepancies[$i];
            }

            // 1. Lưu Phiếu kiểm kho
            $queryOrder = "INSERT INTO inventory_checks (check_code, total_actual, total_discrepancy) VALUES (?, ?, ?)";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([$check_code, $total_actual, $total_discrepancy]);
            $check_id = $this->conn->lastInsertId();

            // 2. Lưu Chi tiết & Cân bằng Tồn kho
            $queryDetail = "INSERT INTO inventory_check_details (check_id, product_id, system_stock, actual_stock, discrepancy) VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            // Cập nhật lại tồn kho: Lấy đúng số thực tế, Có thể bán = Có thể bán + Chênh lệch
            $queryUpdateStock = "UPDATE products SET stock = ?, available = available + ? WHERE id = ?";
            $stmtUpdateStock = $this->conn->prepare($queryUpdateStock);

            for ($i = 0; $i < count($product_ids); $i++) {
                $p_id = $product_ids[$i];
                $sys_qty = $system_stocks[$i];
                $act_qty = $actual_stocks[$i];
                $diff = $discrepancies[$i];

                if (!empty($p_id)) {
                    $stmtDetail->execute([$check_id, $p_id, $sys_qty, $act_qty, $diff]);
                    $stmtUpdateStock->execute([$act_qty, $diff, $p_id]);
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
