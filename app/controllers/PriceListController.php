<?php
// Đường dẫn: app/controllers/PriceListController.php
require_once __DIR__ . '/../../config/database.php';

class PriceListController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 1. XEM, TÌM KIẾM VÀ LỌC DANH SÁCH BẢNG GIÁ
    public function index()
    {
        // Lấy tất cả bảng giá đổ ra danh sách chính
        $stmt = $this->db->query("SELECT * FROM price_lists ORDER BY id DESC");
        $price_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/product/price_list_list.php';
    }

    // Giao diện Tạo mới Bảng giá (Giữ nguyên bài cũ)
    public function create()
    {
        // 1. Lấy Nhóm KH
        $stmt_cg = $this->db->query("SELECT id, group_name FROM customer_groups");
        $customer_groups = $stmt_cg->fetchAll(PDO::FETCH_ASSOC);

        // 2. Lấy Chi nhánh
        try {
            $stmt_br = $this->db->query("SELECT id, branch_name FROM branches");
            $branches = $stmt_br->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhánh mặc định']];
        }

        // 3. Lấy Kênh bán hàng (Nguồn đơn hàng)
        try {
            $stmt_ch = $this->db->query("SELECT id, source_name FROM order_sources WHERE status = 'Đang sử dụng'");
            $channels = $stmt_ch->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $channels = [['id' => 1, 'source_name' => 'Shopee']];
        }

        require_once __DIR__ . '/../views/product/price_list_create.php';
    }

    // Xử lý lưu Bảng giá (Giữ nguyên bài cũ)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $target_type = $_POST['target_type'];

            // Xử lý lưu ID tương ứng cho 3 cái tùy chọn
            if ($target_type === 'customer_group') {
                $target_id = $_POST['customer_group_id'];
            } elseif ($target_type === 'branch') {
                $target_id = $_POST['branch_id'];
            } else {
                $target_id = $_POST['channel_id']; // Của kênh bán hàng
            }

            $adjustment_type = $_POST['adjustment_type'];
            $adjustment_value = floatval($_POST['adjustment_value']);
            $auto_add = isset($_POST['auto_add_new_product']) ? 1 : 0;
            $status = ($_POST['action_status'] === 'active') ? 'active' : 'draft';

            $stmt = $this->db->prepare("INSERT INTO price_lists (name, target_type, target_id, adjustment_type, adjustment_value, auto_add_new_product, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $target_type, $target_id, $adjustment_type, $adjustment_value, $auto_add, $status]);

            $new_price_list_id = $this->db->lastInsertId();

            header("Location: index.php?action=price_list_add_items&id=" . $new_price_list_id . "&success=1");
            exit;
        }
    }

    // Màn hình chọn sản phẩm hàng loạt (Giữ nguyên bài cũ)
    public function add_items()
    {
        $id = $_GET['id'] ?? 0;
        $stmt_pl = $this->db->prepare("SELECT * FROM price_lists WHERE id = ?");
        $stmt_pl->execute([$id]);
        $price_list = $stmt_pl->fetch(PDO::FETCH_ASSOC);

        if (!$price_list) {
            die("Không tìm thấy bảng giá!");
        }

        $stmt_prod = $this->db->query("SELECT id, product_name, sku, base_price FROM products WHERE parent_id IS NULL");
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        $stmt_items = $this->db->prepare("SELECT product_id FROM price_list_items WHERE price_list_id = ?");
        $stmt_items->execute([$id]);
        $existing_items = $stmt_items->fetchAll(PDO::FETCH_COLUMN);

        require_once __DIR__ . '/../views/product/price_list_add_items.php';
    }

    // Lưu sản phẩm hàng loạt vào bảng giá (Giữ nguyên bài cũ)
    public function store_items()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price_list_id = $_POST['price_list_id'];
            $selected_products = $_POST['product_ids'] ?? [];
            $custom_prices = $_POST['custom_prices'] ?? [];

            $stmt_del = $this->db->prepare("DELETE FROM price_list_items WHERE price_list_id = ?");
            $stmt_del->execute([$price_list_id]);

            if (!empty($selected_products)) {
                $stmt_insert = $this->db->prepare("INSERT INTO price_list_items (price_list_id, product_id, custom_price) VALUES (?, ?, ?)");
                foreach ($selected_products as $product_id) {
                    $price = isset($custom_prices[$product_id]) ? floatval($custom_prices[$product_id]) : 0;
                    $stmt_insert->execute([$price_list_id, $product_id, $price]);
                }
            }

            header("Location: index.php?action=product_price&success=items_saved");
            exit;
        }
    }

    // 2. TRANG CHI TIẾT VÀ CHỈNH SỬA BẢNG GIÁ (Sửa lẻ từng món)
    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        // Lấy thông tin chung bảng giá
        $stmt_pl = $this->db->prepare("SELECT * FROM price_lists WHERE id = ?");
        $stmt_pl->execute([$id]);
        $price_list = $stmt_pl->fetch(PDO::FETCH_ASSOC);

        if (!$price_list) {
            die("Không tìm thấy bảng giá!");
        }

        // Lấy danh sách sản phẩm ĐANG ĐƯỢC ĐĂNG BÁN trong bảng giá này
        $stmt_items = $this->db->prepare("
            SELECT p.id, p.product_name, p.sku, p.base_price, pli.custom_price 
            FROM price_list_items pli
            JOIN products p ON pli.product_id = p.id
            WHERE pli.price_list_id = ?
        ");
        $stmt_items->execute([$id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/product/price_list_detail.php';
    }

    // API: Cập nhật giá bán lẻ của một sản phẩm trong bảng giá (Biểu tượng bút chì)
    public function update_item_price()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $price_list_id = $data['price_list_id'] ?? 0;
            $product_id = $data['product_id'] ?? 0;
            $new_price = floatval($data['new_price'] ?? 0);

            if ($price_list_id && $product_id && $new_price >= 0) {
                $stmt = $this->db->prepare("UPDATE price_list_items SET custom_price = ? WHERE price_list_id = ? AND product_id = ?");
                $stmt->execute([$new_price, $price_list_id, $product_id]);
                echo json_encode(['status' => 'success', 'msg' => 'Cập nhật giá sản phẩm thành công!']);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu đầu vào không hợp lệ.']);
            exit;
        }
    }

    // API: Xóa sản phẩm khỏi danh sách đăng bán của bảng giá (Biểu tượng thùng rác)
    public function delete_item()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $price_list_id = $data['price_list_id'] ?? 0;
            $product_id = $data['product_id'] ?? 0;

            if ($price_list_id && $product_id) {
                $stmt = $this->db->prepare("DELETE FROM price_list_items WHERE price_list_id = ? AND product_id = ?");
                $stmt->execute([$price_list_id, $product_id]);
                echo json_encode(['status' => 'success', 'msg' => 'Đã loại bỏ sản phẩm khỏi bảng giá!']);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'Không thể xóa sản phẩm.']);
            exit;
        }
    }
}
