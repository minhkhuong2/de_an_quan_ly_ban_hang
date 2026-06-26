<?php
// Đường dẫn: app/controllers/FundReasonController.php
require_once __DIR__ . '/../../config/database.php';

class FundReasonController
{
    // 1. DANH SÁCH LÝ DO THU CHI
    public function index()
    {
        $db = (new Database())->getConnection();

        $stmt_receipts = $db->query("SELECT * FROM transaction_reasons WHERE type = 'receipt' ORDER BY is_system DESC, id ASC");
        $receipt_reasons = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

        $stmt_expenses = $db->query("SELECT * FROM transaction_reasons WHERE type = 'expense' ORDER BY is_system DESC, id ASC");
        $expense_reasons = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/reason_list.php';
    }

    // 2. THÊM MỚI LÝ DO (Cho cả Thu và Chi)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type']; // 'receipt' hoặc 'expense'
            $reason_name = trim($_POST['reason_name']);
            $is_reported = isset($_POST['is_reported']) ? 1 : 0;
            $expense_category = ($type === 'expense' && $is_reported == 1) ? $_POST['expense_category'] : null;

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO transaction_reasons (type, reason_name, is_system, apply_to, is_reported, expense_category) VALUES (?, ?, 0, 'all', ?, ?)");
            $stmt->execute([$type, $reason_name, $is_reported, $expense_category]);

            header("Location: index.php?action=fund_reasons&success=1&tab=$type");
            exit;
        }
    }

    // 3. XÓA LÝ DO (Chỉ xóa tự tạo)
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $tab = $_GET['tab'] ?? 'receipt';
        $db = (new Database())->getConnection();

        // Chỉ cho phép xóa khi is_system = 0
        $stmt = $db->prepare("DELETE FROM transaction_reasons WHERE id = ? AND is_system = 0");
        $stmt->execute([$id]);

        header("Location: index.php?action=fund_reasons&success_del=1&tab=$tab");
        exit;
    }
    // 4. API/XỬ LÝ CẬP NHẬT LÝ DO THU CHI (Luật Sapo: Sửa không giới hạn tên, chặn xóa nếu là hệ thống)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $reason_name = trim($_POST['reason_name']);
            $is_reported = isset($_POST['is_reported']) ? 1 : 0;

            $db = (new Database())->getConnection();

            // Kiểm tra xem lý do này có phải của hệ thống (is_system = 1) hay không
            $stmt_check = $db->prepare("SELECT is_system, type FROM transaction_reasons WHERE id = ?");
            $stmt_check->execute([$id]);
            $reason = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$reason) {
                die("Không tìm thấy lý do thu chi cần sửa!");
            }

            if ($reason['is_system'] == 1) {
                // Nếu là lý do mặc định hệ thống: Chỉ cho phép sửa Tên nội dung, không cho đổi trạng thái báo cáo
                $stmt_upd = $db->prepare("UPDATE transaction_reasons SET reason_name = ? WHERE id = ?");
                $stmt_upd->execute([$reason_name, $id]);
            } else {
                // Nếu là lý do tự tạo: Sửa toàn quyền bao gồm cả nhóm chi phí
                $expense_category = ($reason['type'] === 'expense' && $is_reported == 1) ? $_POST['expense_category'] : null;
                $stmt_upd = $db->prepare("UPDATE transaction_reasons SET reason_name = ?, is_reported = ?, expense_category = ? WHERE id = ?");
                $stmt_upd->execute([$reason_name, $is_reported, $expense_category, $id]);
            }

            header("Location: index.php?action=fund_reasons&success_upd=1&tab=" . $reason['type']);
            exit;
        }
    }

    // 5. BÁO CÁO IN SỔ QUỸ (THEO THÔNG TƯ 88-2021/TT-BTC)
    public function print_cashbook()
    {
        $db = (new Database())->getConnection();
        $type = $_GET['type'] ?? 'cash'; // 'cash' (Tiền mặt) hoặc 'bank' (Tiền gửi ngân hàng)

        // Kéo toàn bộ phiếu thu
        $stmt_receipts = $db->prepare("SELECT receipt_code AS code, payer_name AS partner, amount, receipt_reason AS reason, created_at, 'receipt' AS f_type FROM receipts WHERE payment_method = ?");
        $stmt_receipts->execute([$type]);
        $receipts = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

        // Kéo toàn bộ phiếu chi
        $stmt_expenses = $db->prepare("SELECT expense_code AS code, recipient_name AS partner, amount, expense_reason AS reason, created_at, 'expense' AS f_type FROM expenses WHERE payment_method = ?");
        $stmt_expenses->execute([$type]);
        $expenses = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

        // Gộp dòng tiền và sắp xếp theo trình tự thời gian tăng dần để kết chuyển số dư
        $cashbook_data = array_merge($receipts, $expenses);
        usort($cashbook_data, function ($a, $b) {
            return strtotime($a['created_at']) <=> strtotime($b['created_at']);
        });

        require_once __DIR__ . '/../views/fund/cashbook_print.php';
    }
}
