<?php
// Đường dẫn file: app/controllers/ImeiController.php

// Gọi các file cần thiết
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ImeiModel.php';

class ImeiController
{
    // Hàm hiển thị form và xử lý dữ liệu khi submit
    public function add()
    {
        $message = "";

        // Khởi tạo kết nối DB trước để dùng cho cả 2 Model
        $database = new Database();
        $db = $database->getConnection();

        // 1. Lấy danh sách Sản phẩm để đổ vào Dropdown
        require_once __DIR__ . '/../models/ProductModel.php';
        $productModel = new ProductModel($db);
        $products = $productModel->getAllProducts(); // Biến này sẽ truyền ra View

        // 2. Xử lý khi người dùng bấm nút Submit form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $imeiModel = new ImeiModel($db);

            $product_id = $_POST['product_id'];
            $imei_code = $_POST['imei_code'];
            $serial_number = $_POST['serial_number'];

            if ($imeiModel->addSingleItem($product_id, $imei_code, $serial_number)) {
                $message = "<div style='color: green; font-weight:bold; margin-bottom: 10px;'>✅ Thêm mã Serial/IMEI thành công!</div>";
            } else {
                $message = "<div style='color: red; font-weight:bold; margin-bottom: 10px;'>❌ Lỗi: Mã Serial đã tồn tại hoặc dữ liệu không hợp lệ.</div>";
            }
        }

        // 3. Gọi View hiển thị
        require_once __DIR__ . '/../views/imei/add_form.php';
    }

    // Thêm hàm này vào bên trong class ImeiController (dưới hàm add)
    public function list()
    {
        // 1. Kết nối Database và Model
        $database = new Database();
        $db = $database->getConnection();
        $imeiModel = new ImeiModel($db);

        // 2. Lấy danh sách dữ liệu từ Model
        $items = $imeiModel->getAllItems();

        // 3. Gọi View để hiển thị danh sách (truyền biến $items sang View)
        require_once __DIR__ . '/../views/imei/list.php';
    }

    public function sell()
    {
        // 1. Xử lý khi nhân viên bấm "Xác nhận bán" (POST)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $imeiModel = new ImeiModel($db);

            $id = $_POST['id'];
            $customer_name = $_POST['customer_name'];
            $customer_phone = $_POST['customer_phone'];

            // Gọi Model để lưu vào Database
            $imeiModel->sellItem($id, $customer_name, $customer_phone);

            // Bán xong quay về trang Danh sách
            header("Location: index.php?action=list");
            exit;
        }

        // 2. Xử lý khi nhân viên bấm nút "Xuất kho" từ danh sách (GET)
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            // Gọi View hiển thị Form nhập thông tin khách
            require_once __DIR__ . '/../views/imei/sell_form.php';
        } else {
            echo "Lỗi: Không tìm thấy sản phẩm cần bán!";
        }
    }

    public function search()
    {
        $result = null;
        $keyword = "";

        // Kiểm tra xem người dùng có bấm nút "Tra cứu" (Gửi GET request) không
        if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
            $keyword = trim($_GET['keyword']);

            $database = new Database();
            $db = $database->getConnection();
            $imeiModel = new ImeiModel($db);

            // Gọi Model để tìm kiếm
            $result = $imeiModel->searchImei($keyword);
        }

        // Gọi View và truyền kết quả ($result) ra ngoài màn hình
        require_once __DIR__ . '/../views/imei/search.php';
    }

    public function warranty()
    {
        if (isset($_GET['id'])) {
            $database = new Database();
            $db = $database->getConnection();
            $imeiModel = new ImeiModel($db);
            $imeiModel->receiveWarranty($_GET['id']);
        }
        header("Location: index.php?action=list");
        exit;
    }

    // Xử lý trả máy cho khách sau khi bảo hành xong
    public function returnItem()
    {
        if (isset($_GET['id'])) {
            $database = new Database();
            $db = $database->getConnection();
            $imeiModel = new ImeiModel($db);
            $imeiModel->returnWarranty($_GET['id']);
        }
        header("Location: index.php?action=list");
        exit;
    }
}
