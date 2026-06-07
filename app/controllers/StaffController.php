<?php
// Đường dẫn: app/controllers/StaffController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/StaffModel.php';

class StaffController
{

    public function list()
    {
        $db = (new Database())->getConnection();
        $staffs = (new StaffModel($db))->getAllStaffs();
        require_once __DIR__ . '/../views/staff/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $db = (new Database())->getConnection();
            $staffModel = new StaffModel($db);

            $id = $staffModel->addStaff(
                $_POST['last_name'] ?? '',
                $_POST['first_name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['role'] ?? 'Nhân viên bán hàng'
            );

            if ($id) {
                $activation_link = "index.php?action=activate_staff&id=" . $id;
                header("Location: index.php?action=staff_list&success_add=1&invite_link=" . urlencode($activation_link));
                exit;
            }
        }
        require_once __DIR__ . '/../views/staff/add.php';
    }

    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $db = (new Database())->getConnection();
        $staffModel = new StaffModel($db);

        $staff = $staffModel->getStaffById($id);
        if (empty($staff)) {
            header("Location: index.php?action=staff_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Xử lý lấy mảng các quyền được check
            $permissions = $_POST['permissions'] ?? [];
            $permissions_json = json_encode($permissions);

            // Cập nhật thông tin (bổ sung lưu permissions)
            $stmt = $db->prepare("UPDATE staffs SET last_name=?, first_name=?, phone=?, role=?, permissions=? WHERE id=?");
            if ($stmt->execute([$_POST['last_name'] ?? '', $_POST['first_name'] ?? '', $_POST['phone'] ?? '', $_POST['role'] ?? 'Nhân viên bán hàng', $permissions_json, $id])) {
                header("Location: index.php?action=edit_staff&id=$id&success=1");
                exit;
            }
        }

        // Giải mã JSON để truyền mảng quyền ra View (để tích sẵn các ô đã chọn)
        $current_permissions = json_decode($staff['permissions'] ?? '[]', true) ?: [];

        require_once __DIR__ . '/../views/staff/edit.php';
    }

    public function delete()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        if ($id > 0) {
            $db = (new Database())->getConnection();
            (new StaffModel($db))->deleteStaff($id);
        }
        header("Location: index.php?action=staff_list&success_delete=1");
        exit;
    }

    // ĐÃ FIX LOGIC BUG (Phân tách rõ ràng ngữ nghĩa kiểm tra)
    public function activate()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $db = (new Database())->getConnection();
        $staffModel = new StaffModel($db);
        $staff = $staffModel->getStaffById($id);

        // 1. Kiểm tra tài khoản có tồn tại không
        if (empty($staff)) {
            die("<h2 style='text-align:center; margin-top:50px;'>Lỗi: Không tìm thấy thông tin nhân viên hoặc Link không hợp lệ!</h2>");
        }

        // 2. Kiểm tra tài khoản đã kích hoạt chưa (Dùng === để chuẩn ngữ nghĩa)
        if (isset($staff['status']) && $staff['status'] === 'Đang kích hoạt') {
            die("<h2 style='text-align:center; margin-top:50px; color:#cf1322;'>Lỗi: Tài khoản này đã được kích hoạt từ trước!</h2><div style='text-align:center;'><a href='index.php'>Quay lại</a></div>");
        }

        // 3. Xử lý đặt mật khẩu
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $password = $_POST['password'] ?? '';
            if (!empty($password) && $staffModel->activateStaff($id, $password)) {
                header("Location: index.php?action=staff_list&success_activate=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/staff/activate.php';
    }
}
