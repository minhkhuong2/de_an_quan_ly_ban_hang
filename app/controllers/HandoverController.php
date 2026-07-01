<?php

require_once __DIR__ . '/../../config/database.php';

class HandoverController
{
    public function index()
    {
        $db = (new Database())->getConnection();
        
        $stmt = $db->query("
            SELECT hr.*, 
                   b.branch_name as branch_name, 
                   sp.partner_name as partner_name,
                   (SELECT COUNT(*) FROM handover_items hi WHERE hi.handover_id = hr.id) as total_packages
            FROM handover_records hr
            LEFT JOIN branches b ON hr.branch_id = b.id
            LEFT JOIN shipping_partners sp ON hr.shipping_partner_id = sp.id
            ORDER BY hr.created_at DESC
        ");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/handover/list.php';
    }

    public function create()
    {
        $db = (new Database())->getConnection();
        
        // Lấy danh sách chi nhánh và đối tác vận chuyển
        $branches = $db->query("SELECT id, branch_name as name FROM branches WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
        $partners = $db->query("SELECT id, partner_name as name FROM shipping_partners")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/handover/create.php';
    }

    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $branch_id = $data['branch_id'] ?? null;
        $partner_id = $data['partner_id'] ?? null;
        $packages = $data['packages'] ?? [];

        if (empty($branch_id) || empty($partner_id) || empty($packages)) {
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $db = (new Database())->getConnection();
        
        try {
            $db->beginTransaction();

            $record_code = 'HOD' . time(); // Generate Handover ID

            $stmt = $db->prepare("INSERT INTO handover_records (record_code, branch_id, shipping_partner_id, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$record_code, $branch_id, $partner_id]);
            $handover_id = $db->lastInsertId();

            $stmtItem = $db->prepare("INSERT INTO handover_items (handover_id, order_id, package_code, waybill_code) VALUES (?, ?, ?, ?)");
            foreach ($packages as $pkg) {
                // Giả lập lấy order_id từ hệ thống thông qua package/waybill
                $order_id = $pkg['order_id'] ?? 0;
                $stmtItem->execute([$handover_id, $order_id, $pkg['package_code'] ?? null, $pkg['waybill_code'] ?? null]);
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'id' => $handover_id]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("
            SELECT hr.*, 
                   b.branch_name as branch_name, 
                   sp.partner_name as partner_name
            FROM handover_records hr
            LEFT JOIN branches b ON hr.branch_id = b.id
            LEFT JOIN shipping_partners sp ON hr.shipping_partner_id = sp.id
            WHERE hr.id = ?
        ");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            die("Không tìm thấy biên bản bàn giao");
        }

        // Lấy danh sách kiện hàng
        $stmtItems = $db->prepare("
            SELECT hi.*, o.order_code, o.customer_name 
            FROM handover_items hi
            LEFT JOIN orders o ON hi.order_id = o.id
            WHERE hi.handover_id = ?
        ");
        $stmtItems->execute([$id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/handover/detail.php';
    }

    public function delete()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;

        $db = (new Database())->getConnection();
        $db->prepare("DELETE FROM handover_records WHERE id = ? AND status = 'pending'")->execute([$id]);
        echo json_encode(['status' => 'success']);
    }

    public function confirm()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;

        $db = (new Database())->getConnection();
        $db->prepare("UPDATE handover_records SET status = 'completed' WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success']);
    }
}
