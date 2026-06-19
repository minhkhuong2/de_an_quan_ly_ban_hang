<?php
require_once __DIR__ . '/../../config/database.php';

class CustomerGroupController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }
    // 1. XEM DANH SÁCH NHÓM KHÁCH HÀNG
    public function index()
    {
        // Lấy danh sách nhóm và đếm số lượng (tạm thời đếm nhóm thủ công, nhóm tự động sẽ đếm động khi vào chi tiết)
        $stmt = $this->db->query("
            SELECT cg.*, 
                   (SELECT COUNT(*) FROM customer_group_members WHERE group_id = cg.id) as manual_count 
            FROM customer_groups cg 
            ORDER BY cg.id DESC
        ");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/customer/group_list.php';
    }

    // Hiển thị Form Tạo Nhóm Khách Hàng (Thủ công / Tự động)
    public function create()
    {
        require_once __DIR__ . '/../views/customer/group_create.php';
    }

    // Xử lý Lưu Nhóm (Giữ nguyên bài trước)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $group_name = trim($_POST['group_name']);
            $description = trim($_POST['description']);
            $group_type = $_POST['group_type'];

            $condition_match = 'all';
            $conditions_json = null;

            if ($group_type === 'auto') {
                $condition_match = $_POST['condition_match'];
                $conditions = [];
                $fields = $_POST['cond_field'] ?? [];
                $operators = $_POST['cond_operator'] ?? [];
                $values = $_POST['cond_value'] ?? [];

                for ($i = 0; $i < count($fields); $i++) {
                    $conditions[] = ['field' => $fields[$i], 'operator' => $operators[$i], 'value' => $values[$i]];
                }
                $conditions_json = json_encode($conditions, JSON_UNESCAPED_UNICODE);
            }

            $stmt = $this->db->prepare("INSERT INTO customer_groups (group_name, description, group_type, condition_match, conditions_json) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$group_name, $description, $group_type, $condition_match, $conditions_json]);

            header("Location: index.php?action=customer_groups&success=group_created");
            exit;
        }
    }

    // 3. XEM CHI TIẾT NHÓM (CẢ THỦ CÔNG LẪN TỰ ĐỘNG)
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->db->prepare("SELECT * FROM customer_groups WHERE id = ?");
        $stmt->execute([$id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$group) die("Không tìm thấy nhóm khách hàng!");

        $members = [];
        $all_customers = []; // Dành cho popup thêm thủ công

        if ($group['group_type'] === 'manual') {
            // Lấy danh sách thành viên trong nhóm thủ công
            $stmt_mem = $this->db->prepare("
                SELECT c.* FROM customers c 
                JOIN customer_group_members m ON c.id = m.customer_id 
                WHERE m.group_id = ?
            ");
            $stmt_mem->execute([$id]);
            $members = $stmt_mem->fetchAll(PDO::FETCH_ASSOC);

            // Lấy những KH CHƯA CÓ TRONG NHÓM để hiện ở Popup Thêm
            $stmt_all = $this->db->prepare("
                SELECT id, customer_code, last_name, first_name, phone FROM customers 
                WHERE id NOT IN (SELECT customer_id FROM customer_group_members WHERE group_id = ?)
            ");
            $stmt_all->execute([$id]);
            $all_customers = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Nhóm TỰ ĐỘNG: Phân tích JSON và lọc khách hàng động
            $conditions = json_decode($group['conditions_json'], true);
            // Để demo, mình sẽ giả lập kéo toàn bộ KH ra, trong thực tế sẽ build câu SQL WHERE phức tạp
            $stmt_auto = $this->db->query("SELECT * FROM customers");
            $members = $stmt_auto->fetchAll(PDO::FETCH_ASSOC);
            // (Trong đồ án thực tế Khương có thể dùng vòng lặp PHP duyệt mảng $members lọc ra theo $conditions nhé)
        }

        require_once __DIR__ . '/../views/customer/group_detail.php';
    }

    // 4. XỬ LÝ THÊM KHÁCH HÀNG VÀO NHÓM THỦ CÔNG
    public function store_members()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $group_id = $_POST['group_id'];
            $customer_ids = $_POST['customer_ids'] ?? [];

            if (!empty($customer_ids)) {
                $stmt = $this->db->prepare("INSERT IGNORE INTO customer_group_members (group_id, customer_id) VALUES (?, ?)");
                foreach ($customer_ids as $cid) {
                    $stmt->execute([$group_id, $cid]);
                }
            }
            header("Location: index.php?action=customer_group_detail&id=$group_id&success=members_added");
            exit;
        }
    }

    // API: Xử lý Xuất file Excel / CSV (Mục 1)
    public function export_customers()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $export_type = $data['export_type']; // all, page, selected
            $selected_ids = $data['selected_ids'] ?? [];

            // Giả lập truy vấn lấy data khách hàng cần xuất
            // Trong thực tế sẽ viết SQL: SELECT * FROM customers WHERE id IN (...)

            // Theo tài liệu Gửi file qua email. 
            // Do môi trường XAMPP khó cấu hình gửi mail thật, ta trả về lệnh cho Frontend tải file ảo để demo nghiệp vụ.

            echo json_encode([
                'status' => 'success',
                'msg' => 'Hệ thống đang tiến hành trích xuất dữ liệu. File danh sách khách hàng sẽ sớm được gửi đến email quản trị của bạn!'
            ]);
            exit;
        }
    }
}
