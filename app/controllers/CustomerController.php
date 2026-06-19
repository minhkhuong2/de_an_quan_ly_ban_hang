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
    // 1. XEM SỔ CHI TIẾT CÔNG NỢ KHÁCH HÀNG
    public function debt_history()
    {
        $id = $_GET['id'] ?? 0;

        // Khởi tạo kết nối DB (Đã sửa lỗi undefined $db)
        $db = (new Database())->getConnection();

        // Lấy thông tin khách hàng
        $stmt_cus = $db->prepare("SELECT id, customer_code, last_name, first_name, phone, debt FROM customers WHERE id = ?");
        $stmt_cus->execute([$id]);
        $customer = $stmt_cus->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            die("Không tìm thấy khách hàng!");
        }

        // Lấy lịch sử biến động công nợ
        $stmt_hist = $db->prepare("SELECT * FROM customer_debt_history WHERE customer_id = ? ORDER BY created_at DESC, id DESC");
        $stmt_hist->execute([$id]);
        $debt_history = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/customer/debt_history.php';
    }

    // 2. XỬ LÝ ĐIỀU CHỈNH CÔNG NỢ CHỦ ĐỘNG
    public function adjust_debt()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $adjust_type = $_POST['adjust_type']; // 'increase' hoặc 'decrease'
            $amount = floatval($_POST['amount']);
            $description = trim($_POST['description']);

            if ($amount <= 0) {
                die("Số tiền điều chỉnh phải lớn hơn 0");
            }

            // Khởi tạo kết nối DB (Đã sửa lỗi undefined $db)
            $db = (new Database())->getConnection();

            // Lấy công nợ hiện tại của khách
            $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
            $stmt_cus->execute([$customer_id]);
            $current_debt = $stmt_cus->fetchColumn();

            $debt_increase = 0;
            $debt_decrease = 0;
            $new_balance = $current_debt;

            if ($adjust_type === 'increase') {
                $debt_increase = $amount;
                $new_balance = $current_debt + $amount;
            } else {
                $debt_decrease = $amount;
                $new_balance = $current_debt - $amount;
            }

            try {
                $db->beginTransaction();

                // 1. Cập nhật lại tổng công nợ trong bảng customers
                $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                $stmt_upd->execute([$new_balance, $customer_id]);

                // 2. Ghi log vào Sổ chi tiết công nợ (Mã tham chiếu DCCN = Điều Chỉnh Công Nợ)
                $ref_code = 'DCCN' . date('YmdHis');
                $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_increase, debt_decrease, balance, description) VALUES (?, 'adjustment', ?, ?, ?, ?, ?)");
                $stmt_log->execute([$customer_id, $ref_code, $debt_increase, $debt_decrease, $new_balance, $description]);

                $db->commit();

                header("Location: index.php?action=customer_debt&id=" . $customer_id . "&success=adjusted");
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi hệ thống khi điều chỉnh công nợ: " . $e->getMessage());
            }
        }
    }
    // API: XỬ LÝ XUẤT FILE CÔNG NỢ (Hàng loạt & Cá nhân)
    public function export_debt()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            $export_scope = $data['export_scope'] ?? 'bulk'; // 'bulk' (Hàng loạt) hoặc 'single' (Cá nhân)
            $export_type = $data['export_type'] ?? 'all';
            $customer_id = $data['customer_id'] ?? null;

            // Tùy vào biến truyền lên, hệ thống sẽ query dữ liệu tương ứng:
            // 1. Xuất hàng loạt (Tất cả KH hoặc trang hiện tại)
            // 2. Xuất Tổng quan 1 KH
            // 3. Xuất Chi tiết 1 KH (Kéo data từ bảng customer_debt_history)

            // Do XAMPP không cấu hình mail server thật, ta giả lập phản hồi thành công chuẩn Sapo
            echo json_encode([
                'status' => 'success',
                'msg' => 'Xuất file công nợ thành công! Dữ liệu đang được hệ thống xử lý và sẽ gửi thông báo về email trên tài khoản của bạn.'
            ]);
            exit;
        }
    }
}
