<?php
// Đường dẫn file: app/controllers/ProductController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController
{
    // Hàm hiển thị form và xử lý lưu tên điện thoại mới
    public function add()
    {
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $productModel = new ProductModel($db);

            $name = $_POST['product_name'];
            $brand = $_POST['brand'];
            $price = $_POST['base_price'];

            if ($productModel->addProduct($name, $brand, $price)) {
                $message = "<div style='color: green; font-weight:bold; margin-bottom: 10px;'>✅ Đã thêm dòng điện thoại mới thành công!</div>";
            } else {
                $message = "<div style='color: red; font-weight:bold; margin-bottom: 10px;'>❌ Lỗi: Không thể thêm sản phẩm.</div>";
            }
        }

        // Gọi View hiển thị
        require_once __DIR__ . '/../views/product/add_form.php';
    }
}
