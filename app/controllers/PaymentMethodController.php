<?php
// Đường dẫn: app/controllers/PaymentMethodController.php
require_once __DIR__ . '/../../config/database.php';

class PaymentMethodController
{
    // Hiển thị giao diện Quản lý Phương thức thanh toán
    public function index()
    {
        $db = (new Database())->getConnection();

        $stmt = $db->query("SELECT * FROM payment_methods ORDER BY type ASC, id ASC");
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Chia làm 2 nhóm: Tích hợp và Thủ công
        $integrated_methods = array_filter($methods, fn($m) => $m['type'] === 'integrated');
        $manual_methods = array_filter($methods, fn($m) => $m['type'] === 'manual');

        require_once __DIR__ . '/../views/setting/payment_methods.php';
    }

    // Xử lý bật/tắt (Active/Deactive)
    public function toggle()
    {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? 0;

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE payment_methods SET is_active = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        header("Location: index.php?action=payment_methods&success=1");
        exit;
    }

    // Lưu Cấu hình ngân hàng cho VietQR
    public function save_config()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['method_id'];
            $code = $_POST['method_code'];

            $config_array = [];

            if ($code === 'zalopay') {
                // Đóng gói dữ liệu ZaloPay
                $config_array = [
                    'business_type' => $_POST['business_type'],
                    'business_name' => $_POST['business_name'],
                    'business_id' => $_POST['business_id'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'app_id' => $_POST['app_id'],
                    'key1' => $_POST['key1'],
                    'key2' => $_POST['key2']
                ];
                // Tự động bật Active khi kết nối xong
                $db = (new Database())->getConnection();
                $db->prepare("UPDATE payment_methods SET is_active = 1 WHERE id = ?")->execute([$id]);
            } elseif ($code === 'vietqr') {
                // Đóng gói dữ liệu MBBank VietQR
                $config_array = [
                    'fullname' => mb_strtoupper(trim($_POST['fullname']), 'UTF-8'),
                    'id_card' => $_POST['id_card'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'account_no' => trim($_POST['account_no']),
                    'bank_code' => 'MB'
                ];
            }

            $config = json_encode($config_array);

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE payment_methods SET config_data = ? WHERE id = ?");
            $stmt->execute([$config, $id]);

            header("Location: index.php?action=payment_methods&success=config");
            exit;
        }
    }
    // Xử lý Ngừng kết nối cổng thanh toán
    public function disconnect()
    {
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $db = (new Database())->getConnection();
            // Khi ngừng kết nối: Xóa data cấu hình và tự động tắt (is_active = 0)
            $stmt = $db->prepare("UPDATE payment_methods SET config_data = NULL, is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
        }

        header("Location: index.php?action=payment_methods&success=disconnect");
        exit;
    }
}
