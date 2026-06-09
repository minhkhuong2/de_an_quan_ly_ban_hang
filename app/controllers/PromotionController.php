<?php
// Đường dẫn file: app/controllers/PromotionController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PromotionModel.php';

class PromotionController
{

    // 1. HIỂN THỊ DANH SÁCH KHUYẾN MẠI
    public function list()
    {
        $db = (new Database())->getConnection();
        $promoModel = new PromotionModel($db);

        // Hứng dữ liệu từ Bộ lọc trên thanh URL
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';

        // Đảm bảo tên biến ĐÚNG CHÍNH XÁC là $promotions để view list.php nhận được
        $promotions = $promoModel->getAllPromotions($search, $status, $type);

        // Tự động cập nhật trạng thái nếu chương trình quá hạn kết thúc
        $now = date('Y-m-d H:i:s');
        foreach ($promotions as &$p) {
            if ($p['no_end_date'] == 0 && $p['end_date'] < $now && $p['status'] != 'Kết thúc') {
                $db->prepare("UPDATE promotions SET status = 'Kết thúc' WHERE id = ?")->execute([$p['id']]);
                $p['status'] = 'Kết thúc';
            }
        }

        // Gọi view hiển thị danh sách
        require_once __DIR__ . '/../views/promotion/list.php';
    }

    // 2. THÊM MỚI CHƯƠNG TRÌNH KHUYẾN MẠI
    public function add()
    {
        $db = (new Database())->getConnection();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $promoModel = new PromotionModel($db);
            $promo_name = $_POST['promo_name'] ?? '';

            // Tự sinh mã ngẫu nhiên nếu người dùng để trống
            $promo_code = !empty($_POST['promo_code']) ? trim($_POST['promo_code']) : 'KM' . strtoupper(uniqid());
            $promo_type = $_POST['promo_type'] ?? 'discount_order';

            $start_date = $_POST['start_date'] . ' ' . ($_POST['start_time'] ?? '00:00:00');
            $no_end_date = isset($_POST['no_end_date']) ? 1 : 0;
            $end_date = $no_end_date ? '2099-12-31 23:59:59' : ($_POST['end_date'] . ' ' . ($_POST['end_time'] ?? '23:59:59'));

            $usage_limit = isset($_POST['unlimited_usage']) ? null : (int)$_POST['usage_limit'];
            $min_order = ($promo_type === 'gift_by_order' || $promo_type === 'discount_order') ? (float)str_replace(['.', ','], '', $_POST['min_order_value'] ?? 0) : 0;

            $d_value = ($promo_type === 'discount_order' || $promo_type === 'discount_product') ? (float)str_replace(['.', ','], '', $_POST['discount_value'] ?? 0) : 0;
            $discount_type = $_POST['discount_type'] ?? 'amount';

            // ĐÓNG GÓI JSON QUÀ TẶNG THEO QUY TẮC SAPO
            $gift_settings = null;
            if ($promo_type === 'gift_by_order') {
                $gift_settings = json_encode([
                    'apply_to_type' => $_POST['apply_order_condition'] ?? 'product',
                    'apply_to_values' => $_POST['apply_order_values'] ?? [],
                    'gift_product_ids' => $_POST['gift_order_product_ids'] ?? [],
                    'max_gift_qty' => (int)($_POST['max_gift_order_qty'] ?? 0)
                ]);
            } elseif ($promo_type === 'gift_by_product') {
                $gift_settings = json_encode([
                    'apply_to_type' => $_POST['apply_prod_condition'] ?? 'product',
                    'apply_to_values' => $_POST['apply_prod_values'] ?? [],
                    'buy_qty' => (int)($_POST['buy_qty'] ?? 0),
                    'gift_product_ids' => $_POST['gift_prod_product_ids'] ?? [],
                    'max_gift_qty' => (int)($_POST['max_gift_prod_qty'] ?? 0),
                    'apply_multiple' => isset($_POST['apply_multiple']) ? 1 : 0
                ]);
            }

            // Xử lý các bộ cấu hình nâng cao khác (Giờ vàng, phạm vi chi nhánh, tập khách hàng)
            $advanced_timing = isset($_POST['enable_advanced_time']) ? json_encode($_POST['advanced_time']) : null;
            $branch_scope = $_POST['branch_scope'] ?? 'all';
            $specific_branches = ($branch_scope === 'specific' && !empty($_POST['branches'])) ? json_encode($_POST['branches']) : null;
            $customer_scope = $_POST['customer_scope'] ?? 'all';
            $customer_conditions = ($customer_scope === 'specific') ? json_encode($_POST['customer_cond']) : null;

            // Xử lý 2 nút bấm: Nếu bấm "Lưu & Kích hoạt" -> Đang chạy. Bấm "Lưu" -> Chờ chạy.
            $status = isset($_POST['btn_save_active']) ? 'Đang chạy' : 'Chờ chạy';

            $query = "INSERT INTO promotions (
                promo_name, promo_code, promo_type, discount_type, discount_value, min_order_value, 
                start_date, end_date, status, usage_limit, description, no_end_date, 
                advanced_timing, branch_scope, specific_branches, customer_scope, customer_conditions, gift_settings
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($query);
            $stmt->execute([
                $promo_name,
                $promo_code,
                $promo_type,
                $discount_type,
                $d_value,
                $min_order,
                $start_date,
                $end_date,
                $status,
                $usage_limit,
                $_POST['description'] ?? '',
                $no_end_date,
                $advanced_timing,
                $branch_scope,
                $specific_branches,
                $customer_scope,
                $customer_conditions,
                $gift_settings
            ]);

            header("Location: index.php?action=promo_list&success=1");
            exit;
        }

        // --- ĐOẠN ĐỔ DỮ LIỆU ĐỂ TRUYỀN RA VIEW THÊM MỚI (Tránh lỗi báo đỏ Undefined Variable) ---
        require_once __DIR__ . '/../models/BranchModel.php';
        $branches = (new BranchModel($db))->getAllBranches();
        $products = $db->query("SELECT id, product_name, sku FROM products WHERE parent_id IS NULL ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT id, category_name FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $brands = $db->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ''")->fetchAll(PDO::FETCH_COLUMN);

        require_once __DIR__ . '/../views/promotion/add.php';
    }

    // 3. XỬ LÝ THAO TÁC HÀNG LOẠT (BẬT/TẮT/XÓA NHIỀU MÃ)
    public function bulkAction()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['promo_ids']) && !empty($_POST['action'])) {
            $db = (new Database())->getConnection();
            $promoModel = new PromotionModel($db);

            $promoModel->bulkAction($_POST['promo_ids'], $_POST['action']);

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

    // 5. CHỈNH SỬA CHƯƠNG TRÌNH KHUYẾN MẠI
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

            // Nếu chương trình chưa kích hoạt (Chờ chạy/Tạm dừng): Cho sửa tên
            if ($promo['status'] === 'Chờ chạy' || $promo['status'] === 'Tạm dừng') {
                $db->prepare("UPDATE promotions SET promo_name = ?, usage_limit = ?, end_date = ?, no_end_date = ? WHERE id = ?")
                    ->execute([$_POST['promo_name'], $usage_limit, $end_date, isset($_POST['no_end_date']) ? 1 : 0, $id]);
            } else {
                // Nếu đang chạy: Chỉ cho sửa Số lượng áp dụng và Ngày kết thúc theo luật Sapo
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
