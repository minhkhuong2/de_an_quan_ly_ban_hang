<?php
// Đường dẫn file: app/controllers/OrderController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ImeiModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController
{
    // Hàm hiển thị màn hình POS
    public function pos()
    {
        $database = new Database();
        $db = $database->getConnection();

        // Tạm thời lấy danh sách IMEI trong kho ra để nhân viên dễ chọn
        $imeiModel = new ImeiModel($db);
        $items_in_stock = $imeiModel->getAllItems();

        // Gọi View giao diện POS
        require_once __DIR__ . '/../views/pos/index.php';
    }
    public function scanImei()
    {
        header('Content-Type: application/json'); // Báo cho trình duyệt biết đây là dữ liệu JSON

        if (isset($_GET['code'])) {
            $database = new Database();
            $db = $database->getConnection();
            $imeiModel = new ImeiModel($db);

            $item = $imeiModel->getImeiForSale($_GET['code']);

            if ($item) {
                echo json_encode(['success' => true, 'data' => $item]);
            } else {
                echo json_encode(['success' => false, 'message' => '❌ Mã IMEI/Serial không tồn tại hoặc máy đã được bán!']);
            }
        }
        exit; // Kết thúc để không in ra giao diện HTML
    }
    public function checkout()
    {
        header('Content-Type: application/json');

        // Nhận dữ liệu JSON từ JavaScript gửi lên
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['cart'])) {
            $database = new Database();
            $db = $database->getConnection();
            $orderModel = new OrderModel($db);

            $c_name = $data['customer_name'] ?? '';
            $c_phone = $data['customer_phone'] ?? '';
            $total = $data['total_amount'] ?? 0;

            if ($orderModel->createOrder($c_name, $c_phone, $total, $data['cart'])) {
                echo json_encode(['success' => true, 'message' => '✅ Thanh toán và xuất kho thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => '❌ Lỗi hệ thống: Không thể lưu hóa đơn.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng đang trống!']);
        }
        exit;
    }
}
