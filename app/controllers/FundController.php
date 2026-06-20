<?php
// Đường dẫn: app/controllers/FundController.php
require_once __DIR__ . '/../../config/database.php';

class FundController
{
    // 1. TỔNG QUAN SỔ QUỸ (DASHBOARD)
    public function dashboard()
    {
        $db = (new Database())->getConnection();

        // Lấy bộ lọc thời gian và loại quỹ từ URL (Mặc định là tháng hiện tại)
        $start_date = $_GET['start_date'] ?? date('Y-m-01 00:00:00');
        $end_date = $_GET['end_date'] ?? date('Y-m-t 23:59:59');
        $fund_type = $_GET['fund_type'] ?? 'all'; // all, cash, bank

        $fund_condition = "";
        if ($fund_type !== 'all') {
            $fund_condition = " AND payment_method = '$fund_type'";
        }

        // --- TÍNH SỐ DƯ ĐẦU KỲ (Trước start_date) ---
        $stmt_open_receipts = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM receipts WHERE transaction_date < ? $fund_condition");
        $stmt_open_receipts->execute([$start_date]);
        $open_in = $stmt_open_receipts->fetchColumn();

        $stmt_open_expenses = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE transaction_date < ? $fund_condition");
        $stmt_open_expenses->execute([$start_date]);
        $open_out = $stmt_open_expenses->fetchColumn();

        $opening_balance = $open_in - $open_out;

        // --- LẤY DỮ LIỆU PHÁT SINH TRONG KỲ (Gộp Thu và Chi bằng UNION ALL) ---
        $query = "
            SELECT 'receipt' AS doc_type, receipt_code AS doc_code, transaction_date, payer_name AS partner, receipt_reason AS reason, amount AS total_in, 0 AS total_out, payment_method 
            FROM receipts 
            WHERE transaction_date >= ? AND transaction_date <= ? $fund_condition
            UNION ALL
            SELECT 'expense' AS doc_type, expense_code AS doc_code, transaction_date, recipient_name AS partner, expense_reason AS reason, 0 AS total_in, amount AS total_out, payment_method 
            FROM expenses 
            WHERE transaction_date >= ? AND transaction_date <= ? $fund_condition
            ORDER BY transaction_date DESC
        ";
        $stmt_transactions = $db->prepare($query);
        $stmt_transactions->execute([$start_date, $end_date, $start_date, $end_date]);
        $transactions = $stmt_transactions->fetchAll(PDO::FETCH_ASSOC);

        // --- TÍNH TỔNG THU, TỔNG CHI TRONG KỲ ---
        $total_in = 0;
        $total_out = 0;
        foreach ($transactions as $t) {
            $total_in += $t['total_in'];
            $total_out += $t['total_out'];
        }

        // --- TỒN QUỸ CUỐI KỲ ---
        $closing_balance = $opening_balance + $total_in - $total_out;

        require_once __DIR__ . '/../views/fund/dashboard.php';
    }

    // 2. XUẤT FILE SỔ QUỸ (CSV EXCEL)
    public function export()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $export_type = $_POST['export_type']; // 'cash', 'bank', 'all'

            // Set header để trình duyệt hiểu đây là file download Excel/CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="So_Quy_' . date('Ymd_His') . '.csv"');

            $output = fopen('php://output', 'w');
            // Ghi BOM để Excel đọc tiếng Việt UTF-8 không bị lỗi font
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Ghi tiêu đề cột
            fputcsv($output, ['Ngày ghi sổ', 'Mã chứng từ', 'Loại chứng từ', 'Đối tượng', 'Lý do', 'Thu vào (+)', 'Chi ra (-)', 'Hình thức']);

            $db = (new Database())->getConnection();
            $fund_cond = ($export_type !== 'all') ? " WHERE payment_method = '$export_type'" : "";

            // Kéo toàn bộ data không giới hạn thời gian để xuất file
            $query = "
                SELECT transaction_date, receipt_code AS doc_code, 'Phiếu thu' AS doc_type, payer_name AS partner, receipt_reason AS reason, amount AS total_in, 0 AS total_out, payment_method 
                FROM receipts $fund_cond
                UNION ALL
                SELECT transaction_date, expense_code AS doc_code, 'Phiếu chi' AS doc_type, recipient_name AS partner, expense_reason AS reason, 0 AS total_in, amount AS total_out, payment_method 
                FROM expenses $fund_cond
                ORDER BY transaction_date ASC
            ";
            $stmt = $db->query($query);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $method = ($row['payment_method'] == 'cash') ? 'Tiền mặt' : 'Chuyển khoản';
                fputcsv($output, [
                    date('d/m/Y H:i', strtotime($row['transaction_date'])),
                    $row['doc_code'],
                    $row['doc_type'],
                    $row['partner'],
                    $row['reason'],
                    $row['total_in'],
                    $row['total_out'],
                    $method
                ]);
            }
            fclose($output);
            exit;
        }
    }
}
