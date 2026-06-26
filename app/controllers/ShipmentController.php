<?php
// ÄÆ°á»ng dáº«n: app/controllers/ShipmentController.php
require_once __DIR__ . '/../../config/database.php';

class ShipmentController
{
    // 1. DANH SÃCH Váº¬N ÄÆ N (CÃ“ Bá»˜ Lá»ŒC)
    public function index()
    {
        $db = (new Database())->getConnection();

        // Nháº­n tham sá»‘ Lá»c
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

        // Láº¥y danh sÃ¡ch chi nhÃ¡nh phá»¥c vá»¥ Dropdown Äá»‘i soÃ¡t
        $branches = $db->query("SELECT id, branch_name FROM branches WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/shipping/shipment_list.php';
    }

    // 2. Äá»”I TRáº NG THÃI Váº¬N ÄÆ N HÃ€NG LOáº T
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

    // 3. Äá»I SOÃT Váº¬N CHUYá»‚N HÃ€NG LOáº T (Thu tiá»n COD vá» Káº¿ toÃ¡n)
    public function reconcile_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = explode(',', $_POST['recon_shipment_ids']);
            $branch_id = intval($_POST['recon_branch_id']);
            $note = trim($_POST['recon_note']);
            $recon_code = 'DS' . date('YmdHis');

            $db = (new Database())->getConnection();

            // BÆ°á»›c 3.1: Gom thÃ´ng tin cÃ¡c váº­n Ä‘Æ¡n Ä‘á»ƒ tÃ­nh toÃ¡n
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $db->prepare("SELECT partner_code, SUM(cod_amount) as sum_cod, SUM(shipping_fee) as sum_fee, COUNT(DISTINCT partner_code) as diff_partners FROM shipments WHERE id IN ($placeholders) AND recon_status = 'unreconciled' AND status IN ('delivered', 'returned')");
            $stmt->execute($ids);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Báº«y lá»—i chuáº©n Há»‡ thá»‘ng: KhÃ¡c Ä‘á»‘i tÃ¡c hoáº·c Tráº¡ng thÃ¡i chÆ°a xong
            if ($data['diff_partners'] > 1) {
                die("<script>alert('Lá»—i: Vui lÃ²ng chá»‰ chá»n váº­n Ä‘Æ¡n tá»« CÃ™NG Má»˜T Ä‘Æ¡n vá»‹ váº­n chuyá»ƒn Ä‘á»ƒ Ä‘á»‘i soÃ¡t!'); history.back();</script>");
            }
            if ($data['sum_cod'] === null) {
                die("<script>alert('Lá»—i: KhÃ´ng cÃ³ Ä‘Æ¡n nÃ o há»£p lá»‡! Chá»‰ Ä‘á»‘i soÃ¡t Ä‘Æ¡n ÄÃ£ giao/HoÃ n vÃ  ChÆ°a Ä‘á»‘i soÃ¡t.'); history.back();</script>");
            }

            $total_received = floatval($data['sum_cod']) - floatval($data['sum_fee']);

            try {
                $db->beginTransaction();

                // 1. Táº¡o Phiáº¿u Äá»‘i soÃ¡t
                $stmt_recon = $db->prepare("INSERT INTO shipping_reconciliations (recon_code, partner_code, branch_id, total_cod, total_fee, total_received, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_recon->execute([$recon_code, $data['partner_code'], $branch_id, $data['sum_cod'], $data['sum_fee'], $total_received, $note]);
                $recon_id = $db->lastInsertId();

                // 2. ÄÃ¡nh dáº¥u Váº­n Ä‘Æ¡n Ä‘Ã£ Ä‘á»‘i soÃ¡t
                $stmt_update = $db->prepare("UPDATE shipments SET recon_status = 'reconciled', recon_id = ? WHERE id IN ($placeholders)");
                $params = array_merge([$recon_id], $ids);
                $stmt_update->execute($params);

                // 3. Tá»± Ä‘á»™ng sinh 1 PHIáº¾U THU vÃ o Sá»• quá»¹ Káº¿ toÃ¡n
                $reason = "Thu tiá»n Ä‘á»‘i soÃ¡t COD tá»« " . strtoupper($data['partner_code']) . " (MÃ£: $recon_code)";
                $stmt_fund = $db->prepare("INSERT INTO receipts (receipt_code, branch_id, receipt_reason, payer_name, amount, payment_method) VALUES (?, ?, ?, ?, ?, 'bank')");
                $stmt_fund->execute([$recon_code, $branch_id, $reason, strtoupper($data['partner_code']), $total_received]);

                $db->commit();
                header("Location: index.php?action=shipment_list&success_recon=1");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lá»—i há»‡ thá»‘ng: " . $e->getMessage());
            }
            exit;
        }
    }
}

