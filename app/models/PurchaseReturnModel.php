<?php
// Đường dẫn: app/models/PurchaseReturnModel.php
class PurchaseReturnModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllReturns($search = '', $refund_status = '')
    {
        $query = "SELECT r.*, COALESCE(SUM(d.quantity), 0) as total_qty 
                  FROM purchase_returns r
                  LEFT JOIN purchase_return_details d ON r.id = d.return_id
                  WHERE 1=1 ";

        $params = [];

        // Tìm kiếm theo Mã trả (#RET), Mã nhập (#PN), hoặc Tên NCC
        if (!empty($search)) {
            $cleanSearch = str_replace(['#RET', 'RET', '#PN', 'PN', '#'], '', strtoupper(trim($search)));
            $query .= " AND (r.id LIKE ? OR r.order_id LIKE ? OR r.supplier_name LIKE ?) ";
            $params[] = "%$cleanSearch%";
            $params[] = "%$cleanSearch%";
            $params[] = "%$search%";
        }

        // Lọc theo trạng thái hoàn tiền
        if (!empty($refund_status)) {
            $query .= " AND r.refund_status = ? ";
            $params[] = $refund_status;
        }

        $query .= " GROUP BY r.id ORDER BY r.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xử lý Lưu Phiếu Trả Hàng và TRỪ TỒN KHO
    public function createReturn($order_id, $supplier, $branch, $employee, $reason, $products, $total_amount)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lưu phiếu trả hàng
            $query = "INSERT INTO purchase_returns (order_id, supplier_name, branch, employee, reason, total_amount, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$order_id, $supplier, $branch, $employee, $reason, $total_amount]);
            $return_id = $this->conn->lastInsertId();

            // 2. Lưu chi tiết & Trừ tồn kho
            $queryDetail = "INSERT INTO purchase_return_details (return_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            $updateStock = "UPDATE products SET stock = stock - ?, available = available - ? WHERE id = ?";
            $stmtStock = $this->conn->prepare($updateStock);

            foreach ($products as $item) {
                // Lưu lịch sử hàng trả
                $stmtDetail->execute([$return_id, $item['product_id'], $item['quantity'], $item['price']]);

                // Trừ thẳng số lượng khỏi kho thực tế
                $stmtStock->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
            }

            $this->conn->commit();
            return $return_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
