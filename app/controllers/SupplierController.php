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
            if ((new SupplierModel($db))->addSupplier($_POST['supplier_name'], $_POST['phone'], $_POST['email'] ?? '', $_POST['address'] ?? '')) {
                header("Location: index.php?action=supplier_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/supplier/add.php';
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
