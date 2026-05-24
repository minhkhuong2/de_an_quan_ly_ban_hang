<?php
// Đường dẫn file: app/controllers/SupplierController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SupplierModel.php';

class SupplierController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $suppliers = (new SupplierModel($db))->getAllSuppliers();
        require_once __DIR__ . '/../views/supplier/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $model = new SupplierModel($db);

            if ($model->addSupplier(
                $_POST['supplier_name'] ?? '',
                $_POST['supplier_code'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_POST['supplier_group'] ?? '',
                $_POST['address'] ?? '',
                $_POST['fax'] ?? '',
                $_POST['tax_code'] ?? '',
                $_POST['website'] ?? '',
                $_POST['debt'] ?? 0,
                $_POST['assignee'] ?? '',
                $_POST['description'] ?? '',
                $_POST['tags'] ?? '',
                $_POST['tax_setting'] ?? '',
                $_POST['default_import_price'] ?? '',
                $_POST['status'] ?? 'Đang giao dịch'
            )) {
                header("Location: index.php?action=supplier_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/supplier/add.php';
    }

    public function edit()
    {
        $db = (new Database())->getConnection();
        $model = new SupplierModel($db);
        $id = $_GET['id'] ?? 0;
        $message = "";

        $supplier = $model->getSupplierById($id);
        if (!$supplier) {
            header("Location: index.php?action=supplier_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($model->updateSupplier(
                $id,
                $_POST['supplier_name'] ?? '',
                $_POST['supplier_code'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_POST['supplier_group'] ?? '',
                $_POST['address'] ?? '',
                $_POST['fax'] ?? '',
                $_POST['tax_code'] ?? '',
                $_POST['website'] ?? '',
                $_POST['debt'] ?? 0,
                $_POST['assignee'] ?? '',
                $_POST['description'] ?? '',
                $_POST['tags'] ?? '',
                $_POST['tax_setting'] ?? '',
                $_POST['default_import_price'] ?? '',
                $_POST['status'] ?? 'Đang giao dịch'
            )) {
                $message = "<div style='background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;'>✅ Cập nhật thông tin nhà cung cấp thành công!</div>";
                $supplier = $model->getSupplierById($id); // Tải lại dữ liệu mới nhất
            }
        }
        require_once __DIR__ . '/../views/supplier/edit.php';
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $db = (new Database())->getConnection();
            (new SupplierModel($db))->deleteSupplier($_GET['id']);
        }
        header("Location: index.php?action=supplier_list");
        exit;
    }
}
