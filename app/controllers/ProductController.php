<?php
// Đường dẫn file: app/controllers/ProductController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/PriceModel.php';

class ProductController
{
    public function list()
    {
        $db = (new Database())->getConnection();

        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $tags = $_GET['tags'] ?? '';
        $type = $_GET['type'] ?? '';

        $productModel = new ProductModel($db);
        $categoryModel = new CategoryModel($db);

        $products = $productModel->getProductsWithStock($search, $category, $brand, $tags, $type);

        foreach ($products as $key => $prod) {
            $products[$key]['smart_categories'] = $categoryModel->getCategoriesOfProduct($prod);
        }

        $categories = $categoryModel->getAllCategories();

        require_once __DIR__ . '/../views/product/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $imagePath = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            // XỬ LÝ DẤU CHẤM TIỀN TỆ TRƯỚC KHI LƯU VÀO DB
            $base_price = (float)str_replace(['.', ','], '', $_POST['base_price'] ?? $_POST['price'] ?? 0);
            $compare_price = (float)str_replace(['.', ','], '', $_POST['compare_price'] ?? 0);
            $cost_price = (float)str_replace(['.', ','], '', $_POST['cost_price'] ?? 0);

            $is_added = $productModel->addProduct(
                $_POST['product_name'] ?? '',
                $_POST['brand'] ?? '',
                $base_price,
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $compare_price,
                $cost_price,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['tags'] ?? ''
            );

            if ($is_added) {
                header("Location: index.php?action=edit_product&id=" . $is_added . "&success=1");
                exit;
            } else {
                $message = "<div style='background:#fff1f0; color:#ff4d4f; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;'>❌ Có lỗi xảy ra, vui lòng thử lại!</div>";
            }
        }

        $stmtCat = $db->prepare("SELECT category_name FROM categories ORDER BY id DESC");
        $stmtCat->execute();
        $dynamic_categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

        $stmtBrand = $db->prepare("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ''");
        $stmtBrand->execute();
        $dynamic_brands = $stmtBrand->fetchAll(PDO::FETCH_COLUMN);
        if (empty($dynamic_brands)) {
            $dynamic_brands = ['Apple', 'Samsung', 'Xiaomi', 'Oppo'];
        }

        $dynamic_types = ['Điện thoại', 'Phụ kiện', 'Đồng hồ', 'Tai nghe', 'Sạc dự phòng'];

        require_once __DIR__ . '/../views/product/add_form.php';
    }

    public function edit()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);
        $id = $_GET['id'] ?? 0;
        $message = "";

        $product = $productModel->getProductById($id);
        if (!$product) {
            header("Location: index.php?action=product_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $imagePath = $product['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            // XỬ LÝ DẤU CHẤM TIỀN TỆ TRƯỚC KHI LƯU VÀO DB
            $base_price = (float)str_replace(['.', ','], '', $_POST['base_price'] ?? $_POST['price'] ?? 0);
            $compare_price = (float)str_replace(['.', ','], '', $_POST['compare_price'] ?? 0);
            $cost_price = (float)str_replace(['.', ','], '', $_POST['cost_price'] ?? 0);

            $is_updated = $productModel->updateProduct(
                $id,
                $_POST['product_name'] ?? '',
                $_POST['brand'] ?? '',
                $base_price,
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $compare_price,
                $cost_price,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['tags'] ?? ''
            );

            if ($is_updated) {
                if (isset($_POST['new_stock'])) {
                    $current_stock = $product['stock'] ?? 0;
                    $current_available = $product['available'] ?? 0;
                    $new_stock = (int)$_POST['new_stock'];

                    $stock_diff = $new_stock - $current_stock;
                    if ($stock_diff != 0) {
                        $new_available = $current_available + $stock_diff;
                        $productModel->updateInventory($id, $new_stock, $new_available);
                    }
                }

                $message = "<div style='background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;'>✅ Cập nhật sản phẩm và Tồn kho thành công!</div>";
                $product = $productModel->getProductById($id);
            }
        }

        $stmtCat = $db->prepare("SELECT category_name FROM categories ORDER BY id DESC");
        $stmtCat->execute();
        $dynamic_categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

        $stmtBrand = $db->prepare("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ''");
        $stmtBrand->execute();
        $dynamic_brands = $stmtBrand->fetchAll(PDO::FETCH_COLUMN);
        if (empty($dynamic_brands)) {
            $dynamic_brands = ['Apple', 'Samsung', 'Xiaomi', 'Oppo'];
        }

        $dynamic_types = ['Điện thoại', 'Phụ kiện', 'Đồng hồ', 'Tai nghe', 'Sạc dự phòng'];

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

    public function category_list()
    {
        $db = (new Database())->getConnection();

        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        $categories = (new CategoryModel($db))->getAllCategories($search, $type);
        require_once __DIR__ . '/../views/product/category_list.php';
    }

    public function add_category()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();

            $auto_rules = [];
            if (isset($_POST['rule_field'])) {
                for ($i = 0; $i < count($_POST['rule_field']); $i++) {
                    $auto_rules[] = [
                        'field' => $_POST['rule_field'][$i],
                        'operator' => $_POST['rule_operator'][$i],
                        'value' => $_POST['rule_value'][$i]
                    ];
                }
            }
            $auto_rules_json = json_encode($auto_rules, JSON_UNESCAPED_UNICODE);

            $newId = (new CategoryModel($db))->addCategory(
                $_POST['category_name'] ?? '',
                $_POST['description'] ?? '',
                '',
                '',
                '',
                $_POST['status'] ?? 'Hiển thị',
                $_POST['selection_type'] ?? 'manual',
                $_POST['match_type'] ?? 'all',
                $auto_rules_json,
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
            if (isset($_POST['rule_field'])) {
                for ($i = 0; $i < count($_POST['rule_field']); $i++) {
                    $auto_rules[] = [
                        'field' => $_POST['rule_field'][$i],
                        'operator' => $_POST['rule_operator'][$i],
                        'value' => $_POST['rule_value'][$i]
                    ];
                }
            }
            $auto_rules_json = json_encode($auto_rules, JSON_UNESCAPED_UNICODE);

            if ($model->updateCategory(
                $id,
                $_POST['category_name'] ?? '',
                $_POST['description'] ?? '',
                '',
                '',
                '',
                $_POST['status'] ?? 'Hiển thị',
                $_POST['selection_type'] ?? 'manual',
                $_POST['match_type'] ?? 'all',
                $auto_rules_json,
                $_POST['sort_order'] ?? 'newest'
            )) {
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

    public function price()
    {
        $db = (new Database())->getConnection();
        $priceModel = new PriceModel($db);
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
            $newId = (new PriceModel($db))->addPrice(
                $_POST['price_name'],
                $_POST['adjust_type'],
                $_POST['adjust_value'],
                $auto_add,
                $_POST['branch'],
                $status
            );
            if ($newId) {
                header("Location: index.php?action=product_price&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/product/price_add.php';
    }

    public function add_conversion()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $parent_id = $_POST['parent_id'];
            $unit = $_POST['unit'];
            $conversion_qty = $_POST['conversion_qty'];
            $sku = $_POST['sku'];
            $barcode = $_POST['barcode'];

            // Lọc định dạng tiền
            $base_price = (float)str_replace(['.', ','], '', $_POST['base_price'] ?? $_POST['price'] ?? 0);

            $baseProduct = $productModel->getProductById($parent_id);
            $newName = $baseProduct['product_name'] . ' (' . $unit . ' ' . $conversion_qty . ' ' . $baseProduct['unit'] . ')';

            if ($productModel->addConvertedProduct($parent_id, $newName, $unit, $conversion_qty, $sku, $barcode, $base_price)) {
                header("Location: index.php?action=product_list&success=1");
                exit;
            }
        }

        $baseProducts = $productModel->getBaseProducts();
        require_once __DIR__ . '/../views/product/add_conversion.php';
    }

    public function add_combo()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $imagePath = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            // Lọc định dạng tiền
            $base_price = (float)str_replace(['.', ','], '', $_POST['base_price'] ?? $_POST['price'] ?? 0);
            $compare_price = (float)str_replace(['.', ','], '', $_POST['compare_price'] ?? 0);

            $component_ids = $_POST['component_id'] ?? [];
            $component_qtys = $_POST['component_qty'] ?? [];

            $newId = $productModel->addComboProduct(
                $_POST['product_name'] ?? '',
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $base_price,
                $compare_price,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['brand'] ?? '',
                $_POST['tags'] ?? '',
                $component_ids,
                $component_qtys
            );

            if ($newId) {
                header("Location: index.php?action=product_list&success=1");
                exit;
            }
        }

        $baseProducts = $productModel->getBaseProducts();
        require_once __DIR__ . '/../views/product/add_combo.php';
    }

    public function quick_update_stock()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $db = (new Database())->getConnection();
            $productModel = new ProductModel($db);

            $id = $_POST['id'];
            $new_stock = (int)$_POST['new_stock'];

            $product = $productModel->getProductById($id);
            if ($product) {
                $current_stock = $product['stock'] ?? 0;
                $current_available = $product['available'] ?? 0;

                $stock_diff = $new_stock - $current_stock;

                if ($stock_diff != 0) {
                    $new_available = $current_available + $stock_diff;
                    $productModel->updateInventory($id, $new_stock, $new_available);
                }
            }

            header("Location: index.php?action=product_list&success=1");
            exit;
        }
    }
}
