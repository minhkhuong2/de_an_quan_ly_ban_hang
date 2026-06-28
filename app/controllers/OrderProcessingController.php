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
                         c.phone AS customer_phone,
                         u.full_name AS packer_name
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  LEFT JOIN users u ON o.packer_id = u.id
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

        // Sắp xếp kiện hàng nâng cao
        if (isset($_GET['sort']) && in_array($_GET['sort'], ['package_asc', 'package_desc'])) {
            $stmt_items_count = $db->query("SELECT order_id, COUNT(DISTINCT product_id) as sku_count, SUM(qty) as total_qty FROM order_items GROUP BY order_id");
            $item_counts = [];
            while($row = $stmt_items_count->fetch(PDO::FETCH_ASSOC)) {
                $item_counts[$row['order_id']] = $row;
            }
            $sort_type = $_GET['sort'];
            usort($orders, function($a, $b) use ($item_counts, $sort_type) {
                $ac = $item_counts[$a['id']] ?? ['sku_count' => 0, 'total_qty' => 0];
                $bc = $item_counts[$b['id']] ?? ['sku_count' => 0, 'total_qty' => 0];
                
                if ($sort_type == 'package_asc') {
                    // Từ A đến Z: 1 SKU trước, qty tăng dần
                    if ($ac['sku_count'] == 1 && $bc['sku_count'] > 1) return -1;
                    if ($ac['sku_count'] > 1 && $bc['sku_count'] == 1) return 1;
                    return $ac['total_qty'] <=> $bc['total_qty'];
                } else {
                    // Từ Z đến A: Nhiều SKU trước, sau đó 1 SKU (qty giảm dần)
                    if ($ac['sku_count'] > 1 && $bc['sku_count'] == 1) return -1;
                    if ($ac['sku_count'] == 1 && $bc['sku_count'] > 1) return 1;
                    return $bc['total_qty'] <=> $ac['total_qty'];
                }
            });
        }

        // Lấy danh sách nhân viên để gán người đóng gói
        $stmt_users = $db->query("SELECT id, username, full_name FROM users");
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

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
        $type = $_GET['type'] ?? 'delivery'; // delivery, picking_order, picking_product
        
        if (empty($ids)) {
            die("Vui lòng chọn ít nhất 1 đơn hàng để in.");
        }

        $id_array = explode(',', $ids);
        $id_array = array_filter($id_array);
        
        $db = (new Database())->getConnection();
        $inQuery = implode(',', array_fill(0, count($id_array), '?'));
        
        // Đánh dấu đã in tự động
        if ($type === 'delivery') {
            $stmt_mark = $db->prepare("UPDATE orders SET printed_delivery = 1 WHERE id IN ($inQuery)");
            $stmt_mark->execute($id_array);
        } elseif ($type === 'picking_order') {
            $stmt_mark = $db->prepare("UPDATE orders SET printed_picking_order = 1 WHERE id IN ($inQuery)");
            $stmt_mark->execute($id_array);
        } elseif ($type === 'picking_product') {
            $stmt_mark = $db->prepare("UPDATE orders SET printed_picking_product = 1 WHERE id IN ($inQuery)");
            $stmt_mark->execute($id_array);
        }
        
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

    // Xử lý các thao tác nâng cao (Đánh dấu in, nhân viên đóng gói)
    public function advanced_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_ids'])) {
            $ids = explode(',', $_POST['order_ids']);
            $db = (new Database())->getConnection();
            $type = $_GET['type'] ?? '';
            
            $ids = array_filter($ids);
            if (empty($ids) || empty($type)) {
                header("Location: index.php?action=order_processing");
                exit;
            }

            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $msg = "";

            if ($type === 'mark_print_delivery_yes') {
                $stmt = $db->prepare("UPDATE orders SET printed_delivery = 1 WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                $msg = "Đã đánh dấu ĐÃ IN phiếu giao hàng.";
            } elseif ($type === 'mark_print_delivery_no') {
                $stmt = $db->prepare("UPDATE orders SET printed_delivery = 0 WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                $msg = "Đã đánh dấu CHƯA IN phiếu giao hàng.";
            } elseif ($type === 'mark_print_picking_yes') {
                $stmt = $db->prepare("UPDATE orders SET printed_picking_order = 1, printed_picking_product = 1 WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                $msg = "Đã đánh dấu ĐÃ IN phiếu nhặt hàng.";
            } elseif ($type === 'mark_print_picking_no') {
                $stmt = $db->prepare("UPDATE orders SET printed_picking_order = 0, printed_picking_product = 0 WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                $msg = "Đã đánh dấu CHƯA IN phiếu nhặt hàng.";
            } elseif ($type === 'remove_packer') {
                $stmt = $db->prepare("UPDATE orders SET packer_id = NULL WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                $msg = "Đã xóa nhân viên đóng gói khỏi các kiện hàng.";
            } elseif ($type === 'add_packer') {
                $packer_id = $_POST['packer_id'] ?? null;
                if ($packer_id) {
                    $params = array_merge([$packer_id], $ids);
                    $stmt = $db->prepare("UPDATE orders SET packer_id = ? WHERE id IN ($inQuery)");
                    $stmt->execute($params);
                    $msg = "Đã phân công nhân viên đóng gói thành công.";
                }
            }

            header("Location: index.php?action=order_processing&success=" . urlencode($msg));
            exit;
        }
    }
}
