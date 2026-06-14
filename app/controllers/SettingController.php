<?php
// Đường dẫn: app/controllers/SettingController.php
require_once __DIR__ . '/../../config/database.php';

class SettingController
{
    // Hiển thị giao diện Cấu hình POS
    public function pos_settings()
    {
        $db = (new Database())->getConnection();

        // Lấy toàn bộ cấu hình từ DB
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings_db = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Trả về mảng ['key' => 'value']

        require_once __DIR__ . '/../views/setting/pos.php';
    }

    // Lưu cấu hình khi bấm nút Cập nhật
    public function save_pos_settings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();

            // Danh sách các checkbox/toggle (Nếu không check thì POST sẽ không gửi lên, nên cần gán mặc định là 0)
            $toggles = [
                'pos_allow_negative_stock',
                'pos_suggest_amount',
                'pos_allow_price_edit',
                'pos_auto_promotions',
                'pos_use_promo_code',
                'pos_shift_management',
                'pos_cash_register',
                'pos_barcode_scale',
                'pos_preprint_invoice',
                'pos_force_full_payment',
                'pos_sapo_qr',
                'pos_auto_print',
                'pos_offline_mode'
            ];

            // Cập nhật các Toggle
            foreach ($toggles as $key) {
                $value = isset($_POST[$key]) ? '1' : '0';
                $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            // Cập nhật các Text/Select (Khổ in, số bản in, kiểu thanh toán)
            $texts = ['pos_payment_steps', 'pos_print_copies', 'pos_print_size'];
            foreach ($texts as $key) {
                if (isset($_POST[$key])) {
                    $value = $_POST[$key];
                    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $value, $value]);
                }
            }

            echo "<script>alert('Lưu cấu hình POS thành công!'); window.location.href='index.php?action=pos_settings';</script>";
        }
    }
}
