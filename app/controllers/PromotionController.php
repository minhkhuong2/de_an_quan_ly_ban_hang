<?php
// ÄÆ°á»ng dáº«n file: app/controllers/PromotionController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PromotionModel.php';

class PromotionController
{

    // 1. HIá»‚N THá»Š DANH SÃCH (CHUáº¨N V3)
    public function list()
    {
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        $hinh_thuc = $_GET['hinh_thuc'] ?? ''; // Láº¥y thÃªm bá»™ lá»c hÃ¬nh thá»©c

        $promotions = $promoModel->getAllPromotions($search, $status, $type, $hinh_thuc);

        // QuÃ©t quÃ¡ háº¡n -> Äá»•i thÃ nh "Ngá»«ng Ã¡p dá»¥ng"
        $now = date('Y-m-d H:i:s');
        foreach ($promotions as &$p) {
            if ($p['no_end_date'] == 0 && $p['end_date'] < $now && $p['status'] != 'Ngá»«ng Ã¡p dá»¥ng') {
                $db->prepare("UPDATE promotions SET status = 'Ngá»«ng Ã¡p dá»¥ng' WHERE id = ?")->execute([$p['id']]);
                $p['status'] = 'Ngá»«ng Ã¡p dá»¥ng';
            }
        }
        unset($p);
        require_once __DIR__ . '/../views/promotion/list.php';
    }

    // 2. THÃŠM Má»šI CHÆ¯Æ NG TRÃŒNH KHUYáº¾N Máº I (Báº¢N HOÃ€N THIá»†N)
    public function add()
    {
        $db = (new Database())->getConnection();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $promoModel = new PromotionModel($db);
            $promo_name = $_POST['promo_name'] ?? '';
            $is_coupon = isset($_POST['is_coupon']) ? 1 : 0;
            $promo_code = ($is_coupon && !empty($_POST['promo_code'])) ? trim($_POST['promo_code']) : null;
            $promo_type = $_POST['promo_type'] ?? 'discount_order';

            $start_date = $_POST['start_date'] . ' ' . ($_POST['start_time'] ?? '00:00:00');
            $no_end_date = isset($_POST['no_end_date']) ? 1 : 0;
            $end_date = $no_end_date ? '2099-12-31 23:59:59' : ($_POST['end_date'] . ' ' . ($_POST['end_time'] ?? '23:59:59'));

            $usage_limit = (!isset($_POST['has_usage_limit'])) ? null : (int)$_POST['usage_limit'];
            $once_per_customer = isset($_POST['once_per_customer']) ? 1 : 0;

            // Xá»­ lÃ½ GiÃ¡ trá»‹ giáº£m máº·c Ä‘á»‹nh
            $d_value = (float)str_replace(['.', ','], '', $_POST['discount_value'] ?? 0);
            $discount_type = $_POST['discount_type'] ?? 'amount';
            $max_discount_amount = !empty($_POST['max_discount_amount']) ? (float)str_replace(['.', ','], '', $_POST['max_discount_amount']) : null;

            // Xá»­ lÃ½ logic JSON tuá»³ theo Loáº¡i Khuyáº¿n Máº¡i
            $product_apply_settings = null;
            $gift_settings = null;
            $shipping_settings = null;

            if ($promo_type === 'discount_product') {
                $product_apply_settings = json_encode([
                    'apply_to' => $_POST['apply_to'] ?? 'all',
                    'product_ids' => $_POST['apply_product_ids'] ?? [],
                    'category_ids' => $_POST['apply_category_ids'] ?? []
                ]);
            } elseif ($promo_type === 'gift_by_product' || $promo_type === 'gift_by_order') {
                // ÄÃ³ng gÃ³i Mua X Táº·ng Y (Chuáº©n V3)
                $gift_settings = json_encode([
                    'buy_condition_type' => $_POST['buy_condition_type'] ?? 'qty',
                    'buy_min_value' => (float)str_replace(['.', ','], '', $_POST['buy_min_value'] ?? 0),
                    'buy_apply_to' => $_POST['buy_apply_to'] ?? 'all',
                    'buy_product_ids' => $_POST['buy_product_ids'] ?? [],
                    'buy_category_ids' => $_POST['buy_category_ids'] ?? [],

                    'get_qty' => (int)($_POST['get_qty'] ?? 1),
                    'get_apply_to' => $_POST['get_apply_to'] ?? 'product',
                    'get_product_ids' => $_POST['get_product_ids'] ?? [],
                    'get_category_ids' => $_POST['get_category_ids'] ?? [],
                    'get_discount_type' => $_POST['get_discount_type'] ?? 'free',
                    'get_discount_value' => (float)str_replace(['.', ','], '', $_POST['get_discount_value'] ?? 0),
                    'max_gift_applies' => (int)($_POST['max_gift_applies'] ?? 1)
                ]);
            } elseif ($promo_type === 'free_shipping') {
                // ÄÃ³ng gÃ³i Freeship (Chuáº©n V3)
                $max_discount_amount = !empty($_POST['shipping_max_discount']) ? (float)str_replace(['.', ','], '', $_POST['shipping_max_discount']) : null;
                $shipping_settings = json_encode([
                    'area_scope' => $_POST['shipping_area_scope'] ?? 'all',
                    'provinces' => $_POST['shipping_provinces'] ?? [],
                    'max_shipping_fee' => !empty($_POST['max_shipping_fee']) ? (float)str_replace(['.', ','], '', $_POST['max_shipping_fee']) : null
                ]);
            }

            $condition_type = $_POST['condition_type'] ?? 'none';
            $min_order = ($condition_type === 'min_amount' || $condition_type === 'min_product_amount') ? (float)str_replace(['.', ','], '', $_POST['min_order_value'] ?? 0) : 0;
            $min_qty = ($condition_type === 'min_qty') ? (int)($_POST['min_product_qty'] ?? 0) : 0;

            $sales_channels = !empty($_POST['sales_channels']) ? json_encode($_POST['sales_channels']) : json_encode(['pos', 'web']);
            $allowed_combinations = !empty($_POST['allowed_combinations']) ? json_encode($_POST['allowed_combinations']) : null;
            $apply_once_per_order = isset($_POST['apply_once_per_order']) ? 1 : 0;
            $now = date('Y-m-d H:i:s');
            $status = ($start_date > $now) ? 'ChÆ°a Ã¡p dá»¥ng' : 'Äang Ã¡p dá»¥ng';

            $query = "INSERT INTO promotions (
                promo_name, promo_code, promo_type, discount_type, discount_value, max_discount_amount, 
                min_order_value, min_product_qty, start_date, end_date, status, usage_limit, once_per_customer, 
                description, no_end_date, gift_settings, shipping_settings, sales_channels, allowed_combinations,
                apply_once_per_order, product_apply_settings
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($query);
            $stmt->execute([
                $promo_name,
                $promo_code,
                $promo_type,
                $discount_type,
                $d_value,
                $max_discount_amount,
                $min_order,
                $min_qty,
                $start_date,
                $end_date,
                $status,
                $usage_limit,
                $once_per_customer,
                $_POST['description'] ?? '',
                $no_end_date,
                $gift_settings,
                $shipping_settings,
                $sales_channels,
                $allowed_combinations,
                $apply_once_per_order,
                $product_apply_settings
            ]);

            header("Location: index.php?action=promo_list&success=1");
            exit;
        }

        require_once __DIR__ . '/../models/BranchModel.php';
        $branches = (new BranchModel($db))->getAllBranches();
        $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/promotion/add.php';
    }

    // Xá»¬ LÃ THAO TÃC HÃ€NG LOáº T (CHUáº¨N V3)
    public function bulkAction()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['promo_ids']) && !empty($_POST['action'])) {
            $db = (new Database())->getConnection();
            $promoModel = new PromotionModel($db);

            //  V3 dÃ¹ng tá»« "Tiáº¿p tá»¥c" vÃ  "Ngá»«ng"
            $action = $_POST['action'];
            if ($action === 'Tiáº¿p tá»¥c') $action = 'Äang Ã¡p dá»¥ng';
            if ($action === 'Ngá»«ng') $action = 'Ngá»«ng Ã¡p dá»¥ng';

            $promoModel->bulkAction($_POST['promo_ids'], $action);
            header("Location: index.php?action=promo_list&success_bulk=1");
            exit;
        }
        header("Location: index.php?action=promo_list");
        exit;
    }

    // 4. XEM CHI TIáº¾T KHUYáº¾N Máº I
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        $promo = $promoModel->getPromotionById($id);
        if (!$promo) {
            header("Location: index.php?action=promo_list");
            exit;
        }

        $applied_orders = [];
        if (!empty($promo['promo_code'])) {
            $applied_orders = $promoModel->getAppliedOrders($promo['promo_code']);
        }

        require_once __DIR__ . '/../views/promotion/detail.php';
    }

    // 5. CHá»ˆNH Sá»¬A CHÆ¯Æ NG TRÃŒNH KHUYáº¾N Máº I (CHUáº¨N V3 )
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);
        $promo = $promoModel->getPromotionById($id);

        if (!$promo) {
            header("Location: index.php?action=promo_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // FIX Lá»–I UNDEFINED VARIABLE
            $no_end_date = isset($_POST['no_end_date']) ? 1 : 0;

            $end_date = $no_end_date ? '2099-12-31 23:59:59' : ($_POST['end_date'] . ' ' . ($_POST['end_time'] ?? '23:59:59'));
            $usage_limit = isset($_POST['unlimited_usage']) ? null : (int)$_POST['usage_limit'];
            $once_per_customer = isset($_POST['once_per_customer']) ? 1 : 0;

            // KIá»‚M TRA TRáº NG THÃI THEO LUáº¬T Há»‡ thá»‘ng V3
            if ($promo['status'] === 'ChÆ°a Ã¡p dá»¥ng' || $promo['status'] === 'Ngá»«ng Ã¡p dá»¥ng') {
                $promo_name = $_POST['promo_name'] ?? '';
                $d_value = (float)str_replace(['.', ','], '', $_POST['discount_value'] ?? 0);
                $discount_type = $_POST['discount_type'] ?? 'amount';
                $max_discount_amount = !empty($_POST['max_discount_amount']) ? (float)str_replace(['.', ','], '', $_POST['max_discount_amount']) : null;

                $condition_type = $_POST['condition_type'] ?? 'none';
                $min_order = ($condition_type === 'min_amount' || $condition_type === 'min_product_amount') ? (float)str_replace(['.', ','], '', $_POST['min_order_value'] ?? 0) : 0;
                $min_qty = ($condition_type === 'min_qty') ? (int)($_POST['min_product_qty'] ?? 0) : 0;

                $product_apply_settings = null;
                $gift_settings = null;
                $shipping_settings = null;

                if ($promo['promo_type'] === 'discount_product') {
                    $product_apply_settings = json_encode([
                        'apply_to' => $_POST['apply_to'] ?? 'all',
                        'product_ids' => $_POST['apply_product_ids'] ?? [],
                        'category_ids' => $_POST['apply_category_ids'] ?? []
                    ]);
                } elseif ($promo['promo_type'] === 'gift_by_product' || $promo['promo_type'] === 'gift_by_order') {
                    $gift_settings = json_encode([
                        'buy_condition_type' => $_POST['buy_condition_type'] ?? 'qty',
                        'buy_min_value' => (float)str_replace(['.', ','], '', $_POST['buy_min_value'] ?? 0),
                        'buy_apply_to' => $_POST['buy_apply_to'] ?? 'all',
                        'buy_product_ids' => $_POST['buy_product_ids'] ?? [],
                        'buy_category_ids' => $_POST['buy_category_ids'] ?? [],
                        'get_qty' => (int)($_POST['get_qty'] ?? 1),
                        'get_apply_to' => $_POST['get_apply_to'] ?? 'product',
                        'get_product_ids' => $_POST['get_product_ids'] ?? [],
                        'get_category_ids' => $_POST['get_category_ids'] ?? [],
                        'get_discount_type' => $_POST['get_discount_type'] ?? 'free',
                        'get_discount_value' => (float)str_replace(['.', ','], '', $_POST['get_discount_value'] ?? 0),
                        'max_gift_applies' => (int)($_POST['max_gift_applies'] ?? 1)
                    ]);
                } elseif ($promo['promo_type'] === 'free_shipping') {
                    $max_discount_amount = !empty($_POST['shipping_max_discount']) ? (float)str_replace(['.', ','], '', $_POST['shipping_max_discount']) : null;
                    $shipping_settings = json_encode([
                        'area_scope' => $_POST['shipping_area_scope'] ?? 'all',
                        'provinces' => $_POST['shipping_provinces'] ?? [],
                        'max_shipping_fee' => !empty($_POST['max_shipping_fee']) ? (float)str_replace(['.', ','], '', $_POST['max_shipping_fee']) : null
                    ]);
                }

                $sales_channels = !empty($_POST['sales_channels']) ? json_encode($_POST['sales_channels']) : json_encode(['pos', 'web']);
                $allowed_combinations = !empty($_POST['allowed_combinations']) ? json_encode($_POST['allowed_combinations']) : null;
                $apply_once_per_order = isset($_POST['apply_once_per_order']) ? 1 : 0;

                $query = "UPDATE promotions SET 
                    promo_name = ?, discount_type = ?, discount_value = ?, max_discount_amount = ?, 
                    min_order_value = ?, min_product_qty = ?, end_date = ?, no_end_date = ?, 
                    usage_limit = ?, once_per_customer = ?, gift_settings = ?, shipping_settings = ?, 
                    sales_channels = ?, allowed_combinations = ?, apply_once_per_order = ?, product_apply_settings = ?
                    WHERE id = ?";

                $db->prepare($query)->execute([
                    $promo_name,
                    $discount_type,
                    $d_value,
                    $max_discount_amount,
                    $min_order,
                    $min_qty,
                    $end_date,
                    $no_end_date,
                    $usage_limit,
                    $once_per_customer,
                    $gift_settings ?? $promo['gift_settings'],
                    $shipping_settings ?? $promo['shipping_settings'],
                    $sales_channels,
                    $allowed_combinations,
                    $apply_once_per_order,
                    $product_apply_settings ?? $promo['product_apply_settings'],
                    $id
                ]);
            } else {
                // 2. ÄANG ÃP Dá»¤NG: CHá»ˆ ÄÆ¯á»¢C PHÃ‰P Sá»¬A Sá» LÆ¯á»¢NG VÃ€ NGÃ€Y Káº¾T THÃšC
                $query = "UPDATE promotions SET usage_limit = ?, end_date = ?, no_end_date = ? WHERE id = ?";
                $db->prepare($query)->execute([$usage_limit, $end_date, $no_end_date, $id]);
            }

            header("Location: index.php?action=view_promo&id=" . $id . "&success_edit=1");
            exit;
        }
    }

    // 6. SAO CHÃ‰P CHÆ¯Æ NG TRÃŒNH KHUYáº¾N Máº I (DUPLICATE)
    public function duplicate()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promo = (new PromotionModel($db))->getPromotionById($id);

        if ($promo) {
            // Chuáº©n bá»‹ dá»¯ liá»‡u má» (Pre-fill) Ä‘á»• ngÆ°á»£c láº¡i form add.php
            $promo['promo_name'] = $promo['promo_name'] . ' (Báº£n sao)';
            $promo['promo_code'] = ''; // Äá»ƒ trá»‘ng cho há»‡ thá»‘ng tá»± táº¡o mÃ£ má»›i

            require_once __DIR__ . '/../models/BranchModel.php';
            $branches = (new BranchModel($db))->getAllBranches();
            $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            $categories = $db->query("SELECT id, category_name FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            $brands = $db->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ''")->fetchAll(PDO::FETCH_COLUMN);

            require_once __DIR__ . '/../views/promotion/add.php';
            exit;
        }
        header("Location: index.php?action=promo_list");
        exit;
    }

    // 7. Cáº¤U HÃŒNH THUáº¬T TOÃN KHUYáº¾N Máº I Tá»”NG
    public function settings()
    {
        $db = (new Database())->getConnection();
        require_once __DIR__ . '/../models/SettingModel.php';
        $settingModel = new SettingModel($db);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $settingModel->updateSetting('promo_global_status', isset($_POST['promo_global_status']) ? '1' : '0');
            $settingModel->updateSetting('promo_stacking', $_POST['promo_stacking'] ?? 'single');
            $settingModel->updateSetting('promo_calc_method', $_POST['promo_calc_method'] ?? 'original');
            $settingModel->updateSetting('promo_coupon_enabled', isset($_POST['promo_coupon_enabled']) ? '1' : '0');

            header("Location: index.php?action=promo_settings&success=1");
            exit;
        }

        $settings = $settingModel->getAllSettings();
        require_once __DIR__ . '/../views/promotion/settings.php';
    }
    /**
     * THUáº¬T TOÃN TÃNH TIá»€N CHUáº¨N Há»‡ thá»‘ng  V3 (Báº¢N CHá»NG Lá»– Cá»˜NG Dá»’N)
     */
    public function calculateCartTotal($cart_items, $applied_promos, $original_shipping_fee)
    {
        $final_cart = [];
        $total_product_discount = 0;
        $total_order_discount = 0;
        $total_shipping_discount = 0;

        // =========================================================================
        // BÆ¯á»šC 1: ÃP Dá»¤NG GIáº¢M GIÃ Sáº¢N PHáº¨M (LUáº¬T "CHá»ˆ CHá»ŒN 1 Má»¨C GIáº¢M Tá»T NHáº¤T")
        // =========================================================================
        foreach ($cart_items as &$item) {
            $price_p1 = $item['price'];
            $best_discount_for_this_item = 0; // Biáº¿n lÆ°u má»©c giáº£m Tá»T NHáº¤T cho 1 sáº£n pháº©m

            foreach ($applied_promos as $promo) {
                if ($promo['promo_type'] == 'discount_product' && $this->isProductEligible($item['id'], $promo['product_apply_settings'])) {
                    $current_promo_discount = 0;

                    if ($promo['discount_type'] == 'percent') {
                        $current_promo_discount = floor(($price_p1 * $promo['discount_value']) / 100);
                        if (!empty($promo['max_discount_amount']) && $current_promo_discount > $promo['max_discount_amount']) {
                            $current_promo_discount = $promo['max_discount_amount'];
                        }
                    } else if ($promo['discount_type'] == 'amount') {
                        $current_promo_discount = min($promo['discount_value'], $price_p1);
                    }

                    // Há»‡ thá»‘ng RULE: So sÃ¡nh xem khuyáº¿n máº¡i nÃ y cÃ³ tá»‘t hÆ¡n khuyáº¿n máº¡i trÆ°á»›c Ä‘Ã³ khÃ´ng
                    if ($current_promo_discount > $best_discount_for_this_item) {
                        $best_discount_for_this_item = $current_promo_discount;
                    }
                }
            }

            // Chá»‰ Ã¡p dá»¥ng Má»©c giáº£m Tá»T NHáº¤T duy nháº¥t 1 láº§n cho sáº£n pháº©m nÃ y
            $item_discount_total = $best_discount_for_this_item * $item['qty'];
            $total_product_discount += $item_discount_total;

            $item['final_price'] = $price_p1 - $best_discount_for_this_item;
            $item['line_total'] = $item['final_price'] * $item['qty'];
        }
        unset($item);

        // =========================================================================
        // BÆ¯á»šC 2: MUA X Táº¶NG Y (Giá»¯ nguyÃªn thuáº­t toÃ¡n quÃ©t vÃ  táº·ng Y giÃ¡ cao nháº¥t)
        // =========================================================================
        foreach ($applied_promos as $promo) {
            if ($promo['promo_type'] == 'gift_by_product' || $promo['promo_type'] == 'gift_by_order') {
                $gift = json_decode($promo['gift_settings'], true);
                $x_count = 0;
                $y_candidates = [];

                foreach ($cart_items as $k => $item) {
                    if (in_array($item['id'], $gift['buy_product_ids']) || $gift['buy_apply_to'] == 'all') $x_count += $item['qty'];
                    if (in_array($item['id'], $gift['get_product_ids'])) {
                        for ($i = 0; $i < $item['qty']; $i++) $y_candidates[] = ['cart_index' => $k, 'price' => $item['final_price']];
                    }
                }

                $req_x = (int)$gift['buy_min_value'];
                $sets_earned = ($req_x > 0) ? floor($x_count / $req_x) : 1;
                if (!empty($gift['max_gift_applies']) && $sets_earned > $gift['max_gift_applies']) $sets_earned = $gift['max_gift_applies'];
                $total_y_to_give = $sets_earned * (int)$gift['get_qty'];

                usort($y_candidates, function ($a, $b) {
                    return $b['price'] <=> $a['price'];
                });

                $given_count = 0;
                foreach ($y_candidates as $candidate) {
                    if ($given_count >= $total_y_to_give) break;
                    $idx = $candidate['cart_index'];
                    $discount_for_this_y = 0;

                    if ($gift['get_discount_type'] == 'free') $discount_for_this_y = $candidate['price'];
                    elseif ($gift['get_discount_type'] == 'percent') $discount_for_this_y = floor(($candidate['price'] * $gift['get_discount_value']) / 100);
                    elseif ($gift['get_discount_type'] == 'amount') $discount_for_this_y = min($gift['get_discount_value'], $candidate['price']);

                    $total_product_discount += $discount_for_this_y;
                    $cart_items[$idx]['line_total'] -= $discount_for_this_y;
                    $given_count++;
                }
            }
        }

        $subtotal_after_products = array_sum(array_column($cart_items, 'line_total'));

        // =========================================================================
        // BÆ¯á»šC 3: GIáº¢M GIÃ ÄÆ N HÃ€NG (Há»‡ thá»‘ng cho phÃ©p Ã¡p dá»¥ng NHIá»€U chÆ°Æ¡ng trÃ¬nh cÃ¹ng lÃºc)
        // =========================================================================
        foreach ($applied_promos as $promo) {
            if ($promo['promo_type'] == 'discount_order') {
                if ($promo['discount_type'] == 'percent') {
                    $order_discount = floor(($subtotal_after_products * $promo['discount_value']) / 100);
                    if (!empty($promo['max_discount_amount']) && $order_discount > $promo['max_discount_amount']) {
                        $order_discount = $promo['max_discount_amount'];
                    }
                } else {
                    $order_discount = min($promo['discount_value'], $subtotal_after_products);
                }
                $total_order_discount += $order_discount;
            }
        }

        // Chá»‘t cháº·n cuá»‘i cÃ¹ng: KhÃ´ng Ä‘á»ƒ tiá»n giáº£m lá»›n hÆ¡n tiá»n hÃ ng
        $total_order_discount = min($total_order_discount, $subtotal_after_products);
        $subtotal_after_orders = $subtotal_after_products - $total_order_discount;

        // =========================================================================
        // BÆ¯á»šC 4: Váº¬N CHUYá»‚N (LUáº¬T "CHá»ˆ CHá»ŒN 1 MÃƒ FREESHIP Tá»T NHáº¤T")
        // =========================================================================
        $best_shipping_discount = 0; // Biáº¿n lÆ°u má»©c giáº£m Freeship lá»›n nháº¥t

        foreach ($applied_promos as $promo) {
            if ($promo['promo_type'] == 'free_shipping') {
                $ship = json_decode($promo['shipping_settings'], true);
                $max_fee_allowed = (!empty($ship['max_shipping_fee'])) ? $ship['max_shipping_fee'] : 999999999;

                if ($original_shipping_fee <= $max_fee_allowed) {
                    $max_ship_discount = !empty($promo['max_discount_amount']) ? $promo['max_discount_amount'] : $original_shipping_fee;
                    $current_shipping_discount = min($original_shipping_fee, $max_ship_discount);

                    // Há»‡ thá»‘ng RULE: Chá»‰ láº¥y mÃ£ Freeship cÃ³ lá»£i nháº¥t
                    if ($current_shipping_discount > $best_shipping_discount) {
                        $best_shipping_discount = $current_shipping_discount;
                    }
                }
            }
        }

        $total_shipping_discount = $best_shipping_discount;
        $final_shipping_fee = $original_shipping_fee - $total_shipping_discount;

        // =========================================================================
        // Tá»”NG Káº¾T
        // =========================================================================
        $grand_total = $subtotal_after_orders + $final_shipping_fee;

        return [
            'cart_items' => $cart_items,
            'summary' => [
                'total_product_discount' => $total_product_discount,
                'total_order_discount' => $total_order_discount,
                'total_shipping_discount' => $total_shipping_discount,
                'final_shipping_fee' => $final_shipping_fee,
                'grand_total' => max(0, $grand_total)
            ]
        ];
    }

    // HÃ m phá»¥ trá»£ cho BÆ°á»›c 1
    private function isProductEligible($product_id, $settings_json)
    {
        if (!$settings_json) return true;
        $settings = json_decode($settings_json, true);
        if ($settings['apply_to'] == 'all') return true;
        if ($settings['apply_to'] == 'product' && in_array($product_id, $settings['product_ids'])) return true;
        return false;
    }
    /**
     * HÃ€M ÃNH Xáº  LOáº I KHUYáº¾N Máº I RA CHUáº¨N Káº¾T Há»¢P
     */
    private function mapComboType($db_promo_type)
    {
        if ($db_promo_type == 'discount_product' || $db_promo_type == 'gift_by_product' || $db_promo_type == 'gift_by_order') {
            return 'product';
        }
        if ($db_promo_type == 'discount_order') {
            return 'order';
        }
        if ($db_promo_type == 'free_shipping') {
            return 'shipping';
        }
        return '';
    }

    /**
     * HÃ€M KIá»‚M TRA CHÃ‰O (MUTUAL CONSENT) THEO ÄÃšNG TÃ€I LIá»†U
     * Khuyáº¿n máº¡i A pháº£i cho phÃ©p loáº¡i cá»§a B, VÃ€ Khuyáº¿n máº¡i B pháº£i cho phÃ©p loáº¡i cá»§a A
     */
    private function checkMutualConsent($promoA, $promoB)
    {
        // Náº¿u lÃ  cÃ¹ng 1 khuyáº¿n máº¡i thÃ¬ bá» qua
        if ($promoA['id'] == $promoB['id']) return true;

        $typeA = $this->mapComboType($promoA['promo_type']);
        $typeB = $this->mapComboType($promoB['promo_type']);

        $comboA = json_decode($promoA['allowed_combinations'], true) ?? [];
        $comboB = json_decode($promoB['allowed_combinations'], true) ?? [];

        // A cÃ³ cho phÃ©p B khÃ´ng? VÃ€ B cÃ³ cho phÃ©p A khÃ´ng?
        $A_allows_B = in_array($typeB, $comboA);
        $B_allows_A = in_array($typeA, $comboB);

        return ($A_allows_B && $B_allows_A);
    }
    /**
     * HÃ€M TÃŒM NHÃ“M KHUYáº¾N Máº I Tá»T NHáº¤T (THE BEST VALID GROUP)
     */
    public function getBestPromoGroup($eligible_promos, $cart_items, $original_shipping_fee)
    {
        $valid_groups = [];

        // Thuáº­t toÃ¡n sinh cÃ¡c nhÃ³m (Äá»ƒ Ä‘Æ¡n giáº£n cho há»‡ thá»‘ng nhá», ta nhÃ³m theo cáº·p hoáº·c bá»™ 3)
        // Duyá»‡t qua tá»«ng promo lÃ m "Node trung tÃ¢m"
        foreach ($eligible_promos as $promoA) {
            $current_group = [$promoA]; // Khá»Ÿi táº¡o nhÃ³m cÃ³ chá»©a A

            // TÃ¬m cÃ¡c Promo khÃ¡c cÃ³ thá»ƒ chÆ¡i chung vá»›i Táº¤T Cáº¢ cÃ¡c thÃ nh viÃªn trong nhÃ³m hiá»‡n táº¡i
            foreach ($eligible_promos as $promoB) {
                if ($promoA['id'] == $promoB['id']) continue;

                $can_join_group = true;
                foreach ($current_group as $group_member) {
                    if (!$this->checkMutualConsent($group_member, $promoB)) {
                        $can_join_group = false;
                        break;
                    }
                }

                if ($can_join_group) {
                    $current_group[] = $promoB;
                }
            }

            // Sinh ra má»™t ID Ä‘áº¡i diá»‡n cho nhÃ³m (Ä‘á»ƒ trÃ¡nh trÃ¹ng láº·p nhÃ³m)
            $group_ids = array_column($current_group, 'id');
            sort($group_ids);
            $group_key = implode('_', $group_ids);

            $valid_groups[$group_key] = $current_group;
        }

        // TÃ­nh toÃ¡n thá»­ sá»‘ tiá»n giáº£m giÃ¡ cá»§a tá»«ng nhÃ³m Ä‘á»ƒ tÃ¬m ra "NhÃ  vÃ´ Ä‘á»‹ch"
        $best_group = [];
        $max_discount_value = -1;

        foreach ($valid_groups as $group) {
            // Gá»i cÃ¡i hÃ m calculateCartTotal() mÃ  mÃ¬nh lÃ m cho báº¡n á»Ÿ bÃ i trÆ°á»›c Ä‘á»ƒ tÃ­nh nhÃ¡p
            $calculation_result = $this->calculateCartTotal($cart_items, $group, $original_shipping_fee);

            // TÃ­nh tá»•ng tiá»n khÃ¡ch ÄÆ¯á»¢C GIáº¢M (CÃ ng nhiá»u cÃ ng tá»‘t)
            $summary = $calculation_result['summary'];
            $total_discount_for_this_group = $summary['total_product_discount']
                + $summary['total_order_discount']
                + $summary['total_shipping_discount'];

            if ($total_discount_for_this_group > $max_discount_value) {
                $max_discount_value = $total_discount_for_this_group;
                $best_group = $group; // Chá»‘t nhÃ³m nÃ y lÃ  nhÃ³m VÃ´ Äá»‹ch
            }
        }

        return $best_group; // Tráº£ vá» nhÃ³m khuyáº¿n máº¡i cÃ³ lá»£i nháº¥t cho khÃ¡ch hÃ ng
    }
    // HÃ€M HIá»‚N THá»Š CHI TIáº¾T
    public function view()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);
        $promo = $promoModel->getPromotionById($id);

        if (!$promo) {
            header("Location: index.php?action=promo_list");
            exit;
        }

        $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        $applied_orders = []; // Náº¿u sau nÃ y cÃ³ báº£ng Ä‘Æ¡n hÃ ng thÃ¬ query á»Ÿ Ä‘Ã¢y

        require_once __DIR__ . '/../views/promotion/detail.php';
    }

    // HÃ€M SAO CHÃ‰P (COPY) - ÄÃ‚Y LÃ€ HÃ€M Báº N ÄANG THIáº¾U
    public function copy()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);
        $promo = $promoModel->getPromotionById($id);

        if (!$promo) {
            header("Location: index.php?action=promo_list");
            exit;
        }

        // Äá»•i tÃªn vÃ  lÃ m má»›i MÃ£ KM cho báº£n sao
        $promo['promo_name'] = $promo['promo_name'] . ' (Báº£n sao)';
        if (!empty($promo['promo_code'])) {
            $promo['promo_code'] = 'KM' . strtoupper(substr(md5(uniqid()), 0, 6));
        }

        // Reset tráº¡ng thÃ¡i Ä‘á»ƒ khÃ´ng bá»‹ khÃ³a
        $promo['status'] = 'ChÆ°a Ã¡p dá»¥ng';
        $promo['used_count'] = 0;

        require_once __DIR__ . '/../models/BranchModel.php';
        $branches = (new BranchModel($db))->getAllBranches();
        $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT id, category_name FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Chuyá»ƒn sang trang add.php kÃ¨m theo dá»¯ liá»‡u má»“i
        require_once __DIR__ . '/../views/promotion/add.php';
    }
}

