<?php
// ÄÆ°á»ng dáº«n: app/controllers/FundReasonController.php
require_once __DIR__ . '/../../config/database.php';

class FundReasonController
{
    // 1. DANH SÃCH LÃ DO THU CHI
    public function index()
    {
        $db = (new Database())->getConnection();

        $stmt_receipts = $db->query("SELECT * FROM transaction_reasons WHERE type = 'receipt' ORDER BY is_system DESC, id ASC");
        $receipt_reasons = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

        $stmt_expenses = $db->query("SELECT * FROM transaction_reasons WHERE type = 'expense' ORDER BY is_system DESC, id ASC");
        $expense_reasons = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/reason_list.php';
    }

    // 2. THÃŠM Má»šI LÃ DO (Cho cáº£ Thu vÃ  Chi)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type']; // 'receipt' hoáº·c 'expense'
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

    // 3. XÃ“A LÃ DO (Chá»‰ xÃ³a tá»± táº¡o)
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $tab = $_GET['tab'] ?? 'receipt';
        $db = (new Database())->getConnection();

        // Chá»‰ cho phÃ©p xÃ³a khi is_system = 0
        $stmt = $db->prepare("DELETE FROM transaction_reasons WHERE id = ? AND is_system = 0");
        $stmt->execute([$id]);

        header("Location: index.php?action=fund_reasons&success_del=1&tab=$tab");
        exit;
    }
    // 4. API/Xá»¬ LÃ Cáº¬P NHáº¬T LÃ DO THU CHI (Luáº­t Há»‡ thá»‘ng: Sá»­a khÃ´ng giá»›i háº¡n tÃªn, cháº·n xÃ³a náº¿u lÃ  há»‡ thá»‘ng)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $reason_name = trim($_POST['reason_name']);
            $is_reported = isset($_POST['is_reported']) ? 1 : 0;

            $db = (new Database())->getConnection();

            // Kiá»ƒm tra xem lÃ½ do nÃ y cÃ³ pháº£i cá»§a há»‡ thá»‘ng (is_system = 1) hay khÃ´ng
            $stmt_check = $db->prepare("SELECT is_system, type FROM transaction_reasons WHERE id = ?");
            $stmt_check->execute([$id]);
            $reason = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$reason) {
                die("KhÃ´ng tÃ¬m tháº¥y lÃ½ do thu chi cáº§n sá»­a!");
            }

            if ($reason['is_system'] == 1) {
                // Náº¿u lÃ  lÃ½ do máº·c Ä‘á»‹nh há»‡ thá»‘ng: Chá»‰ cho phÃ©p sá»­a TÃªn ná»™i dung, khÃ´ng cho Ä‘á»•i tráº¡ng thÃ¡i bÃ¡o cÃ¡o
                $stmt_upd = $db->prepare("UPDATE transaction_reasons SET reason_name = ? WHERE id = ?");
                $stmt_upd->execute([$reason_name, $id]);
            } else {
                // Náº¿u lÃ  lÃ½ do tá»± táº¡o: Sá»­a toÃ n quyá»n bao gá»“m cáº£ nhÃ³m chi phÃ­
                $expense_category = ($reason['type'] === 'expense' && $is_reported == 1) ? $_POST['expense_category'] : null;
                $stmt_upd = $db->prepare("UPDATE transaction_reasons SET reason_name = ?, is_reported = ?, expense_category = ? WHERE id = ?");
                $stmt_upd->execute([$reason_name, $is_reported, $expense_category, $id]);
            }

            header("Location: index.php?action=fund_reasons&success_upd=1&tab=" . $reason['type']);
            exit;
        }
    }

    // 5. BÃO CÃO IN Sá»” QUá»¸ (THEO THÃ”NG TÆ¯ 88-2021/TT-BTC)
    public function print_cashbook()
    {
        $db = (new Database())->getConnection();
        $type = $_GET['type'] ?? 'cash'; // 'cash' (Tiá»n máº·t) hoáº·c 'bank' (Tiá»n gá»­i ngÃ¢n hÃ ng)

        // KÃ©o toÃ n bá»™ phiáº¿u thu
        $stmt_receipts = $db->prepare("SELECT receipt_code AS code, payer_name AS partner, amount, receipt_reason AS reason, created_at, 'receipt' AS f_type FROM receipts WHERE payment_method = ?");
        $stmt_receipts->execute([$type]);
        $receipts = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

        // KÃ©o toÃ n bá»™ phiáº¿u chi
        $stmt_expenses = $db->prepare("SELECT expense_code AS code, recipient_name AS partner, amount, expense_reason AS reason, created_at, 'expense' AS f_type FROM expenses WHERE payment_method = ?");
        $stmt_expenses->execute([$type]);
        $expenses = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

        // Gá»™p dÃ²ng tiá»n vÃ  sáº¯p xáº¿p theo trÃ¬nh tá»± thá»i gian tÄƒng dáº§n Ä‘á»ƒ káº¿t chuyá»ƒn sá»‘ dÆ°
        $cashbook_data = array_merge($receipts, $expenses);
        usort($cashbook_data, function ($a, $b) {
            return strtotime($a['created_at']) <=> strtotime($b['created_at']);
        });

        require_once __DIR__ . '/../views/fund/cashbook_print.php';
    }
}

