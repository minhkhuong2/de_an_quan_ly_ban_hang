<?php
// Đường dẫn file: app/controllers/PromotionController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PromotionModel.php';

class PromotionController
{

    // 1. HIỂN THỊ DANH SÁCH (CHUẨN V3)
    public function list()
    {
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        $hinh_thuc = $_GET['hinh_thuc'] ?? ''; // Lấy thêm bộ lọc hình thức

        $promotions = $promoModel->getAllPromotions($search, $status, $type, $hinh_thuc);

        // Quét quá hạn -> Đổi thành "Ngừng áp dụng"
        $now = date('Y-m-d H:i:s');
        foreach ($promotions as &$p) {
            if ($p['no_end_date'] == 0 && $p['end_date'] < $now && $p['status'] != 'Ngừng áp dụng') {
                $db->prepare("UPDATE promotions SET status = 'Ngừng áp dụng' WHERE id = ?")->execute([$p['id']]);
                $p['status'] = 'Ngừng áp dụng';
            }
        }
        unset($p);
        require_once __DIR__ . '/../views/promotion/list.php';
    }

    // 2. THÊM MỚI CHƯƠNG TRÌNH KHUYẾN MẠI (BẢN HOÀN THIỆN)
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

            // Xử lý Giá trị giảm mặc định
            $d_value = (float)str_replace(['.', ','], '', $_POST['discount_value'] ?? 0);
            $discount_type = $_POST['discount_type'] ?? 'amount';
            $max_discount_amount = !empty($_POST['max_discount_amount']) ? (float)str_replace(['.', ','], '', $_POST['max_discount_amount']) : null;

            // Xử lý logic JSON tuỳ theo Loại Khuyến Mại
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
                // Đóng gói Mua X Tặng Y (Chuẩn V3)
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
                // Đóng gói Freeship (Chuẩn V3)
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
            $status = ($start_date > $now) ? 'Chưa áp dụng' : 'Đang áp dụng';

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

    // XỬ LÝ THAO TÁC HÀNG LOẠT (CHUẨN V3)
    public function bulkAction()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['promo_ids']) && !empty($_POST['action'])) {
            $db = (new Database())->getConnection();
            $promoModel = new PromotionModel($db);

            // OmniAI V3 dùng từ "Tiếp tục" và "Ngừng"
            $action = $_POST['action'];
            if ($action === 'Tiếp tục') $action = 'Đang áp dụng';
            if ($action === 'Ngừng') $action = 'Ngừng áp dụng';

            $promoModel->bulkAction($_POST['promo_ids'], $action);
            header("Location: index.php?action=promo_list&success_bulk=1");
            exit;
        }
        header("Location: index.php?action=promo_list");
        exit;
    }

    // 4. XEM CHI TIẾT KHUYẾN MẠI
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

    // 5. CHỈNH SỬA CHƯƠNG TRÌNH KHUYẾN MẠI (CHUẨN V3 OMNIAI)
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
            // FIX LỖI UNDEFINED VARIABLE
            $no_end_date = isset($_POST['no_end_date']) ? 1 : 0;

            $end_date = $no_end_date ? '2099-12-31 23:59:59' : ($_POST['end_date'] . ' ' . ($_POST['end_time'] ?? '23:59:59'));
            $usage_limit = isset($_POST['unlimited_usage']) ? null : (int)$_POST['usage_limit'];
            $once_per_customer = isset($_POST['once_per_customer']) ? 1 : 0;

            // KIỂM TRA TRẠNG THÁI THEO LUẬT SAPO V3
            if ($promo['status'] === 'Chưa áp dụng' || $promo['status'] === 'Ngừng áp dụng') {
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
                // 2. ĐANG ÁP DỤNG: CHỈ ĐƯỢC PHÉP SỬA SỐ LƯỢNG VÀ NGÀY KẾT THÚC
                $query = "UPDATE promotions SET usage_limit = ?, end_date = ?, no_end_date = ? WHERE id = ?";
                $db->prepare($query)->execute([$usage_limit, $end_date, $no_end_date, $id]);
            }

            header("Location: index.php?action=view_promo&id=" . $id . "&success_edit=1");
            exit;
        }
    }

    // 6. SAO CHÉP CHƯƠNG TRÌNH KHUYẾN MẠI (DUPLICATE)
    public function duplicate()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $promo = (new PromotionModel($db))->getPromotionById($id);

        if ($promo) {
            // Chuẩn bị dữ liệu mờ (Pre-fill) đổ ngược lại form add.php
            $promo['promo_name'] = $promo['promo_name'] . ' (Bản sao)';
            $promo['promo_code'] = ''; // Để trống cho hệ thống tự tạo mã mới

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

    // 7. CẤU HÌNH THUẬT TOÁN KHUYẾN MẠI TỔNG
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
     * THUẬT TOÁN TÍNH TIỀN CHUẨN SAPO OMNIAI V3 (BẢN CHỐNG LỖ CỘNG DỒN)
     */
    public function calculateCartTotal($cart_items, $applied_promos, $original_shipping_fee)
    {
        $final_cart = [];
        $total_product_discount = 0;
        $total_order_discount = 0;
        $total_shipping_discount = 0;

        // =========================================================================
        // BƯỚC 1: ÁP DỤNG GIẢM GIÁ SẢN PHẨM (LUẬT "CHỈ CHỌN 1 MỨC GIẢM TỐT NHẤT")
        // =========================================================================
        foreach ($cart_items as &$item) {
            $price_p1 = $item['price'];
            $best_discount_for_this_item = 0; // Biến lưu mức giảm TỐT NHẤT cho 1 sản phẩm

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

                    // SAPO RULE: So sánh xem khuyến mại này có tốt hơn khuyến mại trước đó không
                    if ($current_promo_discount > $best_discount_for_this_item) {
                        $best_discount_for_this_item = $current_promo_discount;
                    }
                }
            }

            // Chỉ áp dụng Mức giảm TỐT NHẤT duy nhất 1 lần cho sản phẩm này
            $item_discount_total = $best_discount_for_this_item * $item['qty'];
            $total_product_discount += $item_discount_total;

            $item['final_price'] = $price_p1 - $best_discount_for_this_item;
            $item['line_total'] = $item['final_price'] * $item['qty'];
        }
        unset($item);

        // =========================================================================
        // BƯỚC 2: MUA X TẶNG Y (Giữ nguyên thuật toán quét và tặng Y giá cao nhất)
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
        // BƯỚC 3: GIẢM GIÁ ĐƠN HÀNG (Sapo cho phép áp dụng NHIỀU chương trình cùng lúc)
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

        // Chốt chặn cuối cùng: Không để tiền giảm lớn hơn tiền hàng
        $total_order_discount = min($total_order_discount, $subtotal_after_products);
        $subtotal_after_orders = $subtotal_after_products - $total_order_discount;

        // =========================================================================
        // BƯỚC 4: VẬN CHUYỂN (LUẬT "CHỈ CHỌN 1 MÃ FREESHIP TỐT NHẤT")
        // =========================================================================
        $best_shipping_discount = 0; // Biến lưu mức giảm Freeship lớn nhất

        foreach ($applied_promos as $promo) {
            if ($promo['promo_type'] == 'free_shipping') {
                $ship = json_decode($promo['shipping_settings'], true);
                $max_fee_allowed = (!empty($ship['max_shipping_fee'])) ? $ship['max_shipping_fee'] : 999999999;

                if ($original_shipping_fee <= $max_fee_allowed) {
                    $max_ship_discount = !empty($promo['max_discount_amount']) ? $promo['max_discount_amount'] : $original_shipping_fee;
                    $current_shipping_discount = min($original_shipping_fee, $max_ship_discount);

                    // SAPO RULE: Chỉ lấy mã Freeship có lợi nhất
                    if ($current_shipping_discount > $best_shipping_discount) {
                        $best_shipping_discount = $current_shipping_discount;
                    }
                }
            }
        }

        $total_shipping_discount = $best_shipping_discount;
        $final_shipping_fee = $original_shipping_fee - $total_shipping_discount;

        // =========================================================================
        // TỔNG KẾT
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

    // Hàm phụ trợ cho Bước 1
    private function isProductEligible($product_id, $settings_json)
    {
        if (!$settings_json) return true;
        $settings = json_decode($settings_json, true);
        if ($settings['apply_to'] == 'all') return true;
        if ($settings['apply_to'] == 'product' && in_array($product_id, $settings['product_ids'])) return true;
        return false;
    }
    /**
     * HÀM ÁNH XẠ LOẠI KHUYẾN MẠI RA CHUẨN KẾT HỢP
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
     * HÀM KIỂM TRA CHÉO (MUTUAL CONSENT) THEO ĐÚNG TÀI LIỆU
     * Khuyến mại A phải cho phép loại của B, VÀ Khuyến mại B phải cho phép loại của A
     */
    private function checkMutualConsent($promoA, $promoB)
    {
        // Nếu là cùng 1 khuyến mại thì bỏ qua
        if ($promoA['id'] == $promoB['id']) return true;

        $typeA = $this->mapComboType($promoA['promo_type']);
        $typeB = $this->mapComboType($promoB['promo_type']);

        $comboA = json_decode($promoA['allowed_combinations'], true) ?? [];
        $comboB = json_decode($promoB['allowed_combinations'], true) ?? [];

        // A có cho phép B không? VÀ B có cho phép A không?
        $A_allows_B = in_array($typeB, $comboA);
        $B_allows_A = in_array($typeA, $comboB);

        return ($A_allows_B && $B_allows_A);
    }
    /**
     * HÀM TÌM NHÓM KHUYẾN MẠI TỐT NHẤT (THE BEST VALID GROUP)
     */
    public function getBestPromoGroup($eligible_promos, $cart_items, $original_shipping_fee)
    {
        $valid_groups = [];

        // Thuật toán sinh các nhóm (Để đơn giản cho hệ thống nhỏ, ta nhóm theo cặp hoặc bộ 3)
        // Duyệt qua từng promo làm "Node trung tâm"
        foreach ($eligible_promos as $promoA) {
            $current_group = [$promoA]; // Khởi tạo nhóm có chứa A

            // Tìm các Promo khác có thể chơi chung với TẤT CẢ các thành viên trong nhóm hiện tại
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

            // Sinh ra một ID đại diện cho nhóm (để tránh trùng lặp nhóm)
            $group_ids = array_column($current_group, 'id');
            sort($group_ids);
            $group_key = implode('_', $group_ids);

            $valid_groups[$group_key] = $current_group;
        }

        // Tính toán thử số tiền giảm giá của từng nhóm để tìm ra "Nhà vô địch"
        $best_group = [];
        $max_discount_value = -1;

        foreach ($valid_groups as $group) {
            // Gọi cái hàm calculateCartTotal() mà mình làm cho bạn ở bài trước để tính nháp
            $calculation_result = $this->calculateCartTotal($cart_items, $group, $original_shipping_fee);

            // Tính tổng tiền khách ĐƯỢC GIẢM (Càng nhiều càng tốt)
            $summary = $calculation_result['summary'];
            $total_discount_for_this_group = $summary['total_product_discount']
                + $summary['total_order_discount']
                + $summary['total_shipping_discount'];

            if ($total_discount_for_this_group > $max_discount_value) {
                $max_discount_value = $total_discount_for_this_group;
                $best_group = $group; // Chốt nhóm này là nhóm Vô Địch
            }
        }

        return $best_group; // Trả về nhóm khuyến mại có lợi nhất cho khách hàng
    }
    // HÀM HIỂN THỊ CHI TIẾT
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
        $applied_orders = []; // Nếu sau này có bảng đơn hàng thì query ở đây

        require_once __DIR__ . '/../views/promotion/detail.php';
    }

    // HÀM SAO CHÉP (COPY) - ĐÂY LÀ HÀM BẠN ĐANG THIẾU
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

        // Đổi tên và làm mới Mã KM cho bản sao
        $promo['promo_name'] = $promo['promo_name'] . ' (Bản sao)';
        if (!empty($promo['promo_code'])) {
            $promo['promo_code'] = 'KM' . strtoupper(substr(md5(uniqid()), 0, 6));
        }

        // Reset trạng thái để không bị khóa
        $promo['status'] = 'Chưa áp dụng';
        $promo['used_count'] = 0;

        require_once __DIR__ . '/../models/BranchModel.php';
        $branches = (new BranchModel($db))->getAllBranches();
        $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT id, category_name FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Chuyển sang trang add.php kèm theo dữ liệu mồi
        require_once __DIR__ . '/../views/promotion/add.php';
    }
}
