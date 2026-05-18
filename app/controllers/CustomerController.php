<?php
// Đường dẫn file: app/controllers/CustomerController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/CustomerModel.php';

class CustomerController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $customers = (new CustomerModel($db))->getAllCustomers();
        require_once __DIR__ . '/../views/customer/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            if ((new CustomerModel($db))->addCustomer($_POST['customer_name'], $_POST['phone'], $_POST['email'] ?? '', $_POST['address'] ?? '', $_POST['customer_group'] ?? 'Khách lẻ')) {
                header("Location: index.php?action=customer_list&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/customer/add.php';
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $db = (new Database())->getConnection();
            (new CustomerModel($db))->deleteCustomer($_GET['id']);
        }
        header("Location: index.php?action=customer_list");
        exit;
    }
}
