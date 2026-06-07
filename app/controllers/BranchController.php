<?php
// Đường dẫn file: app/controllers/BranchController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/BranchModel.php';

class BranchController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $branches = (new BranchModel($db))->getAllBranches();
        require_once __DIR__ . '/../views/branch/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $branchModel = new BranchModel($db);

        // Kiểm tra số lượng kho (Mô phỏng giới hạn gói dịch vụ của Sapo)
        $current_branches = count($branchModel->getAllBranches());
        if ($current_branches >= 5) {
            die("<h2 style='text-align:center; margin-top:50px; color:#cf1322;'>Cảnh báo: Gói dịch vụ của bạn đã đạt giới hạn tối đa (5 chi nhánh)! Vui lòng liên hệ CSKH.</h2>");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Nếu chọn làm mặc định, phải xóa mặc định của các kho cũ
            if (isset($_POST['is_default'])) {
                $db->query("UPDATE branches SET is_default = 0");
            }

            $stmt = $db->prepare("INSERT INTO branches (branch_name, phone, email, address, status, is_default, is_pickup, is_inventory) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['branch_name'],
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_POST['address'] ?? '',
                'Hoạt động', // Thêm mới auto là Hoạt động
                isset($_POST['is_default']) ? 1 : 0,
                isset($_POST['is_pickup']) ? 1 : 0,
                isset($_POST['is_inventory']) ? 1 : 0
            ]);

            header("Location: index.php?action=branch_list&success=1");
            exit;
        }
        require_once __DIR__ . '/../views/branch/add.php';
    }

    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $db = (new Database())->getConnection();
        $branchModel = new BranchModel($db);
        $branch = $branchModel->getBranchById($id);

        if (empty($branch)) {
            header("Location: index.php?action=branch_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['is_default'])) {
                $db->query("UPDATE branches SET is_default = 0");
            }

            $stmt = $db->prepare("UPDATE branches SET branch_name=?, phone=?, email=?, address=?, status=?, is_default=?, is_pickup=?, is_inventory=? WHERE id=?");
            $stmt->execute([
                $_POST['branch_name'],
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_POST['address'] ?? '',
                $_POST['status'], // Trạng thái có thể chuyển sang Ngừng hoạt động
                isset($_POST['is_default']) ? 1 : 0,
                isset($_POST['is_pickup']) ? 1 : 0,
                isset($_POST['is_inventory']) ? 1 : 0,
                $id
            ]);

            header("Location: index.php?action=branch_list&success=1");
            exit;
        }
        require_once __DIR__ . '/../views/branch/edit.php';
    }

    // Đã xóa hàm delete() vì Sapo không cho phép xóa chi nhánh!
}
