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
            // Danh sách các trường thông tin chuẩn Sapo
            $allowed_keys = [
                'store_name',
                'business_name',
                'store_phone',
                'store_address',
                'store_country',
                'store_province',
                'admin_email',
                'notification_email',
                'inventory_type',
                'costing_method' // <--- Thêm dòng này
            ];

            // Lưu các trường văn bản
            foreach ($allowed_keys as $key) {
                if (isset($_POST[$key])) {
                    $settingModel->updateSetting($key, $_POST[$key]);
                }
            }

            // Xử lý Xóa Logo
            if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
                $settingModel->updateSetting('store_logo', '');
            }

            // Xử lý Upload Logo mới
            if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] == 0) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = time() . '_logo_' . basename($_FILES["store_logo"]["name"]);
                if (move_uploaded_file($_FILES["store_logo"]["tmp_name"], $uploadDir . $fileName)) {
                    $settingModel->updateSetting('store_logo', 'uploads/' . $fileName);
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
