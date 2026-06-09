<?php
// Đường dẫn: app/models/PromotionModel.php
class PromotionModel
{
    private $conn;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Nâng cấp: Hỗ trợ Tìm kiếm và Lọc
    public function getAllPromotions($search = '', $status = '', $type = '')
    {
        $query = "SELECT * FROM promotions WHERE 1=1 ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (promo_name LIKE ? OR promo_code LIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($status)) {
            $query .= " AND status = ? ";
            $params[] = $status;
        }
        if (!empty($type)) {
            $query .= " AND promo_type = ? ";
            $params[] = $type;
        }

        $query .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPromotion($name, $code, $type, $d_type, $d_value, $min_order, $start, $end, $status)
    {
        $stmt = $this->conn->prepare("INSERT INTO promotions (promo_name, promo_code, promo_type, discount_type, discount_value, min_order_value, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $code ?: null, $type, $d_type, $d_value, $min_order, $start, $end, $status]);
    }

    // MỚI: Thao tác hàng loạt (Cập nhật trạng thái hoặc Xóa)
    public function bulkAction($ids, $action)
    {
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        if ($action === 'delete') {
            $query = "DELETE FROM promotions WHERE id IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($ids);
        } else {
            // Các action: Đang chạy (Kích hoạt), Tạm dừng, Kết thúc
            $query = "UPDATE promotions SET status = ? WHERE id IN ($placeholders)";
            array_unshift($ids, $action); // Đẩy giá trị status vào đầu mảng params
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($ids);
        }
    }
    // Lấy chi tiết 1 chương trình
    public function getPromotionById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM promotions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Đơn hàng đã áp dụng Khuyến mại này (Bảng kết quả)
    public function getAppliedOrders($promo_code)
    {
        // Lưu ý: Giả định bạn có bảng 'orders'. Nếu bảng tên khác, bạn sửa lại tên bảng nhé.
        // Giả sử bảng orders có lưu cột 'applied_promo_code' và 'discount_amount'
        try {
            $stmt = $this->conn->prepare("SELECT order_code, created_at, employee_name, customer_name, total_amount, discount_amount FROM orders WHERE applied_promo_code = ? ORDER BY created_at DESC");
            $stmt->execute([$promo_code]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return []; // Trả về mảng rỗng nếu bảng orders chưa hoàn thiện phần khuyến mại
        }
    }
}
