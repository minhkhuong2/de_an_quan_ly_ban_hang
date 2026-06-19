<?php
// Đường dẫn: app/controllers/ReceiptController.php
require_once __DIR__ . '/../../config/database.php';

class ReceiptController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 1. GIAO DIỆN TẠO PHIẾU THU CÔNG NỢ
    public function create()
    {
        // Lấy danh sách Khách hàng ĐANG CÓ NỢ (> 0)
        $stmt_cus = $this->db->query("SELECT id, customer_code, last_name, first_name, phone, debt FROM customers WHERE debt > 0");
        $customers = $stmt_cus->fetchAll(PDO::FETCH_ASSOC);

        // Lấy Chi nhánh và Ngân hàng (Dùng tạm Query tĩnh nếu bạn chưa có bảng)
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

        require_once __DIR__ . '/../views/fund/receipt_create.php';
    }

    // 2. XỬ LÝ LƯU PHIẾU THU & THUẬT TOÁN PHÂN BỔ TIỀN
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $amount = floatval($_POST['amount']);
            $payment_method = $_POST['payment_method'];
            $bank_account_id = ($payment_method === 'bank') ? $_POST['bank_account_id'] : null;
            $payment_strategy = $_POST['payment_strategy']; // oldest_first hoặc newest_first
            $description = trim($_POST['description']);
            $transaction_date = $_POST['transaction_date'];

            $receipt_code = trim($_POST['receipt_code']);
            if (empty($receipt_code)) $receipt_code = 'PT' . date('YmdHis');

            // KIỂM TRA BẢO MẬT: Số tiền thu không được lớn hơn dư nợ
            $stmt_check = $this->db->prepare("SELECT debt FROM customers WHERE id = ?");
            $stmt_check->execute([$customer_id]);
            $current_debt = floatval($stmt_check->fetchColumn());

            if ($amount > $current_debt) {
                die("Lỗi: Số tiền thanh toán ($amount) không được vượt quá dư nợ hiện tại của khách hàng ($current_debt).");
            }

            try {
                $this->db->beginTransaction();

                // BƯỚC 1: LƯU PHIẾU THU
                $stmt_receipt = $this->db->prepare("INSERT INTO receipts (receipt_code, payment_method, bank_account_id, customer_id, amount, payment_strategy, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_receipt->execute([$receipt_code, $payment_method, $bank_account_id, $customer_id, $amount, $payment_strategy, $description, $transaction_date]);

                // BƯỚC 2: THUẬT TOÁN PHÂN BỔ TIỀN VÀO CÁC ĐƠN HÀNG
                $order_direction = ($payment_strategy === 'newest_first') ? 'DESC' : 'ASC';
                // Lấy các đơn chưa thanh toán hết của khách này
                $stmt_orders = $this->db->prepare("SELECT * FROM orders WHERE customer_id = ? AND payment_status IN ('unpaid', 'partial') ORDER BY created_at $order_direction");
                $stmt_orders->execute([$customer_id]);
                $unpaid_orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

                $remaining_amount = $amount;

                foreach ($unpaid_orders as $order) {
                    if ($remaining_amount <= 0) break; // Hết tiền để rải thì dừng lặp

                    $need_to_pay = $order['total_amount'] - $order['paid_amount'];
                    // Lấy số nhỏ hơn giữa (Tiền cần trả cho đơn này) và (Tiền khách đưa còn thừa)
                    $pay_for_this_order = min($need_to_pay, $remaining_amount);

                    $new_paid_amount = $order['paid_amount'] + $pay_for_this_order;
                    $new_status = ($new_paid_amount >= $order['total_amount']) ? 'paid' : 'partial';

                    // Cập nhật lại đơn hàng
                    $stmt_update_order = $this->db->prepare("UPDATE orders SET paid_amount = ?, payment_status = ? WHERE id = ?");
                    $stmt_update_order->execute([$new_paid_amount, $new_status, $order['id']]);

                    // Trừ đi số tiền vừa rải
                    $remaining_amount -= $pay_for_this_order;
                }

                // BƯỚC 3: GIẢM TỔNG CÔNG NỢ KHÁCH HÀNG
                $new_debt = $current_debt - $amount;
                $stmt_upd_cus = $this->db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                $stmt_upd_cus->execute([$new_debt, $customer_id]);

                // BƯỚC 4: GHI SỔ CHI TIẾT CÔNG NỢ (Lịch sử)
                $stmt_log = $this->db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_decrease, balance, description) VALUES (?, 'receipt', ?, ?, ?, ?)");
                $stmt_log->execute([$customer_id, $receipt_code, $amount, $new_debt, "Khách thanh toán công nợ theo phiếu thu $receipt_code"]);

                $this->db->commit();
                // Chuyển hướng về trang Sổ chi tiết công nợ để xem thành quả
                header("Location: index.php?action=customer_debt&id=$customer_id&success=receipt_created");
                exit;
            } catch (Exception $e) {
                $this->db->rollBack();
                die("Lỗi hệ thống khi thanh toán: " . $e->getMessage());
            }
        }
    }
}
