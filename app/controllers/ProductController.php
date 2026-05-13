<?php
// Đường dẫn file: app/controllers/ProductController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class ProductController
{

    // ========================================================
    // PHẦN 1: CÁC HÀM QUẢN LÝ SẢN PHẨM
    // ========================================================

    public function list()
    {
        $database = new Database();
        $db = $database->getConnection();
        $products = (new ProductModel($db))->getProductsWithStock();
        require_once __DIR__ . '/../views/product/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $newId = (new ProductModel($db))->addProduct(
                $_POST['product_name'] ?? '',
                $_POST['brand'] ?? '',
                $_POST['base_price'] ?? 0,
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $_POST['compare_price'] ?? 0,
                $_POST['cost_price'] ?? 0,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['tags'] ?? ''
            );
            if ($newId) {
                header("Location: index.php?action=edit_product&id=" . $newId . "&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/product/add_form.php';
    }

    public function edit()
    {
        $database = new Database();
        $db = $database->getConnection();
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

    public function price()
    {
        $database = new Database();
        $db = $database->getConnection();
        $products = (new ProductModel($db))->getProductsWithStock();
        require_once __DIR__ . '/../views/product/price.php';
    }

    // ========================================================
    // PHẦN 2: CÁC HÀM QUẢN LÝ DANH MỤC (Bản chuẩn giao diện Sapo)
    // ========================================================

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
            $newId = (new CategoryModel($db))->addCategory($_POST['category_name'] ?? '', $_POST['description'] ?? '', $_POST['status'] ?? 'Hiển thị');
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
            if ($model->updateCategory($id, $_POST['category_name'] ?? '', $_POST['description'] ?? '', $_POST['status'] ?? 'Hiển thị')) {
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
}
