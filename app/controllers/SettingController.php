<?php
// Đường dẫn: app/controllers/SettingController.php
require_once __DIR__ . '/../../config/database.php';

class SettingController
{
    // 0. TRANG TỔNG QUAN CẤU HÌNH (SETTINGS HUB)
    public function index()
    {
        require_once __DIR__ . '/../views/settings/hub.php';
    }

    // 1. GIAO DIỆN TỔNG QUAN THIẾT LẬP
    public function store_info()
    {
        $db = (new Database())->getConnection();

        // ĐÃ SỬA THÀNH BẢNG settings CỦA KHƯƠNG
        $stmt = $db->query("SELECT * FROM settings WHERE id = 1");
        $store = $stmt->fetch(PDO::FETCH_ASSOC);

        // Đề phòng trường hợp chưa có dòng dữ liệu nào
        if (!$store) {
            $db->query("INSERT INTO settings (id, store_name, phone) VALUES (1, 'Cửa hàng mặc định', '0999999999')");
            $stmt = $db->query("SELECT * FROM settings WHERE id = 1");
            $store = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../views/settings/store_info.php';
    }

    // 2. XỬ LÝ LƯU THÔNG TIN
    public function update_store_info()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $store_name = trim($_POST['store_name']);
            $phone      = trim($_POST['phone']);
            $email      = trim($_POST['email']);
            $address    = trim($_POST['address']);
            $tax_code   = trim($_POST['tax_code']);

            $db = (new Database())->getConnection();

            // ĐÃ SỬA THÀNH BẢNG settings CỦA KHƯƠNG
            $stmt = $db->prepare("UPDATE settings SET store_name = ?, phone = ?, email = ?, address = ?, tax_code = ? WHERE id = 1");
            $stmt->execute([$store_name, $phone, $email, $address, $tax_code]);

            // Cập nhật xong load lại trang kèm thông báo
            header("Location: index.php?action=store_settings&success=1");
            exit;
        }
    }
}
