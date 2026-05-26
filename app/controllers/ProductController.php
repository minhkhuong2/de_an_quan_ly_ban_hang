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

        // Lấy dữ liệu lọc từ URL (Nếu có)
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $tags = $_GET['tags'] ?? '';
        $type = $_GET['type'] ?? '';

        $productModel = new ProductModel($db);
        $categoryModel = new CategoryModel($db); // Gọi model danh mục

        $products = $productModel->getProductsWithStock($search, $category, $brand, $tags, $type);

        // GẮN DANH MỤC THÔNG MINH CHO TỪNG SẢN PHẨM Ở ĐÂY
        foreach ($products as $key => $prod) {
            $products[$key]['smart_categories'] = $categoryModel->getCategoriesOfProduct($prod);
        }

        // Lấy danh sách danh mục để đổ vào ô Dropdown lọc
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

            // Đếm đủ 13 tham số bắn sang Model
            $is_added = $productModel->addProduct(
                $_POST['product_name'] ?? '',
                $_POST['brand'] ?? '',
                $_POST['base_price'] ?? 0,
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $_POST['compare_price'] ?? 0,
                $_POST['cost_price'] ?? 0,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['tags'] ?? ''
            );

            if ($is_added) {
                header("Location: index.php?action=product_list&success=1");
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

            // Đếm đủ 14 tham số bắn sang Model (ID + 13 dữ liệu)
            $is_updated = $productModel->updateProduct(
                $id,
                $_POST['product_name'] ?? '',
                $_POST['brand'] ?? '',
                $_POST['base_price'] ?? 0,
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $_POST['compare_price'] ?? 0,
                $_POST['cost_price'] ?? 0,
                isset($_POST['apply_tax']) ? 1 : 0,
                $_POST['category'] ?? '',
                $_POST['tags'] ?? ''
            );

            if ($is_updated) {
                $message = "<div style='background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;'>✅ Cập nhật sản phẩm thành công!</div>";
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
        // Bắt buộc phải có 2 dòng này để làm sạch URL và chuyển hướng về danh sách
        header("Location: index.php?action=product_list");
        exit;
    }

    // ==============================================
    // CÁC HÀM DANH MỤC ĐÃ ĐƯỢC FIX LỖI BÁO ĐỎ 
    // ==============================================
    public function category_list()
    {
        $db = (new Database())->getConnection();

        // Nhận dữ liệu tìm kiếm & lọc
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        $categories = (new CategoryModel($db))->getAllCategories($search, $type);
        require_once __DIR__ . '/../views/product/category_list.php';
    }

    public function add_category()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();

            // Lấy dữ liệu điều kiện tự động chuyển thành chuỗi JSON
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

            // Bơm ĐỦ 10 THAM SỐ cho Model của bạn để VS Code khỏi kêu
            $newId = (new CategoryModel($db))->addCategory(
                $_POST['category_name'] ?? '',
                $_POST['description'] ?? '',
                '', // alias (Bỏ trống ngầm)
                '', // seo_title (Bỏ trống ngầm)
                '', // seo_desc (Bỏ trống ngầm)
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
            // Lấy dữ liệu điều kiện tự động chuyển thành chuỗi JSON
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

            // Bơm ĐỦ 11 THAM SỐ cho hàm update (có thêm $id)
            if ($model->updateCategory(
                $id,
                $_POST['category_name'] ?? '',
                $_POST['description'] ?? '',
                '', // alias (Bỏ trống ngầm)
                '', // seo_title (Bỏ trống ngầm)
                '', // seo_desc (Bỏ trống ngầm)
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
            // Hàm này 6 tham số khớp y chang Model của bạn rồi nên không lỗi
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
    // Hàm xử lý Thêm Sản phẩm Quy đổi
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
            $base_price = $_POST['base_price'];

            // Lấy thông tin sản phẩm mẹ để nối tên
            $baseProduct = $productModel->getProductById($parent_id);
            // Tự động tạo tên: Ví dụ "Kính cường lực (Lốc 10 Cái)"
            $newName = $baseProduct['product_name'] . ' (' . $unit . ' ' . $conversion_qty . ' ' . $baseProduct['unit'] . ')';

            if ($productModel->addConvertedProduct($parent_id, $newName, $unit, $conversion_qty, $sku, $barcode, $base_price)) {
                // Quay về danh sách và báo thành công
                header("Location: index.php?action=product_list&success=1");
                exit;
            }
        }

        // Lấy danh sách sản phẩm mẹ truyền ra View
        $baseProducts = $productModel->getBaseProducts();
        require_once __DIR__ . '/../views/product/add_conversion.php';
    }
    // Thêm Sản phẩm Combo
    public function add_combo()
    {
        $db = (new Database())->getConnection();
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Xử lý upload ảnh
            $imagePath = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            $component_ids = $_POST['component_id'] ?? [];
            $component_qtys = $_POST['component_qty'] ?? [];

            $newId = $productModel->addComboProduct(
                $_POST['product_name'] ?? '',
                $_POST['sku'] ?? '',
                $_POST['barcode'] ?? '',
                $_POST['unit'] ?? '',
                $_POST['description'] ?? '',
                $imagePath,
                $_POST['base_price'] ?? 0,
                $_POST['compare_price'] ?? 0,
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

        // Lấy danh sách sản phẩm gốc để chọn làm thành phần Combo
        $baseProducts = $productModel->getBaseProducts();
        require_once __DIR__ . '/../views/product/add_combo.php';
    }
}
