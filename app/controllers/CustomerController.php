<?php
// Đường dẫn: app/controllers/CustomerController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/CustomerModel.php';

class CustomerController
{

    public function list()
    {
        $db = (new Database())->getConnection();
        $customerModel = new CustomerModel($db);
        $search = $_GET['search'] ?? '';
        $customers = $customerModel->getAllCustomers($search);
        require_once __DIR__ . '/../views/customer/list.php';
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            $customerModel = new CustomerModel($db);

            $id = $customerModel->addCustomer(
                $_POST['customer_code'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['first_name'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                isset($_POST['accept_marketing']) ? 1 : 0,
                $_POST['province'] ?? '',
                $_POST['district'] ?? '',
                $_POST['ward'] ?? '',
                $_POST['address'] ?? '',
                $_POST['tax_code'] ?? '',
                $_POST['company_name'] ?? '',
                $_POST['invoice_address'] ?? '',
                $_POST['invoice_email'] ?? '',
                $_POST['notes'] ?? '',
                $_POST['tags'] ?? ''
            );

            if ($id) {
                header("Location: index.php?action=edit_customer&id=$id&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/customer/add.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $customerModel = new CustomerModel($db);

        $customer = $customerModel->getCustomerById($id);
        if (!$customer) {
            header("Location: index.php?action=customer_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($customerModel->updateCustomer(
                $id,
                $_POST['customer_code'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['first_name'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                isset($_POST['accept_marketing']) ? 1 : 0,
                $_POST['province'] ?? '',
                $_POST['district'] ?? '',
                $_POST['ward'] ?? '',
                $_POST['address'] ?? '',
                $_POST['tax_code'] ?? '',
                $_POST['company_name'] ?? '',
                $_POST['invoice_address'] ?? '',
                $_POST['invoice_email'] ?? '',
                $_POST['notes'] ?? '',
                $_POST['tags'] ?? ''
            )) {
                header("Location: index.php?action=edit_customer&id=$id&success=1");
                exit;
            }
        }
        require_once __DIR__ . '/../views/customer/edit.php';
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new Database())->getConnection();
            (new CustomerModel($db))->deleteCustomer($id);
        }
        header("Location: index.php?action=customer_list&success_delete=1");
        exit;
    }
}
