<?php
// Đường dẫn file: app/controllers/DebtManagementController.php
require_once __DIR__ . '/../../config/database.php';

class DebtManagementController
{
    // 1. DANH SÁCH CÔNG NỢ KHÁCH HÀNG (Mục 3 tài liệu)
    public function index()
    {
        $db = (new Database())->getConnection();

        // Truy vấn danh sách khách hàng để hiển thị dòng nợ Đầu kỳ, Phát sinh, Cuối kỳ
        // Để báo cáo chạy động mượt mà, "Nợ cuối kỳ" chính là cột debt hiện tại.
        $stmt = $db->query("SELECT id, customer_code, last_name, first_name, phone, debt FROM customers ORDER BY debt DESC");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/debt/list.php';
    }

    // 2. CHI TIẾT CÔNG NỢ & LỊCH SỬ GIAO DỊCH (Mục 1 tài liệu)
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();

        // Lấy thông tin khách hàng
        $stmt_cus = $db->prepare("SELECT id, customer_code, last_name, first_name, phone, debt FROM customers WHERE id = ?");
        $stmt_cus->execute([$id]);
        $customer = $stmt_cus->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            die("Không tìm thấy dữ liệu đối tác công nợ!");
        }

        // Lấy danh sách toàn bộ giao dịch phát sinh công nợ từ trước tới nay
        $stmt_hist = $db->prepare("SELECT * FROM customer_debt_history WHERE customer_id = ? ORDER BY created_at DESC, id DESC");
        $stmt_hist->execute([$id]);
        $debt_history = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/debt/detail.php';
    }

    // 3. XỬ LÝ ĐIỀU CHỈNH SỐ NỢ MỤC TIÊU (CORE LOGIC SAPO - MỤC 2 TÀI LIỆU)
    public function adjust()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $target_debt = floatval($_POST['target_debt']); // Trường "Nợ sau khi chỉnh sửa"
            $description = trim($_POST['description']);

            $db = (new Database())->getConnection();

            // Lấy số nợ hiện tại trước khi sửa
            $stmt_old = $db->prepare("SELECT debt FROM customers WHERE id = ?");
            $stmt_old->execute([$customer_id]);
            $current_debt = floatval($stmt_old->fetchColumn());

            // Thuật toán tự tính toán giá trị thay đổi công nợ (Delta)
            $delta = $target_debt - $current_debt;

            if ($delta == 0) {
                header("Location: index.php?action=debt_app_detail&id=" . $customer_id . "&msg=no_change");
                exit;
            }

            $debt_increase = 0;
            $debt_decrease = 0;
            $type_label = "";

            if ($delta > 0) {
                // Nợ tăng lên
                $debt_increase = $delta;
                $type_label = "Điều chỉnh tăng";
            } else {
                // Nợ giảm đi
                $debt_decrease = abs($delta);
                $type_label = "Điều chỉnh giảm";
            }

            try {
                $db->beginTransaction();

                // Bước A: Cập nhật số nợ mới chốt vào bảng customers
                $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                $stmt_upd->execute([$target_debt, $customer_id]);

                // Bước B: Ghi nhận chứng từ điều chỉnh (Mã DCCN) vào lịch sử chi tiết
                $ref_code = 'DCCN' . date('YmdHis');
                $stmt_log = $db->prepare("
                    INSERT INTO customer_debt_history 
                    (customer_id, transaction_type, reference_code, debt_increase, debt_decrease, balance, description) 
                    VALUES (?, 'adjustment', ?, ?, ?, ?, ?)
                ");
                $stmt_log->execute([$customer_id, $ref_code, $debt_increase, $debt_decrease, $target_debt, $description]);

                $db->commit();

                header("Location: index.php?action=debt_app_detail&id=" . $customer_id . "&success=adjusted");
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lỗi xử lý cân bằng số dư công nợ: " . $e->getMessage());
            }
        }
    }
}
