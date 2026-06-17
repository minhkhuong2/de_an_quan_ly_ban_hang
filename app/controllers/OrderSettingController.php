<?php
require_once __DIR__ . '/../../config/database.php';

class OrderSettingController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Lấy tất cả cấu hình và biến nó thành 1 mảng Key-Value dễ dùng
    private function getSettings()
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM system_settings");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // Hiển thị trang cấu hình
    public function index()
    {
        $settings = $this->getSettings();
        // Giải mã JSON phần cấu hình wave picking nâng cao
        $advanced = isset($settings['advanced_wave_picking']) ? json_decode($settings['advanced_wave_picking'], true) : [];

        require_once __DIR__ . '/../views/setting/order_settings.php';
    }

    // Lưu cấu hình xuống Database
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đóng gói cấu hình nâng cao thành JSON
            $advanced_settings = json_encode([
                'scan_shelf' => isset($_POST['scan_shelf']) ? 1 : 0,
                'scan_item_pick' => isset($_POST['scan_item_pick']) ? 1 : 0,
                'scan_item_pack' => isset($_POST['scan_item_pack']) ? 1 : 0,
                'strict_wave' => isset($_POST['strict_wave']) ? 1 : 0
            ]);

            // Các cấu hình bật/tắt (Checkbox không check sẽ không có trong POST)
            $allow_negative = isset($_POST['allow_negative_sale_warning']) ? '1' : '0';
            $auto_archive = isset($_POST['auto_archive_order']) ? '1' : '0';
            $auto_delete_txn = isset($_POST['auto_delete_transaction']) ? '1' : '0';

            // Mảng dữ liệu cần cập nhật
            $updates = [
                'order_workflow' => $_POST['order_workflow'],
                'advanced_wave_picking' => $advanced_settings,
                'allow_negative_sale_warning' => $allow_negative,
                'auto_archive_order' => $auto_archive,
                'auto_delete_transaction' => $auto_delete_txn,
                'reminder_email_hours' => $_POST['reminder_email_hours']
            ];

            // Chạy vòng lặp Update (Hoặc Insert nếu chưa có)
            $stmt = $this->db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            foreach ($updates as $key => $value) {
                $stmt->execute([$key, $value]);
            }

            header("Location: index.php?action=order_settings&success=1");
            exit;
        }
    }
}
