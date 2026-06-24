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

    // 2. XỬ LÝ LƯU THÔNG TIN (Hỗ trợ Upload Logo & Chế độ kho)
    public function update_store_info()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();

            // Lấy thông tin cũ để giữ lại Logo nếu không upload logo mới
            $stmt_old = $db->query("SELECT logo FROM settings WHERE id = 1");
            $old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);
            $logo_path = $old_data['logo'] ?? '';

            // Xử lý Upload Logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                // Tạo thư mục uploads nếu chưa có
                $upload_dir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                // Giới hạn chỉ cho phép file ảnh
                if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $filename = 'logo_store_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $filename)) {
                        $logo_path = 'uploads/' . $filename; // Lưu đường dẫn tương đối vào DB
                    }
                }
            }

            // Lấy các dữ liệu text khác
            $store_name     = trim($_POST['store_name'] ?? '');
            $business_name  = trim($_POST['business_name'] ?? '');
            $phone          = trim($_POST['phone'] ?? '');
            $admin_email    = trim($_POST['admin_email'] ?? '');
            $notify_email   = trim($_POST['notify_email'] ?? '');
            $address        = trim($_POST['address'] ?? '');
            $country        = trim($_POST['country'] ?? 'Vietnam');
            $province       = trim($_POST['province'] ?? '');
            $tax_code       = trim($_POST['tax_code'] ?? '');
            $inventory_mode = trim($_POST['inventory_mode'] ?? 'full');

            // Cập nhật Database
            $stmt = $db->prepare("
                UPDATE settings SET 
                    logo = ?, store_name = ?, business_name = ?, phone = ?, 
                    admin_email = ?, notify_email = ?, address = ?, country = ?, 
                    province = ?, tax_code = ?, inventory_mode = ? 
                WHERE id = 1
            ");
            $stmt->execute([
                $logo_path,
                $store_name,
                $business_name,
                $phone,
                $admin_email,
                $notify_email,
                $address,
                $country,
                $province,
                $tax_code,
                $inventory_mode
            ]);

            header("Location: index.php?action=store_settings&success=1");
            exit;
        }
    }
}
