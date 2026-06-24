<?php
// Đường dẫn: app/controllers/ShippingController.php
require_once __DIR__ . '/../../config/database.php';

class ShippingController
{
    // 1. GIAO DIỆN CẤU HÌNH VẬN CHUYỂN
    public function index()
    {
        $db = (new Database())->getConnection();

        // Lấy cấu hình gói hàng
        $stmt_set = $db->query("SELECT * FROM shipping_settings WHERE id = 1");
        $setting = $stmt_set->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách chi nhánh để hiển thị trong Dropdown tạo phí
        $stmt_branches = $db->query("SELECT id, branch_name FROM branches WHERE status = 'active'");
        $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách các biểu phí đã tạo, join với bảng chi nhánh để lấy tên
        $stmt_rates = $db->query("
            SELECT r.*, b.branch_name 
            FROM shipping_rates r 
            JOIN branches b ON r.branch_id = b.id 
            ORDER BY r.created_at DESC
        ");
        $rates = $stmt_rates->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/settings/shipping_settings.php';
    }

    // 2. LƯU CẤU HÌNH GÓI HÀNG MẶC ĐỊNH
    public function update_package()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $weight_mode = $_POST['weight_mode'] ?? 'product';
            $default_weight = intval($_POST['default_weight'] ?? 0);
            $length = intval($_POST['length'] ?? 10);
            $width = intval($_POST['width'] ?? 10);
            $height = intval($_POST['height'] ?? 10);
            $delivery_requirement = $_POST['delivery_requirement'] ?? 'check_no_try';
            $auto_sync_return = isset($_POST['auto_sync_return']) ? 1 : 0;

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("
                UPDATE shipping_settings SET 
                    weight_mode=?, default_weight=?, length=?, width=?, height=?, 
                    delivery_requirement=?, auto_sync_return=? 
                WHERE id=1
            ");
            $stmt->execute([$weight_mode, $default_weight, $length, $width, $height, $delivery_requirement, $auto_sync_return]);

            header("Location: index.php?action=shipping_settings&success_pkg=1");
            exit;
        }
    }

    // 3. THÊM BIỂU PHÍ VẬN CHUYỂN
    public function add_rate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();

            $branch_id = intval($_POST['branch_id']);
            $zone_name = trim($_POST['zone_name']);
            $provinces = trim($_POST['provinces']);
            $rate_type = $_POST['rate_type'] ?? 'custom';
            $rate_name = trim($_POST['rate_name']);
            $estimated_time = trim($_POST['estimated_time']);

            // Xử lý điều kiện
            $min_order_value = !empty($_POST['min_order_value']) ? floatval($_POST['min_order_value']) : null;
            $max_order_value = !empty($_POST['max_order_value']) ? floatval($_POST['max_order_value']) : null;

            if ($rate_type === 'custom') {
                $base_fee = floatval($_POST['base_fee'] ?? 0);
                $partner_code = null;
                $handling_fee_type = 'amount';
                $handling_fee_value = 0;
            } else {
                $base_fee = 0;
                $partner_code = $_POST['partner_code'] ?? 'ghn';
                $handling_fee_type = $_POST['handling_fee_type'] ?? 'amount';
                $handling_fee_value = floatval($_POST['handling_fee_value'] ?? 0);
            }

            $stmt = $db->prepare("
                INSERT INTO shipping_rates 
                (branch_id, zone_name, provinces, rate_type, rate_name, base_fee, partner_code, handling_fee_type, handling_fee_value, estimated_time, min_order_value, max_order_value) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$branch_id, $zone_name, $provinces, $rate_type, $rate_name, $base_fee, $partner_code, $handling_fee_type, $handling_fee_value, $estimated_time, $min_order_value, $max_order_value]);

            header("Location: index.php?action=shipping_settings&success_rate=1");
            exit;
        }
    }

    // 4. XÓA BIỂU PHÍ
    public function delete_rate()
    {
        $id = intval($_GET['id'] ?? 0);
        $db = (new Database())->getConnection();
        $db->prepare("DELETE FROM shipping_rates WHERE id = ?")->execute([$id]);
        header("Location: index.php?action=shipping_settings&success_del=1");
        exit;
    }
}
