<?php
// Đường dẫn: app/controllers/BranchController.php
require_once __DIR__ . '/../../config/database.php';

class BranchController
{
    // 1. GIAO DIỆN DANH SÁCH CHI NHÁNH
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM branches ORDER BY routing_priority ASC, is_default DESC, created_at ASC");
        $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách chi nhánh đang hoạt động để làm dropdown Chuyển giao dữ liệu
        $active_branches = array_filter($branches, function ($b) {
            return $b['status'] === 'active';
        });

        require_once __DIR__ . '/../views/settings/branch_list.php';
    }

    // 2. LƯU THÊM MỚI HOẶC CẬP NHẬT
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : 0;

            $branch_name = trim($_POST['branch_name'] ?? '');
            $branch_code = trim($_POST['branch_code'] ?? '');
            if (empty($branch_code)) $branch_code = 'CN' . date('YmdHis'); // Tự sinh mã nếu để trống

            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $country = $_POST['country'] ?? 'Vietnam';
            $province = $_POST['province'] ?? '';
            $ward = $_POST['ward'] ?? '';
            $address_detail = $_POST['address_detail'] ?? '';

            $is_new_address_format = isset($_POST['is_new_address_format']) ? 1 : 0;
            $district = ($is_new_address_format == 1) ? NULL : ($_POST['district'] ?? '');

            $has_inventory = isset($_POST['has_inventory']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $is_pickup_location = isset($_POST['is_pickup_location']) ? 1 : 0;

            $db = (new Database())->getConnection();

            // Nếu set làm mặc định, phải gỡ mặc định của các chi nhánh khác
            if ($is_default == 1) {
                $db->query("UPDATE branches SET is_default = 0");
                $has_inventory = 1; // Mặc định bắt buộc phải có quản lý kho
                $is_pickup_location = 1; // Mặc định bắt buộc là điểm lấy hàng
            }

            if ($id > 0) {
                // UPDATE (Lưu ý: Không cho sửa branch_code theo tài liệu Sapo)
                $stmt = $db->prepare("UPDATE branches SET branch_name=?, phone=?, email=?, country=?, province=?, district=?, ward=?, address_detail=?, is_new_address_format=?, has_inventory=?, is_default=?, is_pickup_location=? WHERE id=?");
                $stmt->execute([$branch_name, $phone, $email, $country, $province, $district, $ward, $address_detail, $is_new_address_format, $has_inventory, $is_default, $is_pickup_location, $id]);
            } else {
                // INSERT
                $stmt = $db->prepare("INSERT INTO branches (branch_code, branch_name, phone, email, country, province, district, ward, address_detail, is_new_address_format, has_inventory, is_default, is_pickup_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$branch_code, $branch_name, $phone, $email, $country, $province, $district, $ward, $address_detail, $is_new_address_format, $has_inventory, $is_default, $is_pickup_location]);
            }

            header("Location: index.php?action=branch_list&success=1");
            exit;
        }
    }

    // 3. ĐỔI TRẠNG THÁI (Ngừng hoạt động / Kích hoạt)
    public function toggle_status()
    {
        $id = intval($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? 'active';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE branches SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        header("Location: index.php?action=branch_list&success=1");
    }

    // 4. CHUYỂN GIAO GIAO DỊCH & XÓA KHO CHI NHÁNH ĐÓNG CỬA
    public function transfer_and_delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from_branch_id = intval($_POST['from_branch_id']);
            $to_branch_id = intval($_POST['to_branch_id']);

            $db = (new Database())->getConnection();
            try {
                $db->beginTransaction();

                // Chuyển Đơn hàng chưa giao xong sang chi nhánh mới
                $db->prepare("UPDATE orders SET branch_id = ? WHERE branch_id = ? AND shipping_status != 'delivered'")->execute([$to_branch_id, $from_branch_id]);

                // Chuyển Phiếu thu / chi liên quan sang chi nhánh mới
                $db->prepare("UPDATE receipts SET branch_id = ? WHERE branch_id = ?")->execute([$to_branch_id, $from_branch_id]);
                $db->prepare("UPDATE expenses SET branch_id = ? WHERE branch_id = ?")->execute([$to_branch_id, $from_branch_id]);

                // Xóa cờ quản lý kho của chi nhánh cũ
                $db->prepare("UPDATE branches SET has_inventory = 0 WHERE id = ?")->execute([$from_branch_id]);

                $db->commit();
                header("Location: index.php?action=branch_list&success_transfer=1");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi hệ thống khi chuyển giao dữ liệu: " . $e->getMessage());
            }
        }
    }
    // 5. CẬP NHẬT THỨ TỰ ƯU TIÊN NHẬN ĐƠN ONLINE (KÉO THẢ)
    public function update_priority()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['priorities']) && is_array($data['priorities'])) {
                $db = (new Database())->getConnection();
                try {
                    $db->beginTransaction();
                    // Lưu thứ tự 1, 2, 3... dựa trên mảng gửi lên từ Javascript
                    foreach ($data['priorities'] as $index => $id) {
                        $stmt = $db->prepare("UPDATE branches SET routing_priority = ? WHERE id = ?");
                        $stmt->execute([$index + 1, $id]);
                    }
                    $db->commit();
                    echo json_encode(['status' => 'success', 'msg' => 'Đã lưu cấu hình ưu tiên nhận đơn Online!']);
                } catch (Exception $e) {
                    $db->rollBack();
                    echo json_encode(['status' => 'error', 'msg' => 'Lỗi: ' . $e->getMessage()]);
                }
            }
            exit;
        }
    }
}
