<?php
// Đường dẫn: app/controllers/OrderSourceController.php
require_once __DIR__ . '/../../config/database.php';

class OrderSourceController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Hiển thị danh sách nguồn đơn hàng
    public function index()
    {
        // Lấy toàn bộ nguồn đơn sắp xếp theo thứ tự ưu tiên
        $stmt = $this->db->query("SELECT * FROM order_sources ORDER BY sort_order ASC, id DESC");
        $all_sources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm giới hạn (Mục 1 tài liệu Hệ thống)
        $total_created = count($all_sources);

        // Tách làm 2 mảng theo trạng thái tiếng Việt của Khương
        $active_sources = array_filter($all_sources, fn($s) => $s['status'] === 'Đang sử dụng');
        $inactive_sources = array_filter($all_sources, fn($s) => $s['status'] === 'Ngừng sử dụng');

        require_once __DIR__ . '/../views/settings/order_sources.php';
    }

    // Tạo mới nguồn đơn hàng tùy chỉnh (Mục 4)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['source_name']);
            $category = $_POST['category_name'];
            $logo = trim($_POST['logo_url'] ?? '');

            if (!empty($name) && !empty($category)) {
                $stmt = $this->db->prepare("INSERT INTO order_sources (source_name, category_name, source_type, status, logo_url, sort_order) VALUES (?, ?, 'Tùy chỉnh', 'Đang sử dụng', ?, 99)");
                $stmt->execute([$name, $category, !empty($logo) ? $logo : null]);
                header("Location: index.php?action=order_sources&success=create");
                exit;
            }
        }
    }

    // Chỉnh sửa nguồn đơn hàng (Mục 5.b)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['source_name']);
            $category = $_POST['category_name'];
            $logo = trim($_POST['logo_url'] ?? '');

            // Chỉ cho sửa nếu là nguồn Tùy chỉnh để bảo vệ nguồn Mặc định
            $stmt_check = $this->db->prepare("SELECT source_type FROM order_sources WHERE id = ?");
            $stmt_check->execute([$id]);
            $src = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($src && $src['source_type'] === 'Tùy chỉnh') {
                $stmt = $this->db->prepare("UPDATE order_sources SET source_name = ?, category_name = ?, logo_url = ? WHERE id = ?");
                $stmt->execute([$name, $category, !empty($logo) ? $logo : null, $id]);
                header("Location: index.php?action=order_sources&success=update");
                exit;
            }
        }
    }

    // Thay đổi trạng thái Sử dụng / Ngừng sử dụng (Mục 5)
    public function toggle_status()
    {
        $id = $_GET['id'] ?? 0;
        $current_status = $_GET['status'] ?? '';
        $new_status = ($current_status === 'Đang sử dụng') ? 'Ngừng sử dụng' : 'Đang sử dụng';

        if ($id && !empty($current_status)) {
            $stmt = $this->db->prepare("UPDATE order_sources SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            header("Location: index.php?action=order_sources&success=toggle");
            exit;
        }
    }

    // Xóa hoàn toàn nguồn tùy chỉnh (Mục 5.b)
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            // Chỉ cho xóa nguồn Tùy chỉnh
            $stmt = $this->db->prepare("DELETE FROM order_sources WHERE id = ? AND source_type = 'Tùy chỉnh'");
            $stmt->execute([$id]);
            header("Location: index.php?action=order_sources&success=delete");
            exit;
        }
    }
}
