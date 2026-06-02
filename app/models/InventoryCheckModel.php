<?php
// Đường dẫn: app/models/InventoryCheckModel.php
class InventoryCheckModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllChecks($search = '', $status = '')
    {
        $query = "SELECT * FROM inventory_checks WHERE 1=1 ";
        $params = [];

        // Xử lý Tìm kiếm theo mã phiếu (Hỗ trợ gõ #CHK1, CHK1 hoặc chỉ gõ 1)
        if (!empty($search)) {
            $cleanSearch = str_replace(['#CHK', 'CHK', '#'], '', strtoupper(trim($search)));
            $query .= " AND id LIKE ? ";
            $params[] = "%$cleanSearch%";
        }

        // Xử lý Lọc theo Trạng thái
        if (!empty($status)) {
            $query .= " AND status = ? ";
            $params[] = $status;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm tạo phiếu kiểm và CÂN BẰNG KHO
    public function createAndBalance($branch, $employee, $note, $products)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lưu Phiếu kiểm kho
            $query = "INSERT INTO inventory_checks (branch, employee, note, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$branch, $employee, $note]);
            $check_id = $this->conn->lastInsertId();

            // 2. Lưu chi tiết và Cập nhật lại Bảng Products
            $queryDetail = "INSERT INTO inventory_check_details (check_id, product_id, system_stock, actual_stock, difference, reason) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            $updateStock = "UPDATE products SET stock = ?, available = available + ? WHERE id = ?";
            $stmtStock = $this->conn->prepare($updateStock);

            foreach ($products as $item) {
                // Lưu lịch sử
                $stmtDetail->execute([
                    $check_id,
                    $item['product_id'],
                    $item['system_stock'],
                    $item['actual_stock'],
                    $item['difference'],
                    $item['reason']
                ]);

                // Ép Cân bằng kho: Set tồn kho = số thực tế, Có thể bán = Có thể bán + Chênh lệch
                $stmtStock->execute([
                    $item['actual_stock'],
                    $item['difference'],
                    $item['product_id']
                ]);
            }

            $this->conn->commit();
            return $check_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    // Lấy thông tin phiếu kiểm
    public function getCheckById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM inventory_checks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết sản phẩm trong phiếu kiểm
    public function getCheckDetails($id)
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, p.product_name, p.sku 
            FROM inventory_check_details d
            JOIN products p ON d.product_id = p.id
            WHERE d.check_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Sửa thông tin phiếu (Chỉ cho sửa Nhân viên & Ghi chú để bảo vệ kho)
    public function updateCheck($id, $employee, $note)
    {
        $stmt = $this->conn->prepare("UPDATE inventory_checks SET employee = ?, note = ? WHERE id = ?");
        return $stmt->execute([$employee, $note, $id]);
    }

    // Xóa phiếu kiểm và HOÀN LẠI KHO về trạng thái trước khi kiểm
    public function deleteCheck($id)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy chi tiết để biết số lượng chênh lệch cần hoàn lại
            $details = $this->getCheckDetails($id);

            $updateStock = "UPDATE products SET stock = stock - ?, available = available - ? WHERE id = ?";
            $stmtStock = $this->conn->prepare($updateStock);

            // 2. Trừ đi số chênh lệch (Nếu lúc trước cộng thêm thì giờ trừ đi, lúc trước trừ đi thì giờ cộng lại)
            foreach ($details as $item) {
                $stmtStock->execute([$item['difference'], $item['difference'], $item['product_id']]);
            }

            // 3. Xóa dữ liệu trong database
            $this->conn->prepare("DELETE FROM inventory_check_details WHERE check_id = ?")->execute([$id]);
            $this->conn->prepare("DELETE FROM inventory_checks WHERE id = ?")->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
