<?php
// Đường dẫn file: app/controllers/OrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/PromotionController.php'; // Gọi Controller Khuyến mại sang để dùng ké thuật toán
require_once __DIR__ . '/../models/PromotionModel.php';

class OrderController
{
    // 1. MÀN HÌNH TẠO ĐƠN HÀNG (POS)
    // Đổi tên hàm từ create() thành pos() để khớp với URL: order/pos
    // 1. MÀN HÌNH TẠO ĐƠN HÀNG ONLINE (Tại Admin)
    public function create()
    {
        $db = (new Database())->getConnection();

        // Lấy danh sách sản phẩm (Bổ sung AS price để fix lỗi hôm qua)
        $stmt_prod = $db->query("SELECT id, product_name, sku, sell_price AS price FROM products WHERE parent_id IS NULL");
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách khách hàng
        $stmt_cust = $db->query("SELECT id, customer_name, phone FROM customers");
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
}
