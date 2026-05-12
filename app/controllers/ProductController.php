<?php
// Đường dẫn file: app/controllers/ProductController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController
{

    // 1. Hàm xử lý trang Thêm sản phẩm
    public function add()
    {
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $productModel = new ProductModel($db);

            // Nhặt toàn bộ dữ liệu từ Form
            $name = $_POST['product_name'] ?? '';
            $brand = $_POST['brand'] ?? '';
            $price = $_POST['base_price'] ?? 0;

            // Các trường mới thêm
            $sku = $_POST['sku'] ?? '';
            $barcode = $_POST['barcode'] ?? '';
            $unit = $_POST['unit'] ?? '';
            $description = $_POST['description'] ?? '';
            $compare_price = $_POST['compare_price'] ?? 0;
            $cost_price = $_POST['cost_price'] ?? 0;
            $apply_tax = isset($_POST['apply_tax']) ? 1 : 0; // Checkbox
            $category = $_POST['category'] ?? '';
            $tags = $_POST['tags'] ?? '';

            // Gọi Model để lưu vào Database
            if ($productModel->addProduct($name, $brand, $price, $sku, $barcode, $unit, $description, $compare_price, $cost_price, $apply_tax, $category, $tags)) {
                $message = "<div style='color: green; font-weight:bold; margin-bottom: 10px; padding: 10px; background: #e6ffed; border: 1px solid #b7eb8f; border-radius: 4px;'>✅ Đã thêm sản phẩm mới thành công!</div>";
            } else {
                $message = "<div style='color: red; font-weight:bold; margin-bottom: 10px; padding: 10px; background: #fff1f0; border: 1px solid #ffa39e; border-radius: 4px;'>❌ Lỗi: Không thể thêm sản phẩm.</div>";
            }
        }

        require_once __DIR__ . '/../views/product/add_form.php';
    }

    // 2. Hàm xử lý trang Danh sách sản phẩm (CHÍNH LÀ HÀM BẠN ĐANG THIẾU)
    public function list()
    {
        $database = new Database();
        $db = $database->getConnection();
        $productModel = new ProductModel($db);

        // Kiểm tra xem hàm getProductsWithStock đã được tạo chưa để tránh lỗi
        if (method_exists($productModel, 'getProductsWithStock')) {
            $products = $productModel->getProductsWithStock();
        } else {
            $products = $productModel->getAllProducts();
        }

        require_once __DIR__ . '/../views/product/list.php';
    }

    // 3. Hàm xử lý trang Bảng giá
    public function price()
    {
        $database = new Database();
        $db = $database->getConnection();
        $productModel = new ProductModel($db);

        if (method_exists($productModel, 'getProductsWithStock')) {
            $products = $productModel->getProductsWithStock();
        } else {
            $products = $productModel->getAllProducts();
        }

        require_once __DIR__ . '/../views/product/price.php';
    }

    // 4. Hàm xử lý trang Danh mục
    public function category()
    {
        // Tạm thời hiển thị một thông báo cho trang Danh mục
        echo "<h2 style='padding: 20px;'>Tính năng Danh mục đang được phát triển...</h2>";
    }
}
