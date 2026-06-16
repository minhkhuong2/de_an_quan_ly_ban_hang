<?php
// Đường dẫn file: app/controllers/OrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/PromotionController.php'; // Gọi Controller Khuyến mại sang để dùng ké thuật toán
require_once __DIR__ . '/../models/PromotionModel.php';

class OrderController
{
    public function list()
    {
        $db = (new Database())->getConnection();

        // Truy vấn lấy danh sách đơn hàng mới nhất lên đầu
        // Dùng LEFT JOIN để lấy tên khách hàng, dùng CONCAT để nối họ và tên
        $query = "SELECT o.*, 
                         CONCAT(c.last_name, ' ', c.first_name) AS customer_name 
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  ORDER BY o.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gọi View hiển thị
        require_once __DIR__ . '/../views/order/list.php';
    }

    // ========================================================
    // MÀN HÌNH BÁN HÀNG TẠI QUẦY (FULLSCREEN POS)
    // ========================================================
    public function pos()
    {
        $db = (new Database())->getConnection();

        // 1. LẤY CẤU HÌNH HỆ THỐNG ĐỂ FIX LỖI UNDEFINED VARIABLE
        $stmt_set = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings_db = $stmt_set->fetchAll(PDO::FETCH_KEY_PAIR);

        // Nếu DB chưa có cấu hình (đề phòng chạy mới), ta gán giá trị mặc định để không bao giờ lỗi
        if (empty($settings_db)) {
            $settings_db = [
                'pos_use_promo_code' => '1',
                'pos_auto_print' => '1',
                'pos_allow_price_edit' => '1',
                'pos_allow_negative_stock' => '0'
            ];
        }

        // 2. Lấy sản phẩm (Bơm cột giá đúng của bạn vào đây, ví dụ base_price)
        $stmt_prod = $db->query("SELECT id, product_name, sku, base_price AS price FROM products WHERE parent_id IS NULL");
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // 3. Lấy khách hàng
        $stmt_cust = $db->query("SELECT id, CONCAT(last_name, ' ', first_name) AS customer_name, phone FROM customers");
        $customers = $stmt_cust->fetchAll(PDO::FETCH_ASSOC);

        $products_json = json_encode($products);
        $customers_json = json_encode($customers);

        // Gọi ra giao diện POS đặc biệt
        require_once __DIR__ . '/../views/order/pos.php';
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
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $cart_items = $data['cart_items'] ?? [];
        $original_shipping_fee = (float)($data['shipping_fee'] ?? 0);
        $promo_code = trim($data['promo_code'] ?? ''); // <-- NHẬN MÃ GIẢM GIÁ TỪ JS

        if (empty($cart_items)) {
            echo json_encode(['status' => 'empty']);
            exit;
        }

        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        // 1. Lấy các Khuyến mại TỰ ĐỘNG (Không cần mã)
        $active_promos = $promoModel->getAllPromotions('', 'Đang áp dụng', '', 'auto');

        // 2. NẾU KHÁCH CÓ NHẬP MÃ: Tìm mã đó trong DB và nhét thêm vào danh sách Khuyến mại
        if (!empty($promo_code)) {
            $stmt = $db->prepare("SELECT * FROM promotions WHERE promo_code = ? AND status = 'Đang áp dụng'");
            $stmt->execute([$promo_code]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($coupon) {
                // Nhét mã này vào mảng để thuật toán quét chung
                $active_promos[] = $coupon;
            }
        }

        $promoController = new PromotionController();
        $best_promo_group = $promoController->getBestPromoGroup($active_promos, $cart_items, $original_shipping_fee);
        $result = $promoController->calculateCartTotal($cart_items, $best_promo_group, $original_shipping_fee);

        echo json_encode([
            'status' => 'success',
            'data' => $result,
            'applied_promos' => $best_promo_group
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
            // Bắt đầu giao dịch an toàn
            $db->beginTransaction();

            // Sinh mã đơn hàng ngẫu nhiên
            $order_code = 'SON' . strtoupper(substr(uniqid(), -6));
            // Lấy thông tin từ JS gửi lên
            $payment_status = $data['payment_status'] ?? 'paid';
            $payment_method = $data['payment_method'] ?? 'cash'; // Tiền mặt/Chuyển khoản
            $customer_id = !empty($data['customer_id']) ? $data['customer_id'] : null;
            $amount_paid = $data['amount_paid'] ?? 0; // Tiền khách đưa

            // 1. LƯU VÀO BẢNG CHÍNH (orders) - Thêm Thuế, Khách đưa, Phương thức
            $query_order = "INSERT INTO orders (
                order_code, customer_id, subtotal, total_product_discount, total_order_discount, 
                original_shipping_fee, total_shipping_discount, tax_amount, grand_total, 
                amount_paid, payment_method, order_status, payment_status, sales_channel
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, 'pos')";

            $subtotal_p0 = array_sum(array_map(function ($item) {
                return $item['price'] * $item['qty'];
            }, $cart_items));

            $stmt_order = $db->prepare($query_order);
            $stmt_order->execute([
                $order_code,
                $customer_id,
                $subtotal_p0,
                $summary['total_product_discount'] ?? 0,
                $summary['total_order_discount'] ?? 0,
                $summary['final_shipping_fee'] ?? 0,
                $summary['total_shipping_discount'] ?? 0,
                $summary['tax_amount'] ?? 0, // Lưu tiền thuế
                $summary['grand_total'] ?? 0,
                $amount_paid, // Lưu tiền khách đưa
                $payment_method, // Hình thức thanh toán
                $payment_status
            ]);

            $order_id = $db->lastInsertId();

            // 2. LƯU CHI TIẾT VÀO BẢNG (order_items) - ĐÃ BỔ SUNG PROMO_DISCOUNT
            $query_item = "INSERT INTO order_items (
                order_id, product_id, product_name, sku, qty, 
                original_price, promo_discount, manual_discount, final_price, line_total, is_gift
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)";

            $stmt_item = $db->prepare($query_item);

            foreach ($cart_items as $item) {
                $is_gift = ($item['final_price'] == 0) ? 1 : 0;

                // Thuật toán tự tính ra tiền đã giảm của từng món (Giá gốc - Giá cuối)
                $promo_discount = $item['price'] - $item['final_price'];

                $stmt_item->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['sku'],
                    $item['qty'],
                    $item['price'],
                    $promo_discount, // Truyền giá trị vào đây để MySQL không báo lỗi
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
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
        exit;
    }
    public function view()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        // 1. Lấy thông tin chung của đơn hàng + thông tin khách hàng
        $query_order = "SELECT o.*, 
                               CONCAT(c.last_name, ' ', c.first_name) AS customer_name, 
                               c.phone, c.address 
                        FROM orders o 
                        LEFT JOIN customers c ON o.customer_id = c.id 
                        WHERE o.id = ?";
        $stmt_order = $db->prepare($query_order);
        $stmt_order->execute([$id]);
        $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            header("Location: index.php?action=order_list");
            exit;
        }

        // 2. Lấy danh sách sản phẩm nằm trong đơn hàng này
        $query_items = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt_items = $db->prepare($query_items);
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Gọi View hiển thị
        require_once __DIR__ . '/../views/order/detail.php';
    }
    // ========================================================
    // TÍNH NĂNG IN HÓA ĐƠN (MÁY IN NHIỆT 80mm)
    // ========================================================
    public function print()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        // Lấy thông tin đơn hàng
        $query_order = "SELECT o.*, CONCAT(c.last_name, ' ', c.first_name) AS customer_name 
                        FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?";
        $stmt_order = $db->prepare($query_order);
        $stmt_order->execute([$id]);
        $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Không tìm thấy thông tin đơn hàng!");
        }

        // Lấy chi tiết sản phẩm
        $query_items = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt_items = $db->prepare($query_items);
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Gọi View mẫu In Hóa Đơn
        require_once __DIR__ . '/../views/order/print.php';
    }
    // ========================================================
    // 4. XỬ LÝ GIAO HÀNG & TỰ ĐỘNG TRỪ TỒN KHO
    // ========================================================
    public function update_shipping()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'msg' => 'Phương thức không hợp lệ']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);
        $status = $data['status'] ?? 'delivered'; // Mặc định là giao thành công

        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // 1. Cập nhật trạng thái giao hàng trong bảng orders
            $stmt = $db->prepare("UPDATE orders SET shipping_status = ?, order_status = 'completed' WHERE id = ?");
            $stmt->execute([$status, $order_id]);

            // 2. LOGIC TRỪ TỒN KHO TỰ ĐỘNG (Nếu giao hàng thành công)
            if ($status === 'delivered') {
                // Lấy ra các sản phẩm và số lượng trong đơn hàng này
                $stmt_items = $db->prepare("SELECT product_id, qty FROM order_items WHERE order_id = ? AND is_gift = 0");
                $stmt_items->execute([$order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Tiến hành trừ kho (Giả sử bảng products của bạn có cột stock hoặc quantity, bạn thay thế cho đúng nhé)
                // Ở đây mình tạm dùng cột stock. Nếu DB của bạn là quantity thì đổi chữ stock thành quantity nhé.
                $stmt_update_stock = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

                foreach ($items as $item) {
                    $stmt_update_stock->execute([$item['qty'], $item['product_id']]);
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'msg' => 'Cập nhật trạng thái giao hàng thành công, kho hàng đã tự động đồng bộ!']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }

    // ========================================================
    // 5. XÁC NHẬN THANH TOÁN (THU TIỀN COD/SAU)
    // ========================================================
    public function collect_payment()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);

        $db = (new Database())->getConnection();

        try {
            // Lấy ra số tiền grand_total khách cần trả của đơn này
            $stmt_order = $db->prepare("SELECT grand_total FROM orders WHERE id = ?");
            $stmt_order->execute([$order_id]);
            $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy đơn hàng!']);
                exit;
            }

            // Cập nhật: Khách đã trả đủ tiền (amount_paid = grand_total) và chuyển trạng thái thành 'paid'
            $stmt_update = $db->prepare("UPDATE orders SET amount_paid = grand_total, payment_status = 'paid' WHERE id = ?");
            $stmt_update->execute([$order_id]);

            echo json_encode(['status' => 'success', 'msg' => 'Xác nhận thu tiền thành công! Đơn hàng đã chuyển sang trạng thái Đã thanh toán.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }
    // ========================================================
    // 6. HỦY ĐƠN HÀNG & HOÀN LẠI TỒN KHO
    // ========================================================
    public function cancel()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);

        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // 1. Kiểm tra trạng thái hiện tại của đơn hàng
            $stmt = $db->prepare("SELECT order_status, shipping_status FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Không tìm thấy đơn hàng!");
            }
            if ($order['order_status'] == 'cancelled') {
                throw new Exception("Đơn hàng này đã bị hủy từ trước rồi!");
            }

            // 2. NẾU ĐÃ XUẤT KHO (Đã giao hàng) -> PHẢI CỘNG LẠI TỒN KHO TRƯỚC KHI HỦY
            if ($order['shipping_status'] == 'delivered') {
                $stmt_items = $db->prepare("SELECT product_id, qty FROM order_items WHERE order_id = ? AND is_gift = 0");
                $stmt_items->execute([$order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Cập nhật lại kho (Cộng trả lại số lượng). 
                // *Lưu ý: Nếu bảng products của bạn dùng cột quantity thì đổi chữ stock thành quantity nhé
                $stmt_restore = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                foreach ($items as $item) {
                    $stmt_restore->execute([$item['qty'], $item['product_id']]);
                }
            }

            // 3. Cập nhật trạng thái đơn hàng thành Đã hủy (cancelled)
            $stmt_update = $db->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
            $stmt_update->execute([$order_id]);

            $db->commit();

            $msg = $order['shipping_status'] == 'delivered'
                ? 'Đã hủy đơn hàng và hệ thống đã CỘNG TRẢ LẠI số lượng vào kho!'
                : 'Đã hủy đơn hàng thành công!';

            echo json_encode(['status' => 'success', 'msg' => $msg]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
        exit;
    }
    // ========================================================
    // 7. THÊM NHANH KHÁCH HÀNG TỪ MÀN HÌNH POS
    // ========================================================
    public function quick_add_customer()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đủ Tên và Số điện thoại!']);
            exit;
        }

        $db = (new Database())->getConnection();
        try {
            // Tách tên và họ (Vì bảng customers của bạn dùng first_name và last_name)
            $parts = explode(' ', $name);
            $first_name = array_pop($parts);
            $last_name = implode(' ', $parts);

            $stmt = $db->prepare("INSERT INTO customers (first_name, last_name, phone) VALUES (?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $phone]);

            $new_id = $db->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'customer' => ['id' => $new_id, 'customer_name' => $name, 'phone' => $phone]
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi DB: ' . $e->getMessage()]);
        }
        exit;
    }
}
