<?php
// Đường dẫn file: app/controllers/DashboardController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
// ĐÃ XÓA KHAI BÁO ImeiModel vì bạn đã nâng cấp sang hệ thống Kho mới

class DashboardController
{
    public function index()
    {
        // 1. Kết nối Database
        $database = new Database();
        $db = $database->getConnection();

        // 2. Khởi tạo Model mới
        $productModel = new ProductModel($db);

        // 3. THỐNG KÊ DASHBOARD (Nâng cấp theo hệ thống mới)

        // Đếm tổng số lượng dòng sản phẩm đang có
        $allProducts = $productModel->getAllProducts();
        $totalProducts = count($allProducts);

        // Tính tổng Tồn kho thực tế từ bảng products
        $stmt = $db->query("SELECT SUM(stock) as total_stock FROM products");
        $inStock = $stmt->fetch(PDO::FETCH_ASSOC)['total_stock'] ?? 0;

        // Đếm số lượng Đã bán và Đang bảo hành 
        // (Dùng try-catch để nếu bạn đã xóa bảng IMEI cũ thì biểu đồ vẫn hiện số 0 mà không bị sập web)
        try {
            $sold = $db->query("SELECT COUNT(*) FROM product_items WHERE status = 'Đã bán'")->fetchColumn();
            $warranty = $db->query("SELECT COUNT(*) FROM product_items WHERE status = 'Đang bảo hành'")->fetchColumn();
        } catch (Exception $e) {
            $sold = 0;
            $warranty = 0;
        }

        // 4. Truyền toàn bộ biến này ra View Dashboard để hiển thị
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
