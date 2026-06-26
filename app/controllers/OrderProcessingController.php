<?php
// Đường dẫn: app/controllers/OrderProcessingController.php
require_once __DIR__ . '/../../config/database.php';

class OrderProcessingController
{
    public function index()
    {
        $db = (new Database())->getConnection();

        // 1. Xác định Tab đang active (Mặc định là 'pending_process' - Chờ xử lý)
        $tab = $_GET['tab'] ?? 'pending_process';

        // 2. Nhận các tham số lọc
        $keyword = trim($_GET['keyword'] ?? '');
        $channel_filter = $_GET['channel'] ?? 'all'; // 'all', 'shopee', 'lazada', 'tiktok', 'pos', 'web'

        // 3. Xây dựng câu truy vấn cơ bản
        $query = "SELECT o.*, 
                         CONCAT(c.last_name, ' ', c.first_name) AS customer_name,
                         c.phone AS customer_phone
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  WHERE 1=1";

        // Áp dụng bộ lọc Từ khóa
        if ($keyword !== '') {
            $query .= " AND (o.order_code LIKE '%$keyword%' OR c.phone LIKE '%$keyword%' OR CONCAT(c.last_name, ' ', c.first_name) LIKE '%$keyword%')";
        }

        // Áp dụng bộ lọc Kênh bán (Sàn vs Ngoài sàn)
        if ($channel_filter !== 'all') {
            if ($channel_filter === 'ecommerce') {
                $query .= " AND o.sales_channel IN ('shopee', 'lazada', 'tiktok')";
            } elseif ($channel_filter === 'other') {
                $query .= " AND o.sales_channel NOT IN ('shopee', 'lazada', 'tiktok')";
            } else {
                $query .= " AND o.sales_channel = '$channel_filter'";
            }
        }

        // 4. Lọc theo từng Tab (Trạng thái)
        switch ($tab) {
            case 'pending_confirm':
                // Chờ xác nhận
                $query .= " AND o.order_status = 'pending'";
                break;
            case 'pending_process':
                // Chờ xử lý (Đã xác nhận nhưng chưa đóng gói)
                $query .= " AND o.order_status = 'confirmed' AND o.shipping_status = 'pending'";
                break;
            case 'packing':
                // In & đóng gói
                $query .= " AND o.shipping_status = 'packing'";
                break;
            case 'handover':
                // Bàn giao kiện hàng
                $query .= " AND o.shipping_status = 'packed'";
                break;
            case 'all':
            default:
                // Tất cả
                break;
        }

        $query .= " ORDER BY o.created_at DESC";

        $stmt = $db->query($query);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm số lượng cho các tab
        $counts = [
            'pending_confirm' => 0,
            'pending_process' => 0,
            'packing' => 0,
            'handover' => 0,
            'all' => 0
        ];

        // Lấy count tổng quan (có thể tối ưu bằng câu query GROUP BY nếu nhiều đơn)
        $count_query = "SELECT order_status, shipping_status, COUNT(*) as count FROM orders GROUP BY order_status, shipping_status";
        $stmt_count = $db->query($count_query);
        $count_results = $stmt_count->fetchAll(PDO::FETCH_ASSOC);

        foreach ($count_results as $row) {
            $counts['all'] += $row['count'];
            if ($row['order_status'] === 'pending') {
                $counts['pending_confirm'] += $row['count'];
            } elseif ($row['order_status'] === 'confirmed' && $row['shipping_status'] === 'pending') {
                $counts['pending_process'] += $row['count'];
            } elseif ($row['shipping_status'] === 'packing') {
                $counts['packing'] += $row['count'];
            } elseif ($row['shipping_status'] === 'packed') {
                $counts['handover'] += $row['count'];
            }
        }

        require_once __DIR__ . '/../views/order_processing/index.php';
    }

    // Xác nhận đơn hàng (Từ Chờ xác nhận -> Chờ xử lý)
    public function confirm_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_ids'])) {
            $ids = explode(',', $_POST['order_ids']);
            $db = (new Database())->getConnection();
            
            // Xóa phần tử rỗng
            $ids = array_filter($ids);
            if (empty($ids)) {
                header("Location: index.php?action=order_processing&tab=pending_confirm");
                exit;
            }

            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE orders SET order_status = 'confirmed' WHERE id IN ($inQuery) AND order_status = 'pending'");
            $stmt->execute($ids);

            header("Location: index.php?action=order_processing&tab=pending_confirm&success=Xác nhận " . $stmt->rowCount() . " đơn hàng thành công");
            exit;
        }
    }

    // Yêu cầu đóng gói (Từ Chờ xử lý -> In & đóng gói)
    public function request_pack_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_ids'])) {
            $ids = explode(',', $_POST['order_ids']);
            $db = (new Database())->getConnection();
            
            $ids = array_filter($ids);
            if (empty($ids)) {
                header("Location: index.php?action=order_processing&tab=pending_process");
                exit;
            }

            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE orders SET shipping_status = 'packing' WHERE id IN ($inQuery) AND order_status = 'confirmed' AND shipping_status = 'pending'");
            $stmt->execute($ids);

            header("Location: index.php?action=order_processing&tab=pending_process&success=Đã yêu cầu đóng gói " . $stmt->rowCount() . " đơn hàng");
            exit;
        }
    }

    // Đánh dấu đã đóng gói (Từ In & đóng gói -> Bàn giao)
    public function mark_packed_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_ids'])) {
            $ids = explode(',', $_POST['order_ids']);
            $db = (new Database())->getConnection();
            
            $ids = array_filter($ids);
            if (empty($ids)) {
                header("Location: index.php?action=order_processing&tab=packing");
                exit;
            }

            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE orders SET shipping_status = 'packed' WHERE id IN ($inQuery) AND shipping_status = 'packing'");
            $stmt->execute($ids);

            header("Location: index.php?action=order_processing&tab=packing&success=Xác nhận đóng gói " . $stmt->rowCount() . " kiện hàng");
            exit;
        }
    }

    // Bàn giao (Tạo vận đơn / Giao cho ĐVVC)
    public function handover_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_ids'])) {
            $ids = explode(',', $_POST['order_ids']);
            $db = (new Database())->getConnection();
            
            $ids = array_filter($ids);
            if (empty($ids)) {
                header("Location: index.php?action=order_processing&tab=handover");
                exit;
            }

            try {
                $db->beginTransaction();

                $inQuery = implode(',', array_fill(0, count($ids), '?'));
                // Cập nhật orders
                $stmt = $db->prepare("UPDATE orders SET shipping_status = 'delivering', order_status = 'processing' WHERE id IN ($inQuery) AND shipping_status = 'packed'");
                $stmt->execute($ids);
                $rowCount = $stmt->rowCount();

                // Sinh vận đơn tự động nếu chưa có trong bảng shipments
                $stmt_check = $db->prepare("SELECT id, grand_total, amount_paid FROM orders WHERE id IN ($inQuery) AND shipping_status = 'delivering'");
                $stmt_check->execute($ids);
                $orders = $stmt_check->fetchAll(PDO::FETCH_ASSOC);

                $stmt_ship = $db->prepare("INSERT IGNORE INTO shipments (order_id, tracking_code, partner_code, status, cod_amount) VALUES (?, ?, 'ghn', 'delivering', ?)");
                foreach ($orders as $order) {
                    $cod = max(0, floatval($order['grand_total']) - floatval($order['amount_paid']));
                    $tracking = 'GHN' . date('ymd') . rand(1000, 9999) . $order['id'];
                    $stmt_ship->execute([$order['id'], $tracking, $cod]);
                }

                $db->commit();
                header("Location: index.php?action=order_processing&tab=handover&success=Bàn giao thành công " . $rowCount . " kiện hàng cho ĐVVC");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi bàn giao: " . $e->getMessage());
            }
            exit;
        }
    }

    // In phiếu (Giao hàng / Nhặt hàng)
    public function print_docs()
    {
        $ids = $_GET['ids'] ?? '';
        $type = $_GET['type'] ?? 'shipping'; // shipping hoặc picking
        
        if (empty($ids)) {
            die("Vui lòng chọn ít nhất 1 đơn hàng để in.");
        }

        $id_array = explode(',', $ids);
        $id_array = array_filter($id_array);
        
        $db = (new Database())->getConnection();
        $inQuery = implode(',', array_fill(0, count($id_array), '?'));
        
        // Lấy thông tin đơn hàng
        $query = "SELECT o.*, 
                         CONCAT(c.last_name, ' ', c.first_name) AS customer_name,
                         c.phone AS customer_phone,
                         c.address AS customer_address
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  WHERE o.id IN ($inQuery)";
        $stmt = $db->prepare($query);
        $stmt->execute($id_array);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách sản phẩm trong đơn (giả lập hoặc từ bảng order_items nếu có)
        // Vì hệ thống hiện tại có thể lưu sản phẩm ở dạng khác, tôi sẽ query bảng order_items
        $stmt_items = $db->prepare("SELECT * FROM order_items WHERE order_id IN ($inQuery)");
        $stmt_items->execute($id_array);
        $all_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        
        // Nhóm item theo order_id
        $items_by_order = [];
        foreach ($all_items as $item) {
            $items_by_order[$item['order_id']][] = $item;
        }

        // Lấy thông tin cửa hàng
        $stmt_store = $db->query("SELECT * FROM settings LIMIT 1");
        $store = $stmt_store->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/order_processing/print.php';
    }
}
