<?php

class IncompleteOrderController
{
    public function index()
    {
        $db = (new Database())->getConnection();

        $keyword = trim($_GET['keyword'] ?? '');
        $status = $_GET['status'] ?? 'open'; // 'open' or 'archived'
        $email_status = $_GET['email_status'] ?? 'all';
        $from_date = $_GET['from_date'] ?? '';

        $sql = "SELECT id, order_code, customer_name, phone, grand_total, created_at, email_status, is_archived 
                FROM orders 
                WHERE order_status = 'incomplete'";
        $params = [];

        if ($status === 'archived') {
            $sql .= " AND is_archived = 1";
        } else {
            $sql .= " AND is_archived = 0";
        }

        if ($keyword !== '') {
            $sql .= " AND (order_code LIKE ? OR customer_name LIKE ? OR phone LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        if ($email_status !== 'all') {
            $sql .= " AND email_status = ?";
            $params[] = $email_status;
        }

        if ($from_date !== '') {
            $sql .= " AND created_at >= ?";
            $params[] = $from_date . ' 00:00:00';
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/incomplete_order/list.php';
    }

    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) die("Missing order ID");

        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND order_status = 'incomplete'");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) die("Không tìm thấy đơn chưa hoàn tất");

        $stmt_items = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/incomplete_order/detail.php';
    }

    public function send_email()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $order_id = $data['order_id'] ?? 0;
        $message = $data['message'] ?? '';

        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE orders SET email_status = 'sent', email_sent_at = NOW() WHERE id = ?");
            $stmt->execute([$order_id]);

            echo json_encode(['success' => true, 'message' => 'Đã gửi email nhắc nhở thành công!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function bulk_action()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $action = $data['action'] ?? '';
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Không có đơn hàng nào được chọn']);
            return;
        }

        $in_placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        try {
            $db = (new Database())->getConnection();
            
            if ($action === 'archive') {
                $sql = "UPDATE orders SET is_archived = 1 WHERE id IN ($in_placeholders) AND order_status = 'incomplete'";
                $db->prepare($sql)->execute($ids);
            } elseif ($action === 'unarchive') {
                $sql = "UPDATE orders SET is_archived = 0 WHERE id IN ($in_placeholders) AND order_status = 'incomplete'";
                $db->prepare($sql)->execute($ids);
            } elseif ($action === 'delete') {
                // Delete items first
                $sql_items = "DELETE FROM order_items WHERE order_id IN ($in_placeholders)";
                $db->prepare($sql_items)->execute($ids);
                
                $sql = "DELETE FROM orders WHERE id IN ($in_placeholders) AND order_status = 'incomplete'";
                $db->prepare($sql)->execute($ids);
            } else {
                throw new Exception("Thao tác không hợp lệ");
            }

            echo json_encode(['success' => true, 'message' => 'Thao tác thành công']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $db = (new Database())->getConnection();

        $keyword = trim($_GET['keyword'] ?? '');
        $status = $_GET['status'] ?? 'open';
        $email_status = $_GET['email_status'] ?? 'all';

        $sql = "SELECT order_code, customer_name, phone, grand_total, created_at, email_status, is_archived 
                FROM orders 
                WHERE order_status = 'incomplete'";
        $params = [];

        if ($status === 'archived') {
            $sql .= " AND is_archived = 1";
        } else {
            $sql .= " AND is_archived = 0";
        }

        if ($keyword !== '') {
            $sql .= " AND (order_code LIKE ? OR customer_name LIKE ? OR phone LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        if ($email_status !== 'all') {
            $sql .= " AND email_status = ?";
            $params[] = $email_status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=don_chua_hoan_tat_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel utf-8
        fputs($output, "\xEF\xBB\xBF");
        
        fputcsv($output, ['Mã đơn hàng', 'Khách hàng', 'Số điện thoại', 'Thành tiền', 'Trạng thái Email', 'Ngày tạo', 'Lưu trữ']);

        foreach ($orders as $o) {
            $email_st = $o['email_status'] == 'sent' ? 'Đã gửi' : ($o['email_status'] == 'scheduled' ? 'Hẹn giờ' : 'Chưa gửi');
            fputcsv($output, [
                $o['order_code'],
                $o['customer_name'],
                $o['phone'],
                $o['grand_total'],
                $email_st,
                $o['created_at'],
                $o['is_archived'] ? 'Có' : 'Không'
            ]);
        }
        fclose($output);
        exit;
    }
}
