<?php

class ShipmentController
{
    public function list()
    {
        $db = (new Database())->getConnection();

        $active_tab = $_GET['tab'] ?? 'all';
        $keyword = trim($_GET['keyword'] ?? '');
        $status_filter = $_GET['status'] ?? [];
        $branch_filter = $_GET['branch'] ?? [];
        $recon_filter = $_GET['recon'] ?? [];

        $query = "SELECT o.id, o.order_code, o.waybill_code, o.shipping_partner_name, o.shipping_status, 
                         o.reconciliation_status, o.cod_partner, o.shipping_fee_partner, 
                         o.created_at, o.delivery_date, b.branch_name, o.grand_total,
                         CONCAT(c.last_name, ' ', c.first_name) AS customer_name, c.phone
                  FROM orders o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  LEFT JOIN branches b ON o.branch_id = b.id
                  WHERE o.waybill_code IS NOT NULL AND o.waybill_code != ''";

        $params = [];

        // Tab lọc nhanh
        if ($active_tab == 'shipping') {
            $query .= " AND o.shipping_status = 'shipping'";
        } elseif ($active_tab == 'rescheduling') {
            $query .= " AND o.shipping_status = 'rescheduling'";
        } elseif ($active_tab == 'returning') {
            $query .= " AND o.shipping_status = 'returning'";
        }

        // Tìm kiếm từ khóa
        if ($keyword !== '') {
            $query .= " AND (o.order_code LIKE ? OR o.waybill_code LIKE ? OR c.phone LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        // Lọc trạng thái
        if (!empty($status_filter)) {
            $in = implode(',', array_fill(0, count($status_filter), '?'));
            $query .= " AND o.shipping_status IN ($in)";
            $params = array_merge($params, $status_filter);
        }

        // Lọc chi nhánh
        if (!empty($branch_filter)) {
            $in = implode(',', array_fill(0, count($branch_filter), '?'));
            $query .= " AND b.branch_name IN ($in)";
            $params = array_merge($params, $branch_filter);
        }

        // Lọc trạng thái đối soát
        if (!empty($recon_filter)) {
            $in = implode(',', array_fill(0, count($recon_filter), '?'));
            $query .= " AND o.reconciliation_status IN ($in)";
            $params = array_merge($params, $recon_filter);
        }

        $query .= " ORDER BY o.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách chi nhánh để hiển thị ở bộ lọc
        $stmt_branches = $db->query("SELECT branch_name FROM branches");
        $branches = $stmt_branches ? $stmt_branches->fetchAll(PDO::FETCH_ASSOC) : [];

        require_once __DIR__ . '/../views/shipment/list.php';
    }

    public function change_status()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];
        $new_status = $data['status'] ?? '';

        if (empty($ids) || !$new_status) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $in = implode(',', array_fill(0, count($ids), '?'));
            
            $sql = "UPDATE orders SET shipping_status = ? WHERE id IN ($in)";
            $params = array_merge([$new_status], $ids);
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function reconcile()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];
        $note = $data['note'] ?? '';

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Chưa chọn vận đơn nào']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $in = implode(',', array_fill(0, count($ids), '?'));
            
            // Validate: Cùng đối tác, Trạng thái (Đã giao/Đã hoàn), Chưa đối soát
            $stmt = $db->prepare("SELECT id, shipping_partner_name, shipping_status, reconciliation_status FROM orders WHERE id IN ($in)");
            $stmt->execute($ids);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $partner = null;
            foreach ($orders as $o) {
                if ($o['reconciliation_status'] != 'Chưa đối soát') {
                    echo json_encode(['success' => false, 'message' => 'Tồn tại đơn hàng đã được đối soát. Vui lòng kiểm tra lại.']);
                    return;
                }
                if (!in_array($o['shipping_status'], ['delivered', 'returned'])) {
                    echo json_encode(['success' => false, 'message' => 'Đơn hàng không hợp lệ. Chỉ đối soát các đơn Đã giao hàng hoặc Đã hoàn hàng.']);
                    return;
                }
                if ($partner === null) {
                    $partner = $o['shipping_partner_name'];
                } elseif ($partner !== $o['shipping_partner_name']) {
                    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn vận đơn từ CÙNG MỘT đơn vị vận chuyển để đối soát.']);
                    return;
                }
            }

            // Thực hiện đối soát
            $sql = "UPDATE orders SET reconciliation_status = 'Đã đối soát' WHERE id IN ($in)";
            $stmt = $db->prepare($sql);
            $stmt->execute($ids);

            echo json_encode(['success' => true, 'message' => 'Tạo phiếu đối soát thành công!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function cancel_shipment()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $order_id = $data['order_id'] ?? 0;

        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $sql = "UPDATE orders SET shipping_status = 'cancelled' WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$order_id]);

            echo json_encode(['success' => true, 'message' => 'Hủy giao hàng thành công!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function print_shipping()
    {
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        if (empty($ids)) die("Không có dữ liệu");
        
        $db = (new Database())->getConnection();
        $in = implode(',', array_fill(0, count($ids), '?'));
        
        $stmt = $db->prepare("SELECT o.*, CONCAT(c.last_name, ' ', c.first_name) AS customer_name, c.phone, c.address 
                              FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id IN ($in)");
        $stmt->execute($ids);
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/shipment/print_shipping.php';
    }

    public function print_handover()
    {
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        if (empty($ids)) die("Không có dữ liệu");
        
        $db = (new Database())->getConnection();
        $in = implode(',', array_fill(0, count($ids), '?'));
        
        $stmt = $db->prepare("SELECT o.*, CONCAT(c.last_name, ' ', c.first_name) AS customer_name, c.phone, c.address 
                              FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id IN ($in)");
        $stmt->execute($ids);
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/shipment/print_handover.php';
    }
}
