<?php
// Đường dẫn: app/controllers/SettingController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SettingModel.php';

class SettingController
{

    // 1. Hiển thị Trang Lưới Cấu hình (Hub)
    public function index()
    {
        require_once __DIR__ . '/../views/setting/index.php';
    }

    // 2. Xử lý Form Cấu hình chung (Tên, SĐT, Địa chỉ shop...)
    public function general()
    {
        $db = (new Database())->getConnection();
        $settingModel = new SettingModel($db);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Lặp qua mảng POST để cập nhật từng giá trị
            foreach ($_POST as $key => $value) {
                // Chỉ xử lý các key bắt đầu bằng store_ để bảo mật
                if (strpos($key, 'store_') === 0) {
                    $settingModel->updateSetting($key, $value);
                }
            }
            header("Location: index.php?action=general_settings&success=1");
            exit;
        }

        // Lấy dữ liệu cũ hiển thị lên Form
        $settings = $settingModel->getAllSettings();
        require_once __DIR__ . '/../views/setting/general.php';
    }
}
