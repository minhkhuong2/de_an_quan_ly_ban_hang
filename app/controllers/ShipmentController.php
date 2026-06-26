<?php
// Đường dẫn: app/controllers/ShipmentController.php
require_once __DIR__ . '/../../config/database.php';

class ShipmentController
{
    // 1. DANH SÁCH VẬN ĐƠN (CÓ BỘ LỌC)
    public function index()
    {
        $db = (new Database())->getConnection();

        // Nhận tham số Lọc
        $status_filter = $_GET['status'] ?? 'all';
        $recon_filter = $_GET['recon_status'] ?? 'all';
        $keyword = trim($_GET['keyword'] ?? '');

        $query = "
            SELECT s.*, o.order_code, o.customer_name, o.phone, b.branch_name 
            FROM shipments s
            JOIN orders o ON s.order_id = o.id
            JOIN branches b ON s.branch_id = b.id
            WHERE 1=1
        ";

        if ($status_filter !== 'all') $query .= " AND s.status = '$status_filter'";
        if ($recon_filter !== 'all') $query .= " AND s.recon_status = '$recon_filter'";
        if ($keyword !== '') {
            $query .= " AND (s.tracking_code LIKE '%$keyword%' OR o.order_code LIKE '%$keyword%' OR o.customer_name LIKE '%$keyword%')";
        }

        $query .= " ORDER BY s.created_at DESC";
        $stmt = $db->query($query);
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách chi nhánh phục vụ Dropdown Đối soát
        $branches = $db->query("SELECT id, branch_name FROM branches WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/shipping/shipment_list.php';
    }

    // 2. ĐỔI TRẠNG THÁI VẬN ĐƠN HÀNG LOẠT
    public function update_status_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = explode(',', $_POST['shipment_ids']);
            $new_status = $_POST['new_status'];

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE shipments SET status = ? WHERE id = ?");
            foreach ($ids as $id) {
                if (intval($id) > 0) $stmt->execute([$new_status, intval($id)]);
            }

            header("Location: index.php?action=shipment_list&success_status=1");
            exit;
        }
    }

    // 3. ĐỐI SOÁT VẬN CHUYỂN HÀNG LOẠT (Thu tiền COD về Kế toán)
    public function reconcile_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = explode(',', $_POST['recon_shipment_ids']);
            $branch_id = intval($_POST['recon_branch_id']);
            $note = trim($_POST['recon_note']);
            $recon_code = 'DS' . date('YmdHis');

            $db = (new Database())->getConnection();

            // Bước 3.1: Gom thông tin các vận đơn để tính toán
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $db->prepare("SELECT partner_code, SUM(cod_amount) as sum_cod, SUM(shipping_fee) as sum_fee, COUNT(DISTINCT partner_code) as diff_partners FROM shipments WHERE id IN ($placeholders) AND recon_status = 'unreconciled' AND status IN ('delivered', 'returned')");
            $stmt->execute($ids);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Bẫy lỗi chuẩn Hệ thống: Khác đối tác hoặc Trạng thái chưa xong
            if ($data['diff_partners'] > 1) {
                die("<script>alert('Lỗi: Vui lòng chỉ chọn vận đơn từ CÙNG MỘT đơn vị vận chuyển để đối soát!'); history.back();</script>");
            }
            if ($data['sum_cod'] === null) {
                die("<script>alert('Lỗi: Không có đơn nào hợp lệ! Chỉ đối soát đơn Đã giao/Hoàn và Chưa đối soát.'); history.back();</script>");
            }

            $total_received = floatval($data['sum_cod']) - floatval($data['sum_fee']);

            try {
                $db->beginTransaction();

                // 1. Tạo Phiếu Đối soát
                $stmt_recon = $db->prepare("INSERT INTO shipping_reconciliations (recon_code, partner_code, branch_id, total_cod, total_fee, total_received, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_recon->execute([$recon_code, $data['partner_code'], $branch_id, $data['sum_cod'], $data['sum_fee'], $total_received, $note]);
                $recon_id = $db->lastInsertId();

                // 2. Đánh dấu Vận đơn đã đối soát
                $stmt_update = $db->prepare("UPDATE shipments SET recon_status = 'reconciled', recon_id = ? WHERE id IN ($placeholders)");
                $params = array_merge([$recon_id], $ids);
                $stmt_update->execute($params);

                // 3. Tự động sinh 1 PHIẾU THU vào Sổ quỹ Kế toán
                $reason = "Thu tiền đối soát COD từ " . strtoupper($data['partner_code']) . " (Mã: $recon_code)";
                $stmt_fund = $db->prepare("INSERT INTO receipts (receipt_code, branch_id, receipt_reason, payer_name, amount, payment_method) VALUES (?, ?, ?, ?, ?, 'bank')");
                $stmt_fund->execute([$recon_code, $branch_id, $reason, strtoupper($data['partner_code']), $total_received]);

                $db->commit();
                header("Location: index.php?action=shipment_list&success_recon=1");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi hệ thống: " . $e->getMessage());
            }
            exit;
        }
    }
}
