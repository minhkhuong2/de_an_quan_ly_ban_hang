<?php
// Đường dẫn: app/controllers/SupplierController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SupplierModel.php';

class SupplierController
{

    // Hiển thị danh sách Nhà cung cấp
    public function list()
    {
        $db = (new Database())->getConnection();
        $supplierModel = new SupplierModel($db);

        // Hứng dữ liệu tìm kiếm và bộ lọc từ thanh URL
        $search = $_GET['search'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $suppliers = $supplierModel->getAllSuppliers($search, $start_date, $end_date);
        require_once __DIR__ . '/../views/supplier/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $supplierModel = new SupplierModel($db);

            $code = $_POST['supplier_code'] ?? '';
            $name = $_POST['supplier_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
            $address = $_POST['address'] ?? '';
            $tax_code = $_POST['tax_code'] ?? '';

            if (!empty($name)) {
                $supplierModel->addSupplier($code, $name, $phone, $email, $address, $tax_code);
                header("Location: index.php?action=supplier_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/supplier/add.php';
    }
    // Sửa thông tin Nhà cung cấp
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $supplierModel = new SupplierModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $code = $_POST['supplier_code'] ?? '';
            $name = $_POST['supplier_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
            $address = $_POST['address'] ?? '';
            $tax_code = $_POST['tax_code'] ?? '';

            if (!empty($name)) {
                $supplierModel->updateSupplier($id, $code, $name, $phone, $email, $address, $tax_code);
                header("Location: index.php?action=supplier_list&success_edit=1");
                exit;
            }
        }

        $supplier = $supplierModel->getSupplierById($id);
        if (!$supplier) {
            header("Location: index.php?action=supplier_list");
            exit;
        }

        require_once __DIR__ . '/../views/supplier/edit.php';
    }

    // Xóa Nhà cung cấp
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new Database())->getConnection();
            $supplierModel = new SupplierModel($db);
            $supplierModel->deleteSupplier($id);
        }
        header("Location: index.php?action=supplier_list&success_delete=1");
        exit;
    }
}
