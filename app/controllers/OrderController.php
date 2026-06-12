<?php
// Đường dẫn file: app/controllers/OrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/PromotionController.php'; // Gọi Controller Khuyến mại sang để dùng ké thuật toán
require_once __DIR__ . '/../models/PromotionModel.php';

class OrderController
{
    public function list()
    {
        // Require view for order list if exists, otherwise show a placeholder
        if (file_exists(__DIR__ . '/../views/order/list.php')) {
            require_once __DIR__ . '/../views/order/list.php';
        } else {
            echo "<h2>Tính năng Quản lý Đơn hàng đang được phát triển.</h2>";
        }
    }

    public function pos()
    {
        // 1. MÀN HÌNH TẠO ĐƠN HÀNG (POS)
        // Hiện tại dùng chung với hàm create
        $this->create();
    }

    // 1. MÀN HÌNH TẠO ĐƠN HÀNG ONLINE (Tại Admin)
    public function create()
    {
        $db = (new Database())->getConnection();

        // Lấy danh sách sản phẩm (Bổ sung AS price để fix lỗi hôm qua)
        $stmt_prod = $db->query("SELECT id, product_name, sku, base_price AS price FROM products WHERE parent_id IS NULL");
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách khách hàng
        $stmt_cust = $db->query("SELECT id, CONCAT(last_name, ' ', first_name) AS customer_name, phone FROM customers");
        $customers = $stmt_cust->fetchAll(PDO::FETCH_ASSOC);

        $products_json = json_encode($products);
        $customers_json = json_encode($customers);

        // Đã đổi tên file thành create.php
        require_once __DIR__ . '/../views/order/create.php';
    }

    // 2. API TÍNH TIỀN GIỎ HÀNG THÔNG MINH NGẦM (AJAX)
    public function calculate_api()
    {
        // Trả về định dạng JSON
        header('Content-Type: application/json');

        // Nhận dữ liệu Giỏ hàng từ Javascript ném lên
        $data = json_decode(file_get_contents('php://input'), true);
        $cart_items = $data['cart_items'] ?? [];
        $original_shipping_fee = (float)($data['shipping_fee'] ?? 0);

        if (empty($cart_items)) {
            echo json_encode(['status' => 'empty']);
            exit;
        }

        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        // Lấy TẤT CẢ các chương trình Khuyến mại đang ở trạng thái "Đang áp dụng"
        $active_promos = $promoModel->getAllPromotions('', 'Đang áp dụng', '', '');

        // Khởi động cỗ máy tính tiền Sapo OmniAI V3
        $promoController = new PromotionController();

        // 1. Dùng hàm Lọc Nhóm Khuyến Mại Tốt Nhất (Tránh xung đột cộng dồn)
        $best_promo_group = $promoController->getBestPromoGroup($active_promos, $cart_items, $original_shipping_fee);

        // 2. Tính ra số tiền cuối cùng dựa trên Nhóm Tốt Nhất
        $result = $promoController->calculateCartTotal($cart_items, $best_promo_group, $original_shipping_fee);

        echo json_encode([
            'status' => 'success',
            'data' => $result,
            'applied_promos' => $best_promo_group // Trả về danh sách KM đã áp dụng
        ]);
        exit;
    }
    // 3. API LƯU ĐƠN HÀNG VÀO DATABASE
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $cart_items = $data['cart_items'] ?? [];
        $summary = $data['summary'] ?? [];

        if (empty($cart_items)) {
            echo json_encode(['status' => 'error', 'msg' => 'Giỏ hàng đang trống! Vui lòng chọn sản phẩm.']);
            exit;
        }

        $db = (new Database())->getConnection();

        try {
            // Bắt đầu giao dịch an toàn (Nếu lỗi ở giữa chừng sẽ hoàn tác toàn bộ)
            $db->beginTransaction();

            // Sinh mã đơn hàng ngẫu nhiên (VD: SON + mốc thời gian)
            $order_code = 'SON' . strtoupper(substr(uniqid(), -6));

            // Lấy thông tin thanh toán từ JS gửi lên
            $payment_status = $data['payment_status'] ?? 'pending';

            // 1. LƯU VÀO BẢNG CHÍNH (orders)
            $query_order = "INSERT INTO orders (
                order_code, subtotal, total_product_discount, total_order_discount, 
                original_shipping_fee, total_shipping_discount, grand_total, 
                order_status, payment_status, sales_channel
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', ?, 'web')";

            // Tính subtotal gốc trước giảm giá
            $subtotal_p0 = array_sum(array_map(function ($item) {
                return $item['price'] * $item['qty'];
            }, $cart_items));

            $stmt_order = $db->prepare($query_order);
            $stmt_order->execute([
                $order_code,
                $subtotal_p0,
                $summary['total_product_discount'] ?? 0,
                $summary['total_order_discount'] ?? 0,
                $summary['final_shipping_fee'] ?? 0, // Phí ship
                $summary['total_shipping_discount'] ?? 0,
                $summary['grand_total'] ?? 0,
                $payment_status
            ]);

            $order_id = $db->lastInsertId(); // Lấy ID của đơn hàng vừa tạo

            // 2. LƯU CHI TIẾT SẢN PHẨM VÀO BẢNG (order_items)
            $query_item = "INSERT INTO order_items (
                order_id, product_id, product_name, sku, qty, 
                original_price, final_price, line_total, is_gift
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt_item = $db->prepare($query_item);

            foreach ($cart_items as $item) {
                $is_gift = ($item['final_price'] == 0) ? 1 : 0; // Đánh dấu nếu là hàng tặng 0đ
                $stmt_item->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['sku'],
                    $item['qty'],
                    $item['price'],
                    $item['final_price'],
                    $item['line_total'],
                    $is_gift
                ]);
            }

            // Chốt giao dịch
            $db->commit();

            echo json_encode([
                'status' => 'success',
                'msg' => 'Tạo đơn hàng thành công!',
                'order_code' => $order_code
            ]);
        } catch (Exception $e) {
            $db->rollBack(); // Hủy bỏ nếu có lỗi SQL
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
        exit;
    }
}
