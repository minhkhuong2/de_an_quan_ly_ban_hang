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

        // Khởi tạo các biến mặc định mà view list.php mong đợi để tránh lỗi Undefined variable
        $active_tab_id = $_GET['tab'] ?? 'all';
        $saved_filters = []; // Mảng chứa các bộ lọc đã lưu (nếu có)
        $search_type = $_GET['search_type'] ?? 'order_code';
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? 'all';
        $payment_status = $_GET['payment_status'] ?? 'all';
        $branch_id = $_GET['branch_id'] ?? 'all';

        // Lấy danh sách chi nhánh (để hiển thị trong dropdown bộ lọc)
        $stmt_branches = $db->query("SELECT * FROM branches");
        $branches = $stmt_branches ? $stmt_branches->fetchAll(PDO::FETCH_ASSOC) : [];

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
        $settings_db = [];
        try {
            $stmt_set = $db->query("SELECT setting_key, setting_value FROM settings");
            $settings_db = $stmt_set->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu bảng settings không có cấu trúc key-value
        }

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

        // --- ĐOẠN CODE BỔ SUNG LẤY PHƯƠNG THỨC THANH TOÁN ---
        $stmt_pm = $db->query("SELECT * FROM payment_methods WHERE is_active = 1");
        $payment_methods = $stmt_pm->fetchAll(PDO::FETCH_ASSOC);
        $payment_methods_json = json_encode($payment_methods);
        // -----------------------------------------------------

        $products_json = json_encode($products);
        $customers_json = json_encode($customers);



        // Gọi ra giao diện POS đặc biệt
        require_once __DIR__ . '/../views/order/pos.php';
    }

    // 1. MÀN HÌNH TẠO ĐƠN HÀNG ONLINE (Tại Admin)
    public function create()
    {
        $db = (new Database())->getConnection();

        // 1. Lấy danh sách sản phẩm (Theo cú pháp chuẩn Khương đã sửa gáy bài trước)
        $query_products = "
            SELECT id, product_name, sku, base_price AS price, 100 as stock 
            FROM products WHERE parent_id IS NULL
        ";
        $stmt_prod = $db->query($query_products);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // 2. Lấy danh sách khách hàng
        $stmt_cust = $db->query("SELECT id, CONCAT(last_name, ' ', first_name) AS customer_name, phone, address FROM customers");
        $customers = $stmt_cust->fetchAll(PDO::FETCH_ASSOC);

        // 3. Lấy nguồn đơn hàng động đang hoạt động
        $stmt_src = $db->query("SELECT id, source_name FROM order_sources WHERE status = 'Đang sử dụng' ORDER BY sort_order ASC, id ASC");
        $order_sources = $stmt_src->fetchAll(PDO::FETCH_ASSOC);

        // 4. LẤY NHÂN VIÊN PHỤ TRÁCH ĐỘNG (Mục 6.2)
        try {
            // Giả sử bảng quản lý tài khoản/nhân viên của bạn tên là users hoặc employees
            $stmt_users = $db->query("SELECT id, full_name FROM users WHERE role = 'Nhân viên' OR role = 'Quản lý' OR 1=1");
            $employees = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Mảng dự phòng động từ PHP để tránh sập trang nếu sai tên bảng
            $employees = [['id' => 1, 'full_name' => 'Bùi Văn Khương'], ['id' => 2, 'full_name' => 'Tuấn Anh (Kinh doanh)']];
        }

        // 5. LẤY CHI NHÁNH ĐỘNG (Mục 6.1)
        try {
            $stmt_branches = $db->query("SELECT id, branch_name FROM branches");
            $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Mảng dự phòng động từ PHP
            $branches = [['id' => 1, 'branch_name' => 'AKC Store - Chi nhánh 1'], ['id' => 2, 'branch_name' => 'AKC Store - Showroom 2']];
        }

        // Ép sang JSON cho các bộ lọc tìm kiếm Javascript nhận diện nhanh
        $products_json = json_encode($products);
        $customers_json = json_encode($customers);

        require_once __DIR__ . '/../views/order/create.php';
    }

    // 2. API TÍNH TIỀN GIỎ HÀNG THÔNG MINH NGẦM (AJAX)
    public function calculate_api()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $cart_items = $data['cart_items'] ?? [];
        $promo_code = $data['promo_code'] ?? '';

        $subtotal = 0;

        // 1. SỬA LỖI `NaN`: Bổ sung tính toán Thành tiền cho TỪNG sản phẩm
        foreach ($cart_items as &$item) {
            $item['original_price'] = $item['price'];
            $item['final_price'] = $item['price']; // Tạm thời chưa có giảm giá đè trên từng SP

            // Ép kiểu về Float/Int để chắc chắn không bị lỗi chuỗi
            $qty = (float)$item['qty'];
            $price = (float)$item['final_price'];

            $item['line_total'] = $price * $qty;
            $subtotal += $item['line_total'];
        }
        unset($item); // Cắt tham chiếu để an toàn bộ nhớ

        $total_order_discount = 0;
        $msg = "";

        // 2. LOGIC XỬ LÝ MÃ GIẢM GIÁ
        if (!empty($promo_code)) {
            $db = (new Database())->getConnection();

            // Đã gỡ điều kiện start_date và end_date để bạn dễ Test dữ liệu cũ
            $stmt = $db->prepare("SELECT * FROM promotions WHERE promo_code = ? AND status = 'Đang áp dụng'");
            $stmt->execute([$promo_code]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($promo) {
                // Kiểm tra điều kiện đơn hàng tối thiểu
                if ($subtotal >= $promo['min_order_value']) {

                    if ($promo['discount_type'] == 'percent') {
                        $discount = $subtotal * ($promo['discount_value'] / 100);
                        if (!empty($promo['max_discount_amount']) && $promo['max_discount_amount'] > 0 && $discount > $promo['max_discount_amount']) {
                            $discount = $promo['max_discount_amount'];
                        }
                        $total_order_discount = $discount;
                    } else {
                        // Giảm thẳng tiền mặt (amount)
                        $total_order_discount = $promo['discount_value'];
                    }

                    $msg = "Áp dụng mã giảm giá thành công!";
                } else {
                    $msg = "Đơn hàng chưa đạt giá trị tối thiểu " . number_format($promo['min_order_value']) . "đ";
                    $total_order_discount = 0;
                }
            } else {
                $msg = "Mã khuyến mãi không tồn tại hoặc đã bị khóa!";
                $total_order_discount = 0;
            }
        }

        $grand_total_before_tax = $subtotal - $total_order_discount;

        // 3. Trả dữ liệu mượt mà về cho Javascript
        echo json_encode([
            'status' => 'success',
            'msg' => $msg,
            'data' => [
                'cart_items' => $cart_items,
                'summary' => [
                    'subtotal' => $subtotal,
                    'total_product_discount' => 0,
                    'total_order_discount' => $total_order_discount,
                    'grand_total' => $grand_total_before_tax
                ]
            ]
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

        // Lấy cấu hình payment_methods để tạo mã QR
        $stmt_pm = $db->query("SELECT * FROM payment_methods WHERE is_active = 1");
        $payment_methods = $stmt_pm->fetchAll(PDO::FETCH_ASSOC);

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
    // Xử lý Lưu đơn hàng Online từ trang create.php gửi lên
    public function store_online_order()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data || empty($data['cart_items'])) {
                echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ.']);
                exit;
            }

            // Lấy trạng thái lưu đơn từ người dùng
            $action_type = $data['action_type']; // 'draft', 'create', 'confirm', 'ship'

            // --- CHUYỂN ĐỔI ACTION_TYPE THÀNH TRẠNG THÁI CHUẨN AKC ---
            $order_status = 'pending';
            $shipping_status = 'pending';

            if ($action_type === 'draft') {
                $order_status = 'draft'; // Đơn nháp
            } elseif ($action_type === 'create') {
                $order_status = 'processing'; // Đang giao dịch
            } elseif ($action_type === 'confirm') {
                $order_status = 'confirmed'; // Đã xác nhận
            } elseif ($action_type === 'ship') {
                $order_status = 'confirmed';
                $shipping_status = 'shipping'; // Đang giao hàng
            }

            // Ở đây sau này Khương sẽ viết các lệnh SQL:
            // 1. INSERT INTO orders (...)
            // 2. INSERT INTO order_items (...)
            // 3. Trừ số lượng tồn kho (Nếu action_type khác 'draft')

            echo json_encode([
                'status' => 'success',
                'msg' => 'Đã lưu đơn hàng Online thành công với hành động: ' . strtoupper($action_type)
            ]);
            exit;
        }
    }
    // 1. DANH SÁCH ĐƠN HÀNG (TÍCH HỢP TÌM KIẾM ĐA LUỒNG & BỘ LỌC)
    public function index()
    {
        $db = (new Database())->getConnection();

        // Nhận tham số tìm kiếm & Lọc
        $keyword = trim($_GET['keyword'] ?? '');
        $search_type = $_GET['search_type'] ?? 'all'; // all, order_code, phone
        $status = $_GET['status'] ?? 'all';
        $payment_status = $_GET['payment_status'] ?? 'all';
        $branch_id = $_GET['branch_id'] ?? 'all';

        // Nếu click vào một Tab Lưu bộ lọc, parse JSON ra để ghi đè GET
        $active_tab_id = $_GET['tab_id'] ?? '';
        if ($active_tab_id && $active_tab_id !== 'all') {
            $stmt_tab = $db->prepare("SELECT filter_data FROM saved_filters WHERE id = ?");
            $stmt_tab->execute([$active_tab_id]);
            $tab_data = $stmt_tab->fetchColumn();
            if ($tab_data) {
                $parsed = json_decode($tab_data, true);
                if (isset($parsed['status'])) $status = $parsed['status'];
                if (isset($parsed['payment_status'])) $payment_status = $parsed['payment_status'];
                if (isset($parsed['branch_id'])) $branch_id = $parsed['branch_id'];
            }
        }

        // Xây dựng câu truy vấn động
        $query = "SELECT o.*, b.branch_name FROM orders o LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
        $params = [];

        // 1. Xử lý Tìm kiếm (Từ khóa ngăn cách bởi dấu phẩy)
        if ($keyword !== '') {
            $keywords = explode(',', $keyword);
            $keyword_conditions = [];
            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if ($kw === '') continue;

                if ($search_type === 'order_code') {
                    $keyword_conditions[] = "o.order_code LIKE '%$kw%'";
                } elseif ($search_type === 'phone') {
                    $kw_phone = preg_replace('/[^0-9]/', '', $kw); // Lọc ký tự thừa
                    $keyword_conditions[] = "o.phone LIKE '%$kw_phone%'";
                } else {
                    // Chế độ ALL: Ưu tiên quét qua nhiều trường
                    $kw_clean = htmlspecialchars($kw);
                    $keyword_conditions[] = "(o.order_code LIKE '%$kw_clean%' OR o.phone LIKE '%$kw_clean%' OR o.customer_name LIKE '%$kw_clean%')";
                }
            }
            if (!empty($keyword_conditions)) {
                $query .= " AND (" . implode(' OR ', $keyword_conditions) . ")";
            }
        }

        // 2. Xử lý Bộ lọc Dropdown
        if ($status !== 'all') {
            $query .= " AND o.order_status = ?";
            $params[] = $status;
        }
        if ($payment_status !== 'all') {
            $query .= " AND o.payment_status = ?";
            $params[] = $payment_status;
        }
        if ($branch_id !== 'all') {
            $query .= " AND o.branch_id = ?";
            $params[] = $branch_id;
        }

        $query .= " ORDER BY o.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách Bộ lọc đã lưu & Chi nhánh để đổ ra giao diện
        $saved_filters = $db->query("SELECT * FROM saved_filters WHERE module_name = 'orders'")->fetchAll(PDO::FETCH_ASSOC);
        $branches = $db->query("SELECT id, branch_name FROM branches WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/order/list.php';
    }

    // 2. LƯU BỘ LỌC (TẠO TAB MỚI)
    public function save_filter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filter_name = trim($_POST['filter_name']);
            // Gom các tham số lọc hiện tại thành chuỗi JSON
            $filter_data = json_encode([
                'status' => $_POST['status'] ?? 'all',
                'payment_status' => $_POST['payment_status'] ?? 'all',
                'branch_id' => $_POST['branch_id'] ?? 'all'
            ]);

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO saved_filters (module_name, filter_name, filter_data) VALUES ('orders', ?, ?)");
            $stmt->execute([$filter_name, $filter_data]);

            header("Location: index.php?action=order_list&tab_id=" . $db->lastInsertId());
            exit;
        }
    }
    // 3. GIAO HÀNG HÀNG LOẠT (Tạo Vận đơn Bulk)
    public function bulk_ship()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = explode(',', $_POST['bulk_order_ids']);
            $partner_code = $_POST['bulk_partner_code'] ?? 'ghn';
            $branch_id = intval($_POST['bulk_branch_id'] ?? 0);

            $db = (new Database())->getConnection();
            $success_count = 0;

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    $order_id = intval($id);
                    if ($order_id <= 0) continue;

                    // Kiểm tra đơn hàng có tồn tại và chưa giao không
                    $stmt_check = $db->prepare("SELECT order_code, shipping_status, grand_total, amount_paid FROM orders WHERE id = ?");
                    $stmt_check->execute([$order_id]);
                    $order = $stmt_check->fetch(PDO::FETCH_ASSOC);

                    if ($order && $order['shipping_status'] !== 'delivered') {
                        // Tính tiền COD = Tổng bill - Đã thanh toán
                        $cod_amount = max(0, floatval($order['grand_total']) - floatval($order['amount_paid']));
                        $tracking_code = strtoupper($partner_code) . date('ymd') . rand(1000, 9999);

                        // 1. Tạo Vận đơn sang bảng shipments
                        $stmt_ship = $db->prepare("INSERT INTO shipments (order_id, branch_id, tracking_code, partner_code, status, cod_amount) VALUES (?, ?, ?, ?, 'pending', ?)");
                        $stmt_ship->execute([$order_id, $branch_id, $tracking_code, $partner_code, $cod_amount]);

                        // 2. Cập nhật trạng thái đơn hàng thành Delivering
                        $stmt_update = $db->prepare("UPDATE orders SET shipping_status = 'delivering', order_status = 'processing' WHERE id = ?");
                        $stmt_update->execute([$order_id]);

                        $success_count++;
                    }
                }

                $db->commit();
                header("Location: index.php?action=order_list&success_bulk=1&count=" . $success_count);
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi xử lý giao hàng: " . $e->getMessage());
            }
            exit;
        }
    }
    // 4. THAO TÁC HÀNG LOẠT (Lưu trữ, Đóng gói, Gán nhân viên, Tags)
    public function bulk_actions()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action_type = $_POST['bulk_action_type']; // archive, unarchive, packing, assign_staff, add_tags
            $ids = explode(',', $_POST['bulk_action_ids']);

            $db = (new Database())->getConnection();
            $success_count = 0;

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    $order_id = intval($id);
                    if ($order_id <= 0) continue;

                    switch ($action_type) {
                        case 'archive':
                            $stmt = $db->prepare("UPDATE orders SET is_archived = 1 WHERE id = ? AND order_status IN ('completed', 'cancelled')");
                            if ($stmt->execute([$order_id]) && $stmt->rowCount() > 0) $success_count++;
                            break;

                        case 'unarchive':
                            $stmt = $db->prepare("UPDATE orders SET is_archived = 0 WHERE id = ?");
                            if ($stmt->execute([$order_id])) $success_count++;
                            break;

                        case 'packing':
                            $stmt = $db->prepare("UPDATE orders SET packing_status = 'packing', order_status = 'processing' WHERE id = ? AND order_status = 'pending'");
                            if ($stmt->execute([$order_id]) && $stmt->rowCount() > 0) $success_count++;
                            break;

                        case 'assign_staff':
                            $staff_id = intval($_POST['assign_staff_id']);
                            $stmt = $db->prepare("UPDATE orders SET assigned_staff_id = ? WHERE id = ?");
                            if ($stmt->execute([$staff_id, $order_id])) $success_count++;
                            break;

                        case 'add_tags':
                            $new_tags = trim($_POST['order_tags']);
                            if (!empty($new_tags)) {
                                // Lấy tag cũ ra nối thêm tag mới
                                $stmt_get = $db->prepare("SELECT tags FROM orders WHERE id = ?");
                                $stmt_get->execute([$order_id]);
                                $old_tags = $stmt_get->fetchColumn();

                                $combined_tags = empty($old_tags) ? $new_tags : $old_tags . ',' . $new_tags;
                                // Xóa khoảng trắng và loại bỏ tag trùng lặp
                                $tags_array = array_unique(array_map('trim', explode(',', $combined_tags)));
                                $final_tags = implode(', ', $tags_array);

                                $stmt = $db->prepare("UPDATE orders SET tags = ? WHERE id = ?");
                                if ($stmt->execute([$final_tags, $order_id])) $success_count++;
                            }
                            break;
                        case 'confirm_orders':
                            $stmt = $db->prepare("UPDATE orders SET order_status = 'processing' WHERE id = ? AND order_status = 'pending'");
                            if ($stmt->execute([$order_id]) && $stmt->rowCount() > 0) $success_count++;
                            break;

                        // TÍNH NĂNG MỚI: Phát hành Hóa đơn điện tử (VAT)
                        case 'issue_e_invoices':
                            $symbol = trim($_POST['invoice_symbol'] ?? '1C26TAA'); // Lấy ký hiệu từ Modal

                            // Kiểm tra xem đơn này đã xuất HĐ chưa
                            $check = $db->prepare("SELECT has_e_invoice FROM orders WHERE id = ?");
                            $check->execute([$order_id]);
                            if ($check->fetchColumn() == 0) {
                                // Sinh dữ liệu giả lập (Mock Data)
                                $inv_no = '000' . rand(1000, 9999);
                                $cqt_code = strtoupper(uniqid('CQT-')); // Ví dụ: CQT-64E2A8...

                                $stmt_inv = $db->prepare("INSERT INTO e_invoices (order_id, invoice_number, invoice_symbol, cqt_code) VALUES (?, ?, ?, ?)");
                                if ($stmt_inv->execute([$order_id, $inv_no, $symbol, $cqt_code])) {
                                    // Đánh dấu đơn hàng là đã xuất HĐ
                                    $db->prepare("UPDATE orders SET has_e_invoice = 1 WHERE id = ?")->execute([$order_id]);
                                    $success_count++;
                                }
                            }
                            break;
                    }
                }

                $db->commit();
                header("Location: index.php?action=order_list&success_bulk_action=1&action_name=$action_type&count=$success_count");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi thao tác hàng loạt: " . $e->getMessage());
            }
            exit;
        }
    }
    // 5. XUẤT FILE EXCEL / CSV ĐƠN HÀNG
    public function export_orders()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $export_scope = $_POST['export_scope'] ?? 'all';
            $export_type = $_POST['export_type'] ?? 'summary';
            $export_ids = $_POST['export_ids'] ?? '';

            $db = (new Database())->getConnection();
            $query = "";

            if ($export_type === 'detailed') {
                // File chi tiết: JOIN với order_items để lấy từng sản phẩm
                $query = "SELECT o.order_code, o.created_at, o.customer_name, o.phone, b.branch_name, 
                                 oi.product_name, oi.sku, oi.qty, oi.final_price, oi.line_total, 
                                 o.grand_total, o.payment_status, o.order_status
                          FROM orders o 
                          LEFT JOIN order_items oi ON o.order_id = oi.order_id
                          LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
            } else {
                // File tổng quan: Chỉ lấy thông tin đơn mẹ
                $query = "SELECT o.order_code, o.created_at, o.customer_name, o.phone, b.branch_name, 
                                 o.subtotal, o.grand_total, o.amount_paid, 
                                 o.payment_status, o.order_status
                          FROM orders o 
                          LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
            }

            // Xử lý phạm vi xuất
            if ($export_scope === 'selected' && !empty($export_ids)) {
                // Chống SQL Injection cho mệnh đề IN
                $ids_array = array_map('intval', explode(',', $export_ids));
                $ids_str = implode(',', $ids_array);
                $query .= " AND o.id IN ($ids_str)";
            }

            $query .= " ORDER BY o.created_at DESC";
            $stmt = $db->query($query);

            // THIẾT LẬP HEADER ĐỂ TẢI FILE DƯỚI DẠNG CSV
            $filename = "Export_DonHang_" . date('Ymd_His') . ".csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);

            $output = fopen('php://output', 'w');

            // Ghi BOM để Excel nhận diện được Tiếng Việt có dấu (UTF-8)
            fputs($output, "\xEF\xBB\xBF");

            // Xây dựng Dòng Tiêu đề (Header)
            if ($export_type === 'detailed') {
                fputcsv($output, ['Mã đơn hàng', 'Ngày tạo', 'Khách hàng', 'Điện thoại', 'Chi nhánh', 'Tên sản phẩm', 'Mã SKU', 'Số lượng', 'Đơn giá', 'Thành tiền SP', 'Tổng bill', 'Thanh toán', 'Trạng thái']);
            } else {
                fputcsv($output, ['Mã đơn hàng', 'Ngày tạo', 'Khách hàng', 'Điện thoại', 'Chi nhánh', 'Tổng tiền hàng', 'Khách phải trả', 'Đã thanh toán', 'Trạng thái TT', 'Trạng thái Đơn']);
            }

            // Ghi dữ liệu từng dòng
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Mẹo cực đỉnh: Bọc ="098..." để Excel không xóa mất số 0 ở đầu Số điện thoại và Mã đơn
                $row['order_code'] = '="' . $row['order_code'] . '"';
                $row['phone'] = '="' . ($row['phone'] ?? '') . '"';

                // Dịch thuật trạng thái
                $row['payment_status'] = ($row['payment_status'] == 'paid') ? 'Đã thanh toán' : 'Chưa thanh toán';
                if ($row['order_status'] == 'completed') $row['order_status'] = 'Hoàn thành';
                elseif ($row['order_status'] == 'cancelled') $row['order_status'] = 'Đã hủy';
                elseif ($row['order_status'] == 'processing') $row['order_status'] = 'Đang xử lý';
                else $row['order_status'] = 'Chờ xử lý';

                fputcsv($output, $row);
            }

            fclose($output);
            exit; // Ngừng thực thi để không in ra mã HTML dư thừa
        }
    }
}
