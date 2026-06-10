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

            $usage_limit = isset($_POST['unlimited_usage']) ? null : (int)$_POST['usage_limit'];
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
            $status = 'Đang áp dụng';

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
            $end_date = isset($_POST['no_end_date']) ? '2099-12-31 23:59:59' : ($_POST['end_date'] . ' ' . ($_POST['end_time'] ?? '23:59:59'));
            $usage_limit = isset($_POST['unlimited_usage']) ? null : (int)$_POST['usage_limit'];

            // Nếu Chưa áp dụng / Ngừng áp dụng: Cho phép sửa Tên
            if ($promo['status'] === 'Chưa áp dụng' || $promo['status'] === 'Ngừng áp dụng') {
                $db->prepare("UPDATE promotions SET promo_name = ?, usage_limit = ?, end_date = ?, no_end_date = ? WHERE id = ?")
                    ->execute([$_POST['promo_name'], $usage_limit, $end_date, isset($_POST['no_end_date']) ? 1 : 0, $id]);
            } else {
                // Nếu Đang áp dụng: Sapo OmniAI chỉ cho sửa Số lượng và Ngày kết thúc
                $db->prepare("UPDATE promotions SET usage_limit = ?, end_date = ?, no_end_date = ? WHERE id = ?")
                    ->execute([$usage_limit, $end_date, isset($_POST['no_end_date']) ? 1 : 0, $id]);
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
}
