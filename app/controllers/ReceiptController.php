<?php
// Đường dẫn: app/controllers/ReceiptController.php
require_once __DIR__ . '/../../config/database.php';

class ReceiptController
{
    // 1. GIAO DIỆN TẠO PHIẾU THU THỦ CÔNG ĐA ĐỐI TƯỢNG
    public function create()
    {
        $db = (new Database())->getConnection();

        // Kéo dữ liệu phục vụ Dropdown (Khách hàng ưu tiên người đang CÓ NỢ)
        $customers = $db->query("SELECT id, last_name, first_name, phone, debt FROM customers")->fetchAll(PDO::FETCH_ASSOC);

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

        require_once __DIR__ . '/../views/fund/receipt_create.php';
    }

    // 2. XỬ LÝ LƯU PHIẾU THU & KÍCH HOẠT THUẬT TOÁN FIFO (NẾU LÀ KHÁCH HÀNG)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_method = $_POST['payment_method'];
            $bank_account_id = ($payment_method === 'bank') ? $_POST['bank_account_id'] : null;

            $payer_group = $_POST['payer_group'];
            $payer_id = !empty($_POST['payer_id']) ? intval($_POST['payer_id']) : null;
            $payer_name = trim($_POST['payer_name']);

            $amount = floatval($_POST['amount']);
            $receipt_reason = $_POST['receipt_reason'];
            $payment_strategy = $_POST['payment_strategy'] ?? 'oldest_first';
            $description = trim($_POST['description']);

            $branch_id = intval($_POST['branch_id']);
            $transaction_date = $_POST['transaction_date'];
            $reference_code = trim($_POST['reference_code'] ?? '');

            $receipt_code = trim($_POST['receipt_code']);
            if (empty($receipt_code)) $receipt_code = 'PT' . date('YmdHis') . rand(10, 99);

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                // Bước 1: Lưu Phiếu Thu vào bảng receipts
                $stmt = $db->prepare("
                    INSERT INTO receipts 
                    (receipt_code, payment_method, bank_account_id, payer_group, payer_id, payer_name, amount, receipt_reason, payment_strategy, is_automatic, description, branch_id, transaction_date, reference_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $receipt_code,
                    $payment_method,
                    $bank_account_id,
                    $payer_group,
                    $payer_id,
                    $payer_name,
                    $amount,
                    $receipt_reason,
                    $payment_strategy,
                    $description,
                    $branch_id,
                    $transaction_date,
                    $reference_code
                ]);

                // Bước 2: NẾU ĐỐI TƯỢNG LÀ KHÁCH HÀNG -> KÍCH HOẠT RẢI TIỀN FIFO TRỪ NỢ
                if ($payer_group === 'customer' && !empty($payer_id)) {
                    $stmt_check = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                    $stmt_check->execute([$payer_id]);
                    $current_debt = floatval($stmt_check->fetchColumn());

                    // Thuật toán rải tiền vào các đơn hàng đang nợ (FIFO)
                    $order_direction = ($payment_strategy === 'newest_first') ? 'DESC' : 'ASC';
                    $stmt_orders = $db->prepare("SELECT * FROM orders WHERE customer_id = ? AND payment_status IN ('unpaid', 'partial') ORDER BY created_at $order_direction");
                    $stmt_orders->execute([$payer_id]);
                    $unpaid_orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

                    $remaining_amount = $amount;
                    foreach ($unpaid_orders as $order) {
                        if ($remaining_amount <= 0) break;
                        $need_to_pay = $order['total_amount'] - $order['paid_amount'];
                        $pay_for_this_order = min($need_to_pay, $remaining_amount);

                        $new_paid_amount = $order['paid_amount'] + $pay_for_this_order;
                        $new_status = ($new_paid_amount >= $order['total_amount']) ? 'paid' : 'partial';

                        $stmt_update_order = $db->prepare("UPDATE orders SET paid_amount = ?, payment_status = ? WHERE id = ?");
                        $stmt_update_order->execute([$new_paid_amount, $new_status, $order['id']]);
                        $remaining_amount -= $pay_for_this_order;
                    }

                    // Giảm tổng công nợ khách hàng & Ghi Sổ chi tiết
                    $new_debt = $current_debt - $amount;
                    $stmt_upd_cus = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                    $stmt_upd_cus->execute([$new_debt, $payer_id]);

                    $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_decrease, balance, description) VALUES (?, 'receipt', ?, ?, ?, ?)");
                    $stmt_log->execute([$payer_id, $receipt_code, $amount, $new_debt, "Khách thanh toán công nợ theo phiếu thu $receipt_code"]);

                    $db->commit();
                    header("Location: index.php?action=customer_debt&id=$payer_id&success=receipt_created");
                    exit;
                }

                $db->commit();
                // Nếu thu của NCC, Nhân viên hoặc Khác -> Về danh sách khách hàng (tạm thời)
                header("Location: index.php?action=customer_list&success=receipt_created_manual");
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi hệ thống khi tạo phiếu thu: " . $e->getMessage());
            }
        }
    }
    // 3. DANH SÁCH PHIẾU THU (Hỗ trợ Xóa hàng loạt)
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM receipts ORDER BY created_at DESC");
        $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/fund/receipt_list.php';
    }

    // 4. XEM CHI TIẾT PHIẾU THU
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("
            SELECT r.*, b.branch_name, ba.bank_name, ba.account_number 
            FROM receipts r 
            LEFT JOIN branches b ON r.branch_id = b.id 
            LEFT JOIN bank_accounts ba ON r.bank_account_id = ba.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$receipt) {
            die("Không tìm thấy dữ liệu phiếu thu!");
        }

        require_once __DIR__ . '/../views/fund/receipt_detail.php';
    }

    // 5. CẬP NHẬT PHIẾU THU (Quy tắc khóa cứng Hệ thống)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            $db = (new Database())->getConnection();

            // Chỉ cập nhật các trường được chỉnh sửa không giới hạn theo tài liệu (Diễn giải, tham chiếu)
            $stmt = $db->prepare("UPDATE receipts SET description = ?, reference_code = ? WHERE id = ?");
            $stmt->execute([$description, $reference_code, $id]);

            header("Location: index.php?action=receipt_detail&id=$id&success=updated");
            exit;
        }
    }

    // 6. XÓA PHIẾU THU (Hỗ trợ Đơn lẻ & Hàng loạt + Cơ chế hoàn tác cộng ngược lại nợ khách)
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = isset($data['ids']) ? $data['ids'] : [$data['id']];

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    // Lấy dữ liệu phiếu thu để kiểm tra xem có ảnh hưởng đến nợ của khách hàng không
                    $stmt_rc = $db->prepare("SELECT amount, payer_group, payer_id, receipt_code FROM receipts WHERE id = ?");
                    $stmt_rc->execute([$id]);
                    $receipt = $stmt_rc->fetch(PDO::FETCH_ASSOC);

                    if ($receipt) {
                        // Nếu thu từ Khách hàng -> Xóa phiếu thu nghĩa là chưa thu tiền -> Phải CỘNG NGƯỢC LẠI NỢ cho khách
                        if ($receipt['payer_group'] === 'customer' && !empty($receipt['payer_id'])) {
                            $customer_id = $receipt['payer_id'];

                            $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                            $stmt_cus->execute([$customer_id]);
                            $current_debt = floatval($stmt_cus->fetchColumn());

                            $new_debt = $current_debt + $receipt['amount']; // Cộng trả lại nợ cũ

                            $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                            $stmt_upd->execute([$new_debt, $customer_id]);

                            // Ghi nhận dòng hoàn tác vào lịch sử công nợ
                            $stmt_log = $db->prepare("
                                INSERT INTO customer_debt_history 
                                (customer_id, transaction_type, reference_code, debt_increase, balance, description) 
                                VALUES (?, 'adjustment', ?, ?, ?, ?)
                            ");
                            $stmt_log->execute([$customer_id, $receipt['receipt_code'], $receipt['amount'], $new_debt, "Hoàn tác tăng nợ do xóa phiếu thu " . $receipt['receipt_code']]);
                        }

                        // Thực hiện xóa chứng từ khỏi sổ quỹ
                        $stmt_del = $db->prepare("DELETE FROM receipts WHERE id = ?");
                        $stmt_del->execute([$id]);
                    }
                }

                $db->commit();
                echo json_encode(['status' => 'success', 'msg' => 'Đã xóa phiếu thu và hoàn tác công nợ đối tượng liên quan thành công!']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}
