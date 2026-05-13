<?php
// Đường dẫn file: app/controllers/ProductController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/PriceModel.php'; // Gọi thêm Model Bảng giá

class ProductController
{

    // ==== 1. SẢN PHẨM ====
    public function list()
    {
        $db = (new Database())->getConnection();
        $products = (new ProductModel($db))->getProductsWithStock();
        require_once __DIR__ . '/../views/product/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $newId = (new ProductModel($db))->addProduct($_POST['product_name'] ?? '', $_POST['brand'] ?? '', $_POST['base_price'] ?? 0, $_POST['sku'] ?? '', $_POST['barcode'] ?? '', $_POST['unit'] ?? '', $_POST['description'] ?? '', $_POST['compare_price'] ?? 0, $_POST['cost_price'] ?? 0, isset($_POST['apply_tax']) ? 1 : 0, $_POST['category'] ?? '', $_POST['tags'] ?? '');
            if ($newId) {
                header("Location: index.php?action=edit_product&id=" . $newId . "&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/product/add_form.php';
    }

    public function edit()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);
        $id = $_GET['id'] ?? 0;
        $message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($productModel->updateProduct($id, $_POST['product_name'] ?? '', $_POST['brand'] ?? '', $_POST['base_price'] ?? 0, $_POST['sku'] ?? '', $_POST['barcode'] ?? '', $_POST['unit'] ?? '', $_POST['description'] ?? '', $_POST['compare_price'] ?? 0, $_POST['cost_price'] ?? 0, isset($_POST['apply_tax']) ? 1 : 0, $_POST['category'] ?? '', $_POST['tags'] ?? '')) {
                $message = "<div style='background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;'>✅ Cập nhật sản phẩm thành công!</div>";
            }
        }
        $product = $productModel->getProductById($id);
        if (!$product) {
            header("Location: index.php?action=product_list");
            exit;
        }
        require_once __DIR__ . '/../views/product/edit_form.php';
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $db = (new Database())->getConnection();
            (new ProductModel($db))->deleteProduct($_GET['id']);
        }
        header("Location: index.php?action=product_list");
        exit;
    }

    // ==== 2. DANH MỤC ====
    public function category_list()
    {
        $db = (new Database())->getConnection();
        $categories = (new CategoryModel($db))->getAllCategories();
        require_once __DIR__ . '/../views/product/category_list.php';
    }

    public function add_category()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();

            // Xử lý mảng quy tắc tự động thành chuỗi JSON
            $auto_rules = [];
            if (($_POST['selection_type'] ?? 'manual') == 'auto' && isset($_POST['rule_field'])) {
                $fields = $_POST['rule_field'];
                $ops = $_POST['rule_operator'];
                $vals = $_POST['rule_value'];
                for ($i = 0; $i < count($fields); $i++) {
                    if (!empty($vals[$i])) $auto_rules[] = ['field' => $fields[$i], 'operator' => $ops[$i], 'value' => $vals[$i]];
                }
            }
            $rules_json = json_encode($auto_rules, JSON_UNESCAPED_UNICODE);

            $newId = (new CategoryModel($db))->addCategory(
                $_POST['category_name'] ?? '',
                $_POST['description'] ?? '',
                $_POST['alias'] ?? '',
                $_POST['seo_title'] ?? '',
                $_POST['seo_description'] ?? '',
                $_POST['status'] ?? 'Hiển thị',
                $_POST['selection_type'] ?? 'manual',
                $_POST['match_type'] ?? 'all',
                $rules_json,
                $_POST['sort_order'] ?? 'newest'
            );
            if ($newId) {
                header("Location: index.php?action=edit_category&id=" . $newId . "&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/product/category_add.php';
    }

    public function edit_category()
    {
        $db = (new Database())->getConnection();
        $model = new CategoryModel($db);
        $id = $_GET['id'] ?? 0;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $auto_rules = [];
            if (($_POST['selection_type'] ?? 'manual') == 'auto' && isset($_POST['rule_field'])) {
                $fields = $_POST['rule_field'];
                $ops = $_POST['rule_operator'];
                $vals = $_POST['rule_value'];
                for ($i = 0; $i < count($fields); $i++) {
                    if (!empty($vals[$i])) $auto_rules[] = ['field' => $fields[$i], 'operator' => $ops[$i], 'value' => $vals[$i]];
                }
            }
            $rules_json = json_encode($auto_rules, JSON_UNESCAPED_UNICODE);

            if ($model->updateCategory($id, $_POST['category_name'] ?? '', $_POST['description'] ?? '', $_POST['alias'] ?? '', $_POST['seo_title'] ?? '', $_POST['seo_description'] ?? '', $_POST['status'] ?? 'Hiển thị', $_POST['selection_type'] ?? 'manual', $_POST['match_type'] ?? 'all', $rules_json, $_POST['sort_order'] ?? 'newest')) {
                header("Location: index.php?action=edit_category&id=" . $id . "&updated=1");
                exit;
            }
        }
        $category = $model->getCategoryById($id);
        if (!$category) {
            header("Location: index.php?action=product_category");
            exit;
        }
        require_once __DIR__ . '/../views/product/category_edit.php';
    }

    public function delete_category()
    {
        if (isset($_GET['id'])) {
            $db = (new Database())->getConnection();
            (new CategoryModel($db))->deleteCategory($_GET['id']);
        }
        header("Location: index.php?action=product_category");
        exit;
    }

    // ==== 3. BẢNG GIÁ (NEW) ====
    public function price()
    {
        $db = (new Database())->getConnection();
        $priceModel = new PriceModel($db);

        // Xử lý Xóa bảng giá
        if (isset($_GET['delete_id'])) {
            $priceModel->deletePrice($_GET['delete_id']);
            header("Location: index.php?action=product_price");
            exit;
        }

        $prices = $priceModel->getAllPrices();
        require_once __DIR__ . '/../views/product/price.php';
    }

    public function add_price()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $status = isset($_POST['btn_apply']) ? 'Đang áp dụng' : 'Lưu nháp';
            $auto_add = isset($_POST['auto_add']) ? 1 : 0;

            $newId = (new PriceModel($db))->addPrice($_POST['price_name'], $_POST['adjust_type'], $_POST['adjust_value'], $auto_add, $_POST['branch'], $status);
            if ($newId) {
                header("Location: index.php?action=product_price&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/product/price_add.php';
    }
}
