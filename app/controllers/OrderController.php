<?php
// ÄÆ°á»ng dáº«n file: app/controllers/OrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/PromotionController.php'; // Gá»i Controller Khuyáº¿n máº¡i sang Ä‘á»ƒ dÃ¹ng kÃ© thuáº­t toÃ¡n
require_once __DIR__ . '/../models/PromotionModel.php';

class OrderController
{
    public function list()
    {
        $db = (new Database())->getConnection();

        // Truy váº¥n láº¥y danh sÃ¡ch Ä‘Æ¡n hÃ ng má»›i nháº¥t lÃªn Ä‘áº§u
        // DÃ¹ng LEFT JOIN Ä‘á»ƒ láº¥y tÃªn khÃ¡ch hÃ ng, dÃ¹ng CONCAT Ä‘á»ƒ ná»‘i há» vÃ  tÃªn
        $query = "SELECT o.*, 
                         CONCAT(c.last_name, ' ', c.first_name) AS customer_name 
                  FROM orders o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  ORDER BY o.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Khá»Ÿi táº¡o cÃ¡c biáº¿n máº·c Ä‘á»‹nh mÃ  view list.php mong Ä‘á»£i Ä‘á»ƒ trÃ¡nh lá»—i Undefined variable
        $active_tab_id = $_GET['tab'] ?? 'all';
        $saved_filters = []; // Máº£ng chá»©a cÃ¡c bá»™ lá»c Ä‘Ã£ lÆ°u (náº¿u cÃ³)
        $search_type = $_GET['search_type'] ?? 'order_code';
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? 'all';
        $payment_status = $_GET['payment_status'] ?? 'all';
        $branch_id = $_GET['branch_id'] ?? 'all';

        // Láº¥y danh sÃ¡ch chi nhÃ¡nh (Ä‘á»ƒ hiá»ƒn thá»‹ trong dropdown bá»™ lá»c)
        $stmt_branches = $db->query("SELECT * FROM branches");
        $branches = $stmt_branches ? $stmt_branches->fetchAll(PDO::FETCH_ASSOC) : [];

        // Gá»i View hiá»ƒn thá»‹
        require_once __DIR__ . '/../views/order/list.php';
    }

    // ========================================================
    // MÃ€N HÃŒNH BÃN HÃ€NG Táº I QUáº¦Y (FULLSCREEN POS)
    // ========================================================
    public function pos()
    {
        $db = (new Database())->getConnection();

        // 1. Láº¤Y Cáº¤U HÃŒNH Há»† THá»NG Äá»‚ FIX Lá»–I UNDEFINED VARIABLE
        $settings_db = [];
        try {
            $stmt_set = $db->query("SELECT setting_key, setting_value FROM settings");
            $settings_db = $stmt_set->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            // Bá» qua lá»—i náº¿u báº£ng settings khÃ´ng cÃ³ cáº¥u trÃºc key-value
        }

        // Náº¿u DB chÆ°a cÃ³ cáº¥u hÃ¬nh (Ä‘á» phÃ²ng cháº¡y má»›i), ta gÃ¡n giÃ¡ trá»‹ máº·c Ä‘á»‹nh Ä‘á»ƒ khÃ´ng bao giá» lá»—i
        if (empty($settings_db)) {
            $settings_db = [
                'pos_use_promo_code' => '1',
                'pos_auto_print' => '1',
                'pos_allow_price_edit' => '1',
                'pos_allow_negative_stock' => '0'
            ];
        }

        // 2. Láº¥y sáº£n pháº©m (BÆ¡m cá»™t giÃ¡ Ä‘Ãºng cá»§a báº¡n vÃ o Ä‘Ã¢y, vÃ­ dá»¥ base_price)
        $stmt_prod = $db->query("SELECT id, product_name, sku, base_price AS price FROM products WHERE parent_id IS NULL");
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // 3. Láº¥y khÃ¡ch hÃ ng
        $stmt_cust = $db->query("SELECT id, CONCAT(last_name, ' ', first_name) AS customer_name, phone FROM customers");
        $customers = $stmt_cust->fetchAll(PDO::FETCH_ASSOC);

        // --- ÄOáº N CODE Bá»” SUNG Láº¤Y PHÆ¯Æ NG THá»¨C THANH TOÃN ---
        $stmt_pm = $db->query("SELECT * FROM payment_methods WHERE is_active = 1");
        $payment_methods = $stmt_pm->fetchAll(PDO::FETCH_ASSOC);
        $payment_methods_json = json_encode($payment_methods);
        // -----------------------------------------------------

        $products_json = json_encode($products);
        $customers_json = json_encode($customers);



        // Gá»i ra giao diá»‡n POS Ä‘áº·c biá»‡t
        require_once __DIR__ . '/../views/order/pos.php';
    }

    // 1. MÃ€N HÃŒNH Táº O ÄÆ N HÃ€NG ONLINE (Táº¡i Admin)
    public function create()
    {
        $db = (new Database())->getConnection();

        // 1. Láº¥y danh sÃ¡ch sáº£n pháº©m (Theo cÃº phÃ¡p chuáº©n KhÆ°Æ¡ng Ä‘Ã£ sá»­a gÃ¡y bÃ i trÆ°á»›c)
        $query_products = "
            SELECT id, product_name, sku, base_price AS price, 100 as stock 
            FROM products WHERE parent_id IS NULL
        ";
        $stmt_prod = $db->query($query_products);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // 2. Láº¥y danh sÃ¡ch khÃ¡ch hÃ ng
        $stmt_cust = $db->query("SELECT id, CONCAT(last_name, ' ', first_name) AS customer_name, phone, address FROM customers");
        $customers = $stmt_cust->fetchAll(PDO::FETCH_ASSOC);

        // 3. Láº¥y nguá»“n Ä‘Æ¡n hÃ ng Ä‘á»™ng Ä‘ang hoáº¡t Ä‘á»™ng
        $stmt_src = $db->query("SELECT id, source_name FROM order_sources WHERE status = 'Äang sá»­ dá»¥ng' ORDER BY sort_order ASC, id ASC");
        $order_sources = $stmt_src->fetchAll(PDO::FETCH_ASSOC);

        // 4. Láº¤Y NHÃ‚N VIÃŠN PHá»¤ TRÃCH Äá»˜NG (Má»¥c 6.2)
        try {
            // Giáº£ sá»­ báº£ng quáº£n lÃ½ tÃ i khoáº£n/nhÃ¢n viÃªn cá»§a báº¡n tÃªn lÃ  users hoáº·c employees
            $stmt_users = $db->query("SELECT id, full_name FROM users WHERE role = 'NhÃ¢n viÃªn' OR role = 'Quáº£n lÃ½' OR 1=1");
            $employees = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Máº£ng dá»± phÃ²ng Ä‘á»™ng tá»« PHP Ä‘á»ƒ trÃ¡nh sáº­p trang náº¿u sai tÃªn báº£ng
            $employees = [['id' => 1, 'full_name' => 'BÃ¹i VÄƒn KhÆ°Æ¡ng'], ['id' => 2, 'full_name' => 'Tuáº¥n Anh (Kinh doanh)']];
        }

        // 5. Láº¤Y CHI NHÃNH Äá»˜NG (Má»¥c 6.1)
        try {
            $stmt_branches = $db->query("SELECT id, branch_name FROM branches");
            $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Máº£ng dá»± phÃ²ng Ä‘á»™ng tá»« PHP
            $branches = [['id' => 1, 'branch_name' => 'AKC Store - Chi nhÃ¡nh 1'], ['id' => 2, 'branch_name' => 'AKC Store - Showroom 2']];
        }

        // Ã‰p sang JSON cho cÃ¡c bá»™ lá»c tÃ¬m kiáº¿m Javascript nháº­n diá»‡n nhanh
        $products_json = json_encode($products);
        $customers_json = json_encode($customers);

        require_once __DIR__ . '/../views/order/create.php';
    }

    // 2. API TÃNH TIá»€N GIá»Ž HÃ€NG THÃ”NG MINH NGáº¦M (AJAX)
    public function calculate_api()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $cart_items = $data['cart_items'] ?? [];
        $promo_code = $data['promo_code'] ?? '';

        $subtotal = 0;

        // 1. Sá»¬A Lá»–I `NaN`: Bá»• sung tÃ­nh toÃ¡n ThÃ nh tiá»n cho Tá»ªNG sáº£n pháº©m
        foreach ($cart_items as &$item) {
            $item['original_price'] = $item['price'];
            $item['final_price'] = $item['price']; // Táº¡m thá»i chÆ°a cÃ³ giáº£m giÃ¡ Ä‘Ã¨ trÃªn tá»«ng SP

            // Ã‰p kiá»ƒu vá» Float/Int Ä‘á»ƒ cháº¯c cháº¯n khÃ´ng bá»‹ lá»—i chuá»—i
            $qty = (float)$item['qty'];
            $price = (float)$item['final_price'];

            $item['line_total'] = $price * $qty;
            $subtotal += $item['line_total'];
        }
        unset($item); // Cáº¯t tham chiáº¿u Ä‘á»ƒ an toÃ n bá»™ nhá»›

        $total_order_discount = 0;
        $msg = "";

        // 2. LOGIC Xá»¬ LÃ MÃƒ GIáº¢M GIÃ
        if (!empty($promo_code)) {
            $db = (new Database())->getConnection();

            // ÄÃ£ gá»¡ Ä‘iá»u kiá»‡n start_date vÃ  end_date Ä‘á»ƒ báº¡n dá»… Test dá»¯ liá»‡u cÅ©
            $stmt = $db->prepare("SELECT * FROM promotions WHERE promo_code = ? AND status = 'Äang Ã¡p dá»¥ng'");
            $stmt->execute([$promo_code]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($promo) {
                // Kiá»ƒm tra Ä‘iá»u kiá»‡n Ä‘Æ¡n hÃ ng tá»‘i thiá»ƒu
                if ($subtotal >= $promo['min_order_value']) {

                    if ($promo['discount_type'] == 'percent') {
                        $discount = $subtotal * ($promo['discount_value'] / 100);
                        if (!empty($promo['max_discount_amount']) && $promo['max_discount_amount'] > 0 && $discount > $promo['max_discount_amount']) {
                            $discount = $promo['max_discount_amount'];
                        }
                        $total_order_discount = $discount;
                    } else {
                        // Giáº£m tháº³ng tiá»n máº·t (amount)
                        $total_order_discount = $promo['discount_value'];
                    }

                    $msg = "Ãp dá»¥ng mÃ£ giáº£m giÃ¡ thÃ nh cÃ´ng!";
                } else {
                    $msg = "ÄÆ¡n hÃ ng chÆ°a Ä‘áº¡t giÃ¡ trá»‹ tá»‘i thiá»ƒu " . number_format($promo['min_order_value']) . "Ä‘";
                    $total_order_discount = 0;
                }
            } else {
                $msg = "MÃ£ khuyáº¿n mÃ£i khÃ´ng tá»“n táº¡i hoáº·c Ä‘Ã£ bá»‹ khÃ³a!";
                $total_order_discount = 0;
            }
        }

        $grand_total_before_tax = $subtotal - $total_order_discount;

        // 3. Tráº£ dá»¯ liá»‡u mÆ°á»£t mÃ  vá» cho Javascript
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
    // 3. API LÆ¯U ÄÆ N HÃ€NG VÃ€O DATABASE
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $cart_items = $data['cart_items'] ?? [];
        $summary = $data['summary'] ?? [];

        if (empty($cart_items)) {
            echo json_encode(['status' => 'error', 'msg' => 'Giá» hÃ ng Ä‘ang trá»‘ng! Vui lÃ²ng chá»n sáº£n pháº©m.']);
            exit;
        }

        $db = (new Database())->getConnection();

        try {
            // Báº¯t Ä‘áº§u giao dá»‹ch an toÃ n
            $db->beginTransaction();

            // Sinh mÃ£ Ä‘Æ¡n hÃ ng ngáº«u nhiÃªn
            $order_code = 'SON' . strtoupper(substr(uniqid(), -6));
            // Láº¥y thÃ´ng tin tá»« JS gá»­i lÃªn
            $payment_status = $data['payment_status'] ?? 'paid';
            $payment_method = $data['payment_method'] ?? 'cash'; // Tiá»n máº·t/Chuyá»ƒn khoáº£n
            $customer_id = !empty($data['customer_id']) ? $data['customer_id'] : null;
            $amount_paid = $data['amount_paid'] ?? 0; // Tiá»n khÃ¡ch Ä‘Æ°a

            // 1. LÆ¯U VÃ€O Báº¢NG CHÃNH (orders) - ThÃªm Thuáº¿, KhÃ¡ch Ä‘Æ°a, PhÆ°Æ¡ng thá»©c
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
                $summary['tax_amount'] ?? 0, // LÆ°u tiá»n thuáº¿
                $summary['grand_total'] ?? 0,
                $amount_paid, // LÆ°u tiá»n khÃ¡ch Ä‘Æ°a
                $payment_method, // HÃ¬nh thá»©c thanh toÃ¡n
                $payment_status
            ]);

            $order_id = $db->lastInsertId();

            // 2. LÆ¯U CHI TIáº¾T VÃ€O Báº¢NG (order_items) - ÄÃƒ Bá»” SUNG PROMO_DISCOUNT
            $query_item = "INSERT INTO order_items (
                order_id, product_id, product_name, sku, qty, 
                original_price, promo_discount, manual_discount, final_price, line_total, is_gift
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)";

            $stmt_item = $db->prepare($query_item);

            foreach ($cart_items as $item) {
                $is_gift = ($item['final_price'] == 0) ? 1 : 0;

                // Thuáº­t toÃ¡n tá»± tÃ­nh ra tiá»n Ä‘Ã£ giáº£m cá»§a tá»«ng mÃ³n (GiÃ¡ gá»‘c - GiÃ¡ cuá»‘i)
                $promo_discount = $item['price'] - $item['final_price'];

                $stmt_item->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['sku'],
                    $item['qty'],
                    $item['price'],
                    $promo_discount, // Truyá»n giÃ¡ trá»‹ vÃ o Ä‘Ã¢y Ä‘á»ƒ MySQL khÃ´ng bÃ¡o lá»—i
                    $item['final_price'],
                    $item['line_total'],
                    $is_gift
                ]);
            }

            // Chá»‘t giao dá»‹ch
            $db->commit();

            echo json_encode([
                'status' => 'success',
                'msg' => 'Táº¡o Ä‘Æ¡n hÃ ng thÃ nh cÃ´ng!',
                'order_code' => $order_code
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => 'Lá»—i há»‡ thá»‘ng: ' . $e->getMessage()]);
        }
        exit;
    }
    public function view()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        // 1. Láº¥y thÃ´ng tin chung cá»§a Ä‘Æ¡n hÃ ng + thÃ´ng tin khÃ¡ch hÃ ng
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

        // 2. Láº¥y danh sÃ¡ch sáº£n pháº©m náº±m trong Ä‘Æ¡n hÃ ng nÃ y
        $query_items = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt_items = $db->prepare($query_items);
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Láº¥y cáº¥u hÃ¬nh payment_methods Ä‘á»ƒ táº¡o mÃ£ QR
        $stmt_pm = $db->query("SELECT * FROM payment_methods WHERE is_active = 1");
        $payment_methods = $stmt_pm->fetchAll(PDO::FETCH_ASSOC);

        // Gá»i View hiá»ƒn thá»‹
        require_once __DIR__ . '/../views/order/detail.php';
    }
    // ========================================================
    // TÃNH NÄ‚NG IN HÃ“A ÄÆ N (MÃY IN NHIá»†T 80mm)
    // ========================================================
    public function print()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        // Láº¥y thÃ´ng tin Ä‘Æ¡n hÃ ng
        $query_order = "SELECT o.*, CONCAT(c.last_name, ' ', c.first_name) AS customer_name 
                        FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?";
        $stmt_order = $db->prepare($query_order);
        $stmt_order->execute([$id]);
        $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin Ä‘Æ¡n hÃ ng!");
        }

        // Láº¥y chi tiáº¿t sáº£n pháº©m
        $query_items = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt_items = $db->prepare($query_items);
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Gá»i View máº«u In HÃ³a ÄÆ¡n
        require_once __DIR__ . '/../views/order/print.php';
    }
    // ========================================================
    // 4. Xá»¬ LÃ GIAO HÃ€NG & Tá»° Äá»˜NG TRá»ª Tá»’N KHO
    // ========================================================
    public function update_shipping()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'msg' => 'PhÆ°Æ¡ng thá»©c khÃ´ng há»£p lá»‡']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);
        $status = $data['status'] ?? 'delivered'; // Máº·c Ä‘á»‹nh lÃ  giao thÃ nh cÃ´ng

        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // 1. Cáº­p nháº­t tráº¡ng thÃ¡i giao hÃ ng trong báº£ng orders
            $stmt = $db->prepare("UPDATE orders SET shipping_status = ?, order_status = 'completed' WHERE id = ?");
            $stmt->execute([$status, $order_id]);

            // 2. LOGIC TRá»ª Tá»’N KHO Tá»° Äá»˜NG (Náº¿u giao hÃ ng thÃ nh cÃ´ng)
            if ($status === 'delivered') {
                // Láº¥y ra cÃ¡c sáº£n pháº©m vÃ  sá»‘ lÆ°á»£ng trong Ä‘Æ¡n hÃ ng nÃ y
                $stmt_items = $db->prepare("SELECT product_id, qty FROM order_items WHERE order_id = ? AND is_gift = 0");
                $stmt_items->execute([$order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Tiáº¿n hÃ nh trá»« kho (Giáº£ sá»­ báº£ng products cá»§a báº¡n cÃ³ cá»™t stock hoáº·c quantity, báº¡n thay tháº¿ cho Ä‘Ãºng nhÃ©)
                // á»ž Ä‘Ã¢y mÃ¬nh táº¡m dÃ¹ng cá»™t stock. Náº¿u DB cá»§a báº¡n lÃ  quantity thÃ¬ Ä‘á»•i chá»¯ stock thÃ nh quantity nhÃ©.
                $stmt_update_stock = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

                foreach ($items as $item) {
                    $stmt_update_stock->execute([$item['qty'], $item['product_id']]);
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'msg' => 'Cáº­p nháº­t tráº¡ng thÃ¡i giao hÃ ng thÃ nh cÃ´ng, kho hÃ ng Ä‘Ã£ tá»± Ä‘á»™ng Ä‘á»“ng bá»™!']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => 'Lá»—i: ' . $e->getMessage()]);
        }
        exit;
    }

    // ========================================================
    // 5. XÃC NHáº¬N THANH TOÃN (THU TIá»€N COD/SAU)
    // ========================================================
    public function collect_payment()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);

        $db = (new Database())->getConnection();

        try {
            // Láº¥y ra sá»‘ tiá»n grand_total khÃ¡ch cáº§n tráº£ cá»§a Ä‘Æ¡n nÃ y
            $stmt_order = $db->prepare("SELECT grand_total FROM orders WHERE id = ?");
            $stmt_order->execute([$order_id]);
            $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                echo json_encode(['status' => 'error', 'msg' => 'KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n hÃ ng!']);
                exit;
            }

            // Cáº­p nháº­t: KhÃ¡ch Ä‘Ã£ tráº£ Ä‘á»§ tiá»n (amount_paid = grand_total) vÃ  chuyá»ƒn tráº¡ng thÃ¡i thÃ nh 'paid'
            $stmt_update = $db->prepare("UPDATE orders SET amount_paid = grand_total, payment_status = 'paid' WHERE id = ?");
            $stmt_update->execute([$order_id]);

            echo json_encode(['status' => 'success', 'msg' => 'XÃ¡c nháº­n thu tiá»n thÃ nh cÃ´ng! ÄÆ¡n hÃ ng Ä‘Ã£ chuyá»ƒn sang tráº¡ng thÃ¡i ÄÃ£ thanh toÃ¡n.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lá»—i: ' . $e->getMessage()]);
        }
        exit;
    }
    // ========================================================
    // 6. Há»¦Y ÄÆ N HÃ€NG & HOÃ€N Láº I Tá»’N KHO
    // ========================================================
    public function cancel()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($data['order_id'] ?? 0);

        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // 1. Kiá»ƒm tra tráº¡ng thÃ¡i hiá»‡n táº¡i cá»§a Ä‘Æ¡n hÃ ng
            $stmt = $db->prepare("SELECT order_status, shipping_status FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n hÃ ng!");
            }
            if ($order['order_status'] == 'cancelled') {
                throw new Exception("ÄÆ¡n hÃ ng nÃ y Ä‘Ã£ bá»‹ há»§y tá»« trÆ°á»›c rá»“i!");
            }

            // 2. Náº¾U ÄÃƒ XUáº¤T KHO (ÄÃ£ giao hÃ ng) -> PHáº¢I Cá»˜NG Láº I Tá»’N KHO TRÆ¯á»šC KHI Há»¦Y
            if ($order['shipping_status'] == 'delivered') {
                $stmt_items = $db->prepare("SELECT product_id, qty FROM order_items WHERE order_id = ? AND is_gift = 0");
                $stmt_items->execute([$order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Cáº­p nháº­t láº¡i kho (Cá»™ng tráº£ láº¡i sá»‘ lÆ°á»£ng). 
                // *LÆ°u Ã½: Náº¿u báº£ng products cá»§a báº¡n dÃ¹ng cá»™t quantity thÃ¬ Ä‘á»•i chá»¯ stock thÃ nh quantity nhÃ©
                $stmt_restore = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                foreach ($items as $item) {
                    $stmt_restore->execute([$item['qty'], $item['product_id']]);
                }
            }

            // 3. Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n hÃ ng thÃ nh ÄÃ£ há»§y (cancelled)
            $stmt_update = $db->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
            $stmt_update->execute([$order_id]);

            $db->commit();

            $msg = $order['shipping_status'] == 'delivered'
                ? 'ÄÃ£ há»§y Ä‘Æ¡n hÃ ng vÃ  há»‡ thá»‘ng Ä‘Ã£ Cá»˜NG TRáº¢ Láº I sá»‘ lÆ°á»£ng vÃ o kho!'
                : 'ÄÃ£ há»§y Ä‘Æ¡n hÃ ng thÃ nh cÃ´ng!';

            echo json_encode(['status' => 'success', 'msg' => $msg]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
        exit;
    }
    // ========================================================
    // 7. THÃŠM NHANH KHÃCH HÃ€NG Tá»ª MÃ€N HÃŒNH POS
    // ========================================================
    public function quick_add_customer()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lÃ²ng nháº­p Ä‘á»§ TÃªn vÃ  Sá»‘ Ä‘iá»‡n thoáº¡i!']);
            exit;
        }

        $db = (new Database())->getConnection();
        try {
            // TÃ¡ch tÃªn vÃ  há» (VÃ¬ báº£ng customers cá»§a báº¡n dÃ¹ng first_name vÃ  last_name)
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
            echo json_encode(['status' => 'error', 'msg' => 'Lá»—i DB: ' . $e->getMessage()]);
        }
        exit;
    }
    // Xá»­ lÃ½ LÆ°u Ä‘Æ¡n hÃ ng Online tá»« trang create.php gá»­i lÃªn
    public function store_online_order()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data || empty($data['cart_items'])) {
                echo json_encode(['status' => 'error', 'msg' => 'Dá»¯ liá»‡u khÃ´ng há»£p lá»‡.']);
                exit;
            }

            // Láº¥y tráº¡ng thÃ¡i lÆ°u Ä‘Æ¡n tá»« ngÆ°á»i dÃ¹ng
            $action_type = $data['action_type']; // 'draft', 'create', 'confirm', 'ship'

            // --- CHUYá»‚N Äá»”I ACTION_TYPE THÃ€NH TRáº NG THÃI CHUáº¨N Há»‡ thá»‘ng ---
            $order_status = 'pending';
            $shipping_status = 'pending';

            if ($action_type === 'draft') {
                $order_status = 'draft'; // ÄÆ¡n nhÃ¡p
            } elseif ($action_type === 'create') {
                $order_status = 'processing'; // Äang giao dá»‹ch
            } elseif ($action_type === 'confirm') {
                $order_status = 'confirmed'; // ÄÃ£ xÃ¡c nháº­n
            } elseif ($action_type === 'ship') {
                $order_status = 'confirmed';
                $shipping_status = 'shipping'; // Äang giao hÃ ng
            }

            // á»ž Ä‘Ã¢y sau nÃ y KhÆ°Æ¡ng sáº½ viáº¿t cÃ¡c lá»‡nh SQL:
            // 1. INSERT INTO orders (...)
            // 2. INSERT INTO order_items (...)
            // 3. Trá»« sá»‘ lÆ°á»£ng tá»“n kho (Náº¿u action_type khÃ¡c 'draft')

            echo json_encode([
                'status' => 'success',
                'msg' => 'ÄÃ£ lÆ°u Ä‘Æ¡n hÃ ng Online thÃ nh cÃ´ng vá»›i hÃ nh Ä‘á»™ng: ' . strtoupper($action_type)
            ]);
            exit;
        }
    }
    // 1. DANH SÃCH ÄÆ N HÃ€NG (TÃCH Há»¢P TÃŒM KIáº¾M ÄA LUá»’NG & Bá»˜ Lá»ŒC)
    public function index()
    {
        $db = (new Database())->getConnection();

        // Nháº­n tham sá»‘ tÃ¬m kiáº¿m & Lá»c
        $keyword = trim($_GET['keyword'] ?? '');
        $search_type = $_GET['search_type'] ?? 'all'; // all, order_code, phone
        $status = $_GET['status'] ?? 'all';
        $payment_status = $_GET['payment_status'] ?? 'all';
        $branch_id = $_GET['branch_id'] ?? 'all';

        // Náº¿u click vÃ o má»™t Tab LÆ°u bá»™ lá»c, parse JSON ra Ä‘á»ƒ ghi Ä‘Ã¨ GET
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

        // XÃ¢y dá»±ng cÃ¢u truy váº¥n Ä‘á»™ng
        $query = "SELECT o.*, b.branch_name FROM orders o LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
        $params = [];

        // 1. Xá»­ lÃ½ TÃ¬m kiáº¿m (Tá»« khÃ³a ngÄƒn cÃ¡ch bá»Ÿi dáº¥u pháº©y)
        if ($keyword !== '') {
            $keywords = explode(',', $keyword);
            $keyword_conditions = [];
            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if ($kw === '') continue;

                if ($search_type === 'order_code') {
                    $keyword_conditions[] = "o.order_code LIKE '%$kw%'";
                } elseif ($search_type === 'phone') {
                    $kw_phone = preg_replace('/[^0-9]/', '', $kw); // Lá»c kÃ½ tá»± thá»«a
                    $keyword_conditions[] = "o.phone LIKE '%$kw_phone%'";
                } else {
                    // Cháº¿ Ä‘á»™ ALL: Æ¯u tiÃªn quÃ©t qua nhiá»u trÆ°á»ng
                    $kw_clean = htmlspecialchars($kw);
                    $keyword_conditions[] = "(o.order_code LIKE '%$kw_clean%' OR o.phone LIKE '%$kw_clean%' OR o.customer_name LIKE '%$kw_clean%')";
                }
            }
            if (!empty($keyword_conditions)) {
                $query .= " AND (" . implode(' OR ', $keyword_conditions) . ")";
            }
        }

        // 2. Xá»­ lÃ½ Bá»™ lá»c Dropdown
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

        // Láº¥y danh sÃ¡ch Bá»™ lá»c Ä‘Ã£ lÆ°u & Chi nhÃ¡nh Ä‘á»ƒ Ä‘á»• ra giao diá»‡n
        $saved_filters = $db->query("SELECT * FROM saved_filters WHERE module_name = 'orders'")->fetchAll(PDO::FETCH_ASSOC);
        $branches = $db->query("SELECT id, branch_name FROM branches WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/order/list.php';
    }

    // 2. LÆ¯U Bá»˜ Lá»ŒC (Táº O TAB Má»šI)
    public function save_filter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filter_name = trim($_POST['filter_name']);
            // Gom cÃ¡c tham sá»‘ lá»c hiá»‡n táº¡i thÃ nh chuá»—i JSON
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
    // 3. GIAO HÃ€NG HÃ€NG LOáº T (Táº¡o Váº­n Ä‘Æ¡n Bulk)
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

                    // Kiá»ƒm tra Ä‘Æ¡n hÃ ng cÃ³ tá»“n táº¡i vÃ  chÆ°a giao khÃ´ng
                    $stmt_check = $db->prepare("SELECT order_code, shipping_status, grand_total, amount_paid FROM orders WHERE id = ?");
                    $stmt_check->execute([$order_id]);
                    $order = $stmt_check->fetch(PDO::FETCH_ASSOC);

                    if ($order && $order['shipping_status'] !== 'delivered') {
                        // TÃ­nh tiá»n COD = Tá»•ng bill - ÄÃ£ thanh toÃ¡n
                        $cod_amount = max(0, floatval($order['grand_total']) - floatval($order['amount_paid']));
                        $tracking_code = strtoupper($partner_code) . date('ymd') . rand(1000, 9999);

                        // 1. Táº¡o Váº­n Ä‘Æ¡n sang báº£ng shipments
                        $stmt_ship = $db->prepare("INSERT INTO shipments (order_id, branch_id, tracking_code, partner_code, status, cod_amount) VALUES (?, ?, ?, ?, 'pending', ?)");
                        $stmt_ship->execute([$order_id, $branch_id, $tracking_code, $partner_code, $cod_amount]);

                        // 2. Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n hÃ ng thÃ nh Delivering
                        $stmt_update = $db->prepare("UPDATE orders SET shipping_status = 'delivering', order_status = 'processing' WHERE id = ?");
                        $stmt_update->execute([$order_id]);

                        $success_count++;
                    }
                }

                $db->commit();
                header("Location: index.php?action=order_list&success_bulk=1&count=" . $success_count);
            } catch (Exception $e) {
                $db->rollBack();
                die("Lá»—i xá»­ lÃ½ giao hÃ ng: " . $e->getMessage());
            }
            exit;
        }
    }
    // 4. THAO TÃC HÃ€NG LOáº T (LÆ°u trá»¯, ÄÃ³ng gÃ³i, GÃ¡n nhÃ¢n viÃªn, Tags)
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
                                // Láº¥y tag cÅ© ra ná»‘i thÃªm tag má»›i
                                $stmt_get = $db->prepare("SELECT tags FROM orders WHERE id = ?");
                                $stmt_get->execute([$order_id]);
                                $old_tags = $stmt_get->fetchColumn();

                                $combined_tags = empty($old_tags) ? $new_tags : $old_tags . ',' . $new_tags;
                                // XÃ³a khoáº£ng tráº¯ng vÃ  loáº¡i bá» tag trÃ¹ng láº·p
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

                        // TÃNH NÄ‚NG Má»šI: PhÃ¡t hÃ nh HÃ³a Ä‘Æ¡n Ä‘iá»‡n tá»­ (VAT)
                        case 'issue_e_invoices':
                            $symbol = trim($_POST['invoice_symbol'] ?? '1C26TAA'); // Láº¥y kÃ½ hiá»‡u tá»« Modal

                            // Kiá»ƒm tra xem Ä‘Æ¡n nÃ y Ä‘Ã£ xuáº¥t HÄ chÆ°a
                            $check = $db->prepare("SELECT has_e_invoice FROM orders WHERE id = ?");
                            $check->execute([$order_id]);
                            if ($check->fetchColumn() == 0) {
                                // Sinh dá»¯ liá»‡u giáº£ láº­p (Mock Data)
                                $inv_no = '000' . rand(1000, 9999);
                                $cqt_code = strtoupper(uniqid('CQT-')); // VÃ­ dá»¥: CQT-64E2A8...

                                $stmt_inv = $db->prepare("INSERT INTO e_invoices (order_id, invoice_number, invoice_symbol, cqt_code) VALUES (?, ?, ?, ?)");
                                if ($stmt_inv->execute([$order_id, $inv_no, $symbol, $cqt_code])) {
                                    // ÄÃ¡nh dáº¥u Ä‘Æ¡n hÃ ng lÃ  Ä‘Ã£ xuáº¥t HÄ
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
                die("Lá»—i thao tÃ¡c hÃ ng loáº¡t: " . $e->getMessage());
            }
            exit;
        }
    }
    // 5. XUáº¤T FILE EXCEL / CSV ÄÆ N HÃ€NG
    public function export_orders()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $export_scope = $_POST['export_scope'] ?? 'all';
            $export_type = $_POST['export_type'] ?? 'summary';
            $export_ids = $_POST['export_ids'] ?? '';

            $db = (new Database())->getConnection();
            $query = "";

            if ($export_type === 'detailed') {
                // File chi tiáº¿t: JOIN vá»›i order_items Ä‘á»ƒ láº¥y tá»«ng sáº£n pháº©m
                $query = "SELECT o.order_code, o.created_at, o.customer_name, o.phone, b.branch_name, 
                                 oi.product_name, oi.sku, oi.qty, oi.final_price, oi.line_total, 
                                 o.grand_total, o.payment_status, o.order_status
                          FROM orders o 
                          LEFT JOIN order_items oi ON o.order_id = oi.order_id
                          LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
            } else {
                // File tá»•ng quan: Chá»‰ láº¥y thÃ´ng tin Ä‘Æ¡n máº¹
                $query = "SELECT o.order_code, o.created_at, o.customer_name, o.phone, b.branch_name, 
                                 o.subtotal, o.grand_total, o.amount_paid, 
                                 o.payment_status, o.order_status
                          FROM orders o 
                          LEFT JOIN branches b ON o.branch_id = b.id WHERE 1=1";
            }

            // Xá»­ lÃ½ pháº¡m vi xuáº¥t
            if ($export_scope === 'selected' && !empty($export_ids)) {
                // Chá»‘ng SQL Injection cho má»‡nh Ä‘á» IN
                $ids_array = array_map('intval', explode(',', $export_ids));
                $ids_str = implode(',', $ids_array);
                $query .= " AND o.id IN ($ids_str)";
            }

            $query .= " ORDER BY o.created_at DESC";
            $stmt = $db->query($query);

            // THIáº¾T Láº¬P HEADER Äá»‚ Táº¢I FILE DÆ¯á»šI Dáº NG CSV
            $filename = "Export_DonHang_" . date('Ymd_His') . ".csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);

            $output = fopen('php://output', 'w');

            // Ghi BOM Ä‘á»ƒ Excel nháº­n diá»‡n Ä‘Æ°á»£c Tiáº¿ng Viá»‡t cÃ³ dáº¥u (UTF-8)
            fputs($output, "\xEF\xBB\xBF");

            // XÃ¢y dá»±ng DÃ²ng TiÃªu Ä‘á» (Header)
            if ($export_type === 'detailed') {
                fputcsv($output, ['MÃ£ Ä‘Æ¡n hÃ ng', 'NgÃ y táº¡o', 'KhÃ¡ch hÃ ng', 'Äiá»‡n thoáº¡i', 'Chi nhÃ¡nh', 'TÃªn sáº£n pháº©m', 'MÃ£ SKU', 'Sá»‘ lÆ°á»£ng', 'ÄÆ¡n giÃ¡', 'ThÃ nh tiá»n SP', 'Tá»•ng bill', 'Thanh toÃ¡n', 'Tráº¡ng thÃ¡i']);
            } else {
                fputcsv($output, ['MÃ£ Ä‘Æ¡n hÃ ng', 'NgÃ y táº¡o', 'KhÃ¡ch hÃ ng', 'Äiá»‡n thoáº¡i', 'Chi nhÃ¡nh', 'Tá»•ng tiá»n hÃ ng', 'KhÃ¡ch pháº£i tráº£', 'ÄÃ£ thanh toÃ¡n', 'Tráº¡ng thÃ¡i TT', 'Tráº¡ng thÃ¡i ÄÆ¡n']);
            }

            // Ghi dá»¯ liá»‡u tá»«ng dÃ²ng
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Máº¹o cá»±c Ä‘á»‰nh: Bá»c ="098..." Ä‘á»ƒ Excel khÃ´ng xÃ³a máº¥t sá»‘ 0 á»Ÿ Ä‘áº§u Sá»‘ Ä‘iá»‡n thoáº¡i vÃ  MÃ£ Ä‘Æ¡n
                $row['order_code'] = '="' . $row['order_code'] . '"';
                $row['phone'] = '="' . ($row['phone'] ?? '') . '"';

                // Dá»‹ch thuáº­t tráº¡ng thÃ¡i
                $row['payment_status'] = ($row['payment_status'] == 'paid') ? 'ÄÃ£ thanh toÃ¡n' : 'ChÆ°a thanh toÃ¡n';
                if ($row['order_status'] == 'completed') $row['order_status'] = 'HoÃ n thÃ nh';
                elseif ($row['order_status'] == 'cancelled') $row['order_status'] = 'ÄÃ£ há»§y';
                elseif ($row['order_status'] == 'processing') $row['order_status'] = 'Äang xá»­ lÃ½';
                else $row['order_status'] = 'Chá» xá»­ lÃ½';

                fputcsv($output, $row);
            }

            fclose($output);
            exit; // Ngá»«ng thá»±c thi Ä‘á»ƒ khÃ´ng in ra mÃ£ HTML dÆ° thá»«a
        }
    }
}

