<?php
// Đường dẫn: app/controllers/ExpenseController.php
require_once __DIR__ . '/../../config/database.php';

class ExpenseController
{
    // 1. GIAO DIỆN TẠO PHIẾU CHI THỦ CÔNG MULTI-OBJECT
    public function create()
    {
        $db = (new Database())->getConnection();

        // Kéo dữ liệu phục vụ các Dropdown đối tượng nhận
        $customers = $db->query("SELECT id, last_name, first_name, phone FROM customers")->fetchAll(PDO::FETCH_ASSOC);

        // Thêm khối try-catch đề phòng Khương chưa làm bảng Nhà cung cấp hoặc Nhân viên
        try {
            $suppliers = $db->query("SELECT id, supplier_name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $suppliers = [['id' => 1, 'supplier_name' => 'Nhà cung cấp Apple VN']];
        }

        try {
            $employees = $db->query("SELECT id, full_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $employees = [['id' => 1, 'full_name' => 'Nguyễn Văn Nhân Viên']];
        }

        // Kéo danh sách chi nhánh và tài khoản ngân hàng nhận tiền
        try {
            $branches = $db->query("SELECT id, branch_name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhánh Trung tâm']];
        }

        try {
            $bank_accounts = $db->query("SELECT id, bank_name, account_number FROM bank_accounts")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bank_accounts = [['id' => 1, 'bank_name' => 'Techcombank', 'account_number' => '1903123']];
        }

        require_once __DIR__ . '/../views/fund/expense_create.php';
    }

    // 2. THUẬT TOÁN LƯU PHIẾU CHI VÀ TỰ ĐỘNG PHÂN LUỒNG HẠCH TOÁN
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_method = $_POST['payment_method'];
            $bank_account_id = ($payment_method === 'bank') ? $_POST['bank_account_id'] : null;

            $recipient_group = $_POST['recipient_group'];
            $recipient_id = !empty($_POST['recipient_id']) ? intval($_POST['recipient_id']) : null;
            $recipient_name = trim($_POST['recipient_name']);

            $amount = floatval($_POST['amount']);
            $expense_reason = $_POST['expense_reason'];
            $expense_category = !empty($_POST['expense_category']) ? $_POST['expense_category'] : 'Chi phí vận hành';
            $description = trim($_POST['description']);

            $branch_id = intval($_POST['branch_id']);
            $transaction_date = $_POST['transaction_date'];

            // Xử lý Cơ chế Tự sinh mã phiếu chi chuẩn Hệ thống (Nếu để trống)
            $expense_code = trim($_POST['expense_code']);
            if (empty($expense_code)) {
                $expense_code = 'PC' . date('YmdHis') . rand(10, 99);
            }

            $is_debt_affected = isset($_POST['is_debt_affected']) ? 1 : 0;
            $reference_code = trim($_POST['reference_code'] ?? '');

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                // Bước 1: Lưu phiếu chi thủ công vào bảng expenses
                $stmt = $db->prepare("
                    INSERT INTO expenses 
                    (expense_code, payment_method, recipient_group, recipient_id, recipient_name, amount, expense_reason, expense_category, is_debt_affected, is_automatic, description, branch_id, transaction_date, reference_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $expense_code,
                    $payment_method,
                    $recipient_group,
                    $recipient_id,
                    $recipient_name,
                    $amount,
                    $expense_reason,
                    $expense_category,
                    $is_debt_affected,
                    $description,
                    $branch_id,
                    $transaction_date,
                    $reference_code
                ]);

                // Bước 2: Kiểm tra nghiệp vụ tích chọn cập nhật công nợ (Chỉ áp dụng khi chi cho Khách hàng hoặc Nhà cung cấp)
                if ($is_debt_affected === 1 && $recipient_group === 'customer' && !empty($recipient_id)) {
                    // Tăng công nợ khách hàng (Khách nợ thêm cửa hàng)
                    $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                    $stmt_cus->execute([$recipient_id]);
                    $current_debt = floatval($stmt_cus->fetchColumn());

                    $new_debt = $current_debt + $amount;

                    $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                    $stmt_upd->execute([$new_debt, $recipient_id]);

                    // Ghi Sổ chi tiết công nợ
                    $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_increase, balance, description) VALUES (?, 'payment', ?, ?, ?, ?)");
                    $stmt_log->execute([$recipient_id, $expense_code, $amount, $new_debt, "Hạch toán tăng nợ từ phiếu chi thủ công $expense_code"]);
                }

                $db->commit();

                // Trả về trang danh sách (Tạm chuyển về danh sách khách hàng hoặc màn hình công nợ tùy tích chọn)
                if ($is_debt_affected === 1 && $recipient_group === 'customer') {
                    header("Location: index.php?action=customer_debt&id=$recipient_id&success=expense_created");
                } else {
                    header("Location: index.php?action=customer_list&success=expense_created_manual");
                }
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi hệ thống khi tạo phiếu chi thủ công: " . $e->getMessage());
            }
        }
    }
    // 3. DANH SÁCH PHIẾU CHI (Hỗ trợ Xóa hàng loạt)
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM expenses ORDER BY created_at DESC");
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/fund/expense_list.php';
    }

    // 4. XEM CHI TIẾT & IN PHIẾU CHI
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("SELECT e.*, b.branch_name, ba.bank_name, ba.account_number 
                              FROM expenses e 
                              LEFT JOIN branches b ON e.branch_id = b.id 
                              LEFT JOIN bank_accounts ba ON e.bank_account_id = ba.id 
                              WHERE e.id = ?");
        $stmt->execute([$id]);
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$expense) die("Không tìm thấy phiếu chi!");

        require_once __DIR__ . '/../views/fund/expense_detail.php';
    }

    // 5. CẬP NHẬT PHIẾU CHI (Khóa cứng theo luật Hệ thống)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);
            // Các trường khác như Số tiền, Đối tượng, Chi nhánh không được phép lấy từ POST để chống hack

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE expenses SET description = ?, reference_code = ? WHERE id = ?");
            $stmt->execute([$description, $reference_code, $id]);

            header("Location: index.php?action=expense_detail&id=$id&success=updated");
            exit;
        }
    }

    // 6. XÓA PHIẾU CHI (Đơn lẻ & Hàng loạt kèm Hoàn tác Công nợ)
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = isset($data['ids']) ? $data['ids'] : [$data['id']];

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    // 1. Lấy thông tin phiếu chi trước khi xóa
                    $stmt_ex = $db->prepare("SELECT amount, is_debt_affected, recipient_group, recipient_id, expense_code FROM expenses WHERE id = ?");
                    $stmt_ex->execute([$id]);
                    $expense = $stmt_ex->fetch(PDO::FETCH_ASSOC);

                    if ($expense) {
                        // 2. Nếu phiếu chi này từng làm TĂNG công nợ khách -> Xóa phiếu thì phải GIẢM công nợ trả lại
                        if ($expense['is_debt_affected'] == 1 && $expense['recipient_group'] == 'customer' && $expense['recipient_id']) {
                            $customer_id = $expense['recipient_id'];

                            $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                            $stmt_cus->execute([$customer_id]);
                            $current_debt = floatval($stmt_cus->fetchColumn());

                            $new_debt = $current_debt - $expense['amount']; // Trừ ngược lại

                            $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                            $stmt_upd->execute([$new_debt, $customer_id]);

                            // Ghi log hoàn tác
                            $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_decrease, balance, description) VALUES (?, 'adjustment', ?, ?, ?, ?)");
                            $stmt_log->execute([$customer_id, $expense['expense_code'], $expense['amount'], $new_debt, "Hoàn tác xóa phiếu chi " . $expense['expense_code']]);
                        }

                        // 3. Xóa phiếu chi
                        $stmt_del = $db->prepare("DELETE FROM expenses WHERE id = ?");
                        $stmt_del->execute([$id]);
                    }
                }

                $db->commit();
                echo json_encode(['status' => 'success', 'msg' => 'Đã xóa chứng từ và hoàn tác công nợ (nếu có) thành công!']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}
