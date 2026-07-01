<?php

require_once __DIR__ . '/../../config/database.php';

class DraftOrderController
{
    public function index()
    {
        $db = (new Database())->getConnection();

        // Xử lý bộ lọc
        $conditions = ["o.draft_status IS NOT NULL"];
        $params = [];

        if (!empty($_GET['keyword'])) {
            $conditions[] = "(o.order_code LIKE ? OR c.last_name LIKE ? OR c.phone LIKE ?)";
            $k = '%' . $_GET['keyword'] . '%';
            $params = array_merge($params, [$k, $k, $k]);
        }

        if (!empty($_GET['status'])) {
            $conditions[] = "o.draft_status = ?";
            $params[] = $_GET['status'];
        }

        if (!empty($_GET['created_from']) && !empty($_GET['created_to'])) {
            $conditions[] = "DATE(o.created_at) BETWEEN ? AND ?";
            $params[] = $_GET['created_from'];
            $params[] = $_GET['created_to'];
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

        // Truy vấn danh sách đơn hàng nháp
        $query = "
            SELECT o.*, 
                   CONCAT(c.last_name, ' ', c.first_name) AS customer_name,
                   c.phone as customer_phone
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            $whereClause
            ORDER BY o.created_at DESC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $draft_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm trạng thái
        $status_counts = [
            'all' => count($draft_orders),
            'open' => 0,
            'completed' => 0
        ];
        foreach ($draft_orders as $o) {
            if ($o['draft_status'] === 'open') $status_counts['open']++;
            if ($o['draft_status'] === 'completed') $status_counts['completed']++;
        }

        require_once __DIR__ . '/../views/order/draft_list.php';
    }

    public function copy()
    {
        $id = $_GET['id'] ?? 0;
        // Chuyển hướng sang màn edit nhưng mang theo flag copy_1
        header("Location: index.php?action=edit_order&id=$id&copy=1");
        exit;
    }

    public function update_tags()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $ids = $data['ids'] ?? [];
        $action = $data['tag_action'] ?? ''; // 'add' or 'remove'
        $tag = $data['tag'] ?? '';

        if (empty($ids) || empty($tag)) {
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $db = (new Database())->getConnection();
        
        try {
            $db->beginTransaction();
            foreach ($ids as $id) {
                $stmt = $db->prepare("SELECT tags FROM orders WHERE id = ?");
                $stmt->execute([$id]);
                $current_tags = $stmt->fetchColumn();
                $tagArray = $current_tags ? explode(',', $current_tags) : [];
                $tagArray = array_map('trim', $tagArray);

                if ($action === 'add') {
                    if (!in_array($tag, $tagArray)) {
                        $tagArray[] = $tag;
                    }
                } else if ($action === 'remove') {
                    $tagArray = array_filter($tagArray, function($t) use ($tag) {
                        return $t !== $tag;
                    });
                }

                $new_tags = implode(', ', array_filter($tagArray));
                $stmt_upd = $db->prepare("UPDATE orders SET tags = ? WHERE id = ?");
                $stmt_upd->execute([$new_tags, $id]);
            }
            $db->commit();
            echo json_encode(['status' => 'success', 'msg' => 'Đã cập nhật tags thành công']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function delete_bulk()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'msg' => 'Chưa chọn đơn hàng nào']);
            return;
        }

        $db = (new Database())->getConnection();
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        try {
            $db->beginTransaction();
            // Chỉ cho xóa đơn hàng nháp đang mở
            $stmt = $db->prepare("DELETE FROM orders WHERE id IN ($placeholders) AND draft_status = 'open'");
            $stmt->execute($ids);
            
            // Note: Xóa đơn hàng sẽ trigger cascade delete order_items nếu đã cấu hình, 
            // nếu chưa cấu hình thì cần xóa thủ công order_items
            $stmt_items = $db->prepare("DELETE FROM order_items WHERE order_id IN ($placeholders)");
            $stmt_items->execute($ids);

            $db->commit();
            echo json_encode(['status' => 'success', 'msg' => 'Đã xóa thành công các đơn hàng nháp được chọn.']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
}
