<?php
// Đường dẫn: app/models/InventoryTransferModel.php
class InventoryTransferModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllTransfers($search = '', $status = '')
    {
        $query = "SELECT t.*, COALESCE(SUM(d.quantity), 0) as total_qty 
                  FROM inventory_transfers t
                  LEFT JOIN inventory_transfer_details d ON t.id = d.transfer_id
                  WHERE 1=1 ";

        $params = [];

        // Xử lý Tìm kiếm theo mã phiếu (Hỗ trợ gõ #TRN1 hoặc chỉ gõ 1)
        if (!empty($search)) {
            $cleanSearch = str_replace(['#TRN', 'TRN', '#'], '', strtoupper(trim($search)));
            $query .= " AND t.id LIKE ? ";
            $params[] = "%$cleanSearch%";
        }

        // Xử lý Lọc theo Trạng thái
        if (!empty($status)) {
            $query .= " AND t.status = ? ";
            $params[] = $status;
        }

        $query .= " GROUP BY t.id ORDER BY t.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransferById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM inventory_transfers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTransferDetails($id)
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, p.product_name, p.sku 
            FROM inventory_transfer_details d
            JOIN products p ON d.product_id = p.id WHERE d.transfer_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 1. Tạo Phiếu Nháp
    public function createTransfer($from, $to, $employee, $note, $products)
    {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO inventory_transfers (from_branch, to_branch, employee, note, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$from, $to, $employee, $note]);
            $transfer_id = $this->conn->lastInsertId();

            $stmtDetail = $this->conn->prepare("INSERT INTO inventory_transfer_details (transfer_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($products as $item) {
                $stmtDetail->execute([$transfer_id, $item['product_id'], $item['quantity']]);
            }
            $this->conn->commit();
            return $transfer_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 2. Xuất kho đi (Đang chuyển): Trừ tồn kho hiện tại, Tăng hàng đang về
    public function startTransfer($id)
    {
        try {
            $this->conn->beginTransaction();
            $details = $this->getTransferDetails($id);
            $stmtStock = $this->conn->prepare("UPDATE products SET stock = stock - ?, available = available - ?, dang_ve_kho = dang_ve_kho + ? WHERE id = ?");

            foreach ($details as $item) {
                $stmtStock->execute([$item['quantity'], $item['quantity'], $item['quantity'], $item['product_id']]);
            }
            $this->conn->prepare("UPDATE inventory_transfers SET status = 'Đang chuyển' WHERE id = ?")->execute([$id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 3. Nhận hàng: Trừ hàng đang về, Cộng vào tồn kho
    public function receiveTransfer($id)
    {
        try {
            $this->conn->beginTransaction();
            $details = $this->getTransferDetails($id);
            $stmtStock = $this->conn->prepare("UPDATE products SET dang_ve_kho = dang_ve_kho - ?, stock = stock + ?, available = available + ? WHERE id = ?");

            foreach ($details as $item) {
                $stmtStock->execute([$item['quantity'], $item['quantity'], $item['quantity'], $item['product_id']]);
            }
            $this->conn->prepare("UPDATE inventory_transfers SET status = 'Đã nhận' WHERE id = ?")->execute([$id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
