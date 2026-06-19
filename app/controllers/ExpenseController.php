<?php
// Đường dẫn: app/controllers/ExpenseController.php
require_once __DIR__ . '/../../config/database.php';

class ExpenseController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 1. GIAO DIỆN TẠO PHIẾU CHI
    public function create()
    {
        // Lấy danh sách Khách hàng
        $stmt_cus = $this->db->query("SELECT id, customer_code, last_name, first_name, phone, debt FROM customers");
        $customers = $stmt_cus->fetchAll(PDO::FETCH_ASSOC);

        // Lấy Chi nhánh và Ngân hàng
        try {
            $branches = $this->db->query("SELECT id, branch_name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhánh Trung tâm']];
        }

        try {
            $bank_accounts = $this->db->query("SELECT id, bank_name, account_number FROM bank_accounts")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bank_accounts = [['id' => 1, 'bank_name' => 'Techcombank', 'account_number' => '1903123']];
        }

        require_once __DIR__ . '/../views/fund/expense_create.php';
    }

    // 2. XỬ LÝ LƯU PHIẾU CHI & HẠCH TOÁN TĂNG CÔNG NỢ
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $amount = floatval($_POST['amount']);
            $payment_method = $_POST['payment_method'];
            $bank_account_id = ($payment_method === 'bank') ? $_POST['bank_account_id'] : null;
            $is_debt_affected = isset($_POST['is_debt_affected']) ? 1 : 0; // Checkbox hạch toán công nợ
            $description = trim($_POST['description']);
            $transaction_date = $_POST['transaction_date'];

            $expense_code = trim($_POST['expense_code']);
            if (empty($expense_code)) $expense_code = 'PC' . date('YmdHis');

            try {
                $this->db->beginTransaction();

                // 1. Lưu Phiếu Chi vào Sổ Quỹ
                $stmt_expense = $this->db->prepare("INSERT INTO expenses (expense_code, payment_method, bank_account_id, customer_id, amount, is_debt_affected, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_expense->execute([$expense_code, $payment_method, $bank_account_id, $customer_id, $amount, $is_debt_affected, $description, $transaction_date]);

                // 2. Nếu có tích chọn "Hạch toán công nợ" -> Tăng công nợ khách hàng
                if ($is_debt_affected == 1) {
                    // Lấy nợ hiện tại
                    $stmt_check = $this->db->prepare("SELECT debt FROM customers WHERE id = ?");
                    $stmt_check->execute([$customer_id]);
                    $current_debt = floatval($stmt_check->fetchColumn());

                    $new_debt = $current_debt + $amount; // Khách nợ thêm

                    // Cập nhật bảng customers
                    $stmt_upd_cus = $this->db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                    $stmt_upd_cus->execute([$new_debt, $customer_id]);

                    // Ghi Sổ chi tiết công nợ (transaction_type = 'payment' - Phiếu chi)
                    $stmt_log = $this->db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_increase, balance, description) VALUES (?, 'payment', ?, ?, ?, ?)");
                    $stmt_log->execute([$customer_id, $expense_code, $amount, $new_debt, "Hạch toán tăng công nợ từ phiếu chi $expense_code"]);
                }

                $this->db->commit();

                // Nếu có hạch toán nợ thì về trang Sổ nợ, không thì về danh sách phiếu thu/chi (tạm về danh sách khách)
                if ($is_debt_affected == 1) {
                    header("Location: index.php?action=customer_debt&id=$customer_id&success=expense_created");
                } else {
                    header("Location: index.php?action=customer_list&success=expense_created");
                }
                exit;
            } catch (Exception $e) {
                $this->db->rollBack();
                die("Lỗi hệ thống khi tạo phiếu chi: " . $e->getMessage());
            }
        }
    }
}
