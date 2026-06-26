<?php
// ÄÆ°á»ng dáº«n: app/controllers/ExpenseController.php
require_once __DIR__ . '/../../config/database.php';

class ExpenseController
{
    // 1. GIAO DIá»†N Táº O PHIáº¾U CHI THá»¦ CÃ”NG MULTI-OBJECT
    public function create()
    {
        $db = (new Database())->getConnection();

        // KÃ©o dá»¯ liá»‡u phá»¥c vá»¥ cÃ¡c Dropdown Ä‘á»‘i tÆ°á»£ng nháº­n
        $customers = $db->query("SELECT id, last_name, first_name, phone FROM customers")->fetchAll(PDO::FETCH_ASSOC);

        // ThÃªm khá»‘i try-catch Ä‘á» phÃ²ng KhÆ°Æ¡ng chÆ°a lÃ m báº£ng NhÃ  cung cáº¥p hoáº·c NhÃ¢n viÃªn
        try {
            $suppliers = $db->query("SELECT id, supplier_name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $suppliers = [['id' => 1, 'supplier_name' => 'NhÃ  cung cáº¥p Apple VN']];
        }

        try {
            $employees = $db->query("SELECT id, full_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $employees = [['id' => 1, 'full_name' => 'Nguyá»…n VÄƒn NhÃ¢n ViÃªn']];
        }

        // KÃ©o danh sÃ¡ch chi nhÃ¡nh vÃ  tÃ i khoáº£n ngÃ¢n hÃ ng nháº­n tiá»n
        try {
            $branches = $db->query("SELECT id, branch_name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhÃ¡nh Trung tÃ¢m']];
        }

        try {
            $bank_accounts = $db->query("SELECT id, bank_name, account_number FROM bank_accounts")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bank_accounts = [['id' => 1, 'bank_name' => 'Techcombank', 'account_number' => '1903123']];
        }

        require_once __DIR__ . '/../views/fund/expense_create.php';
    }

    // 2. THUáº¬T TOÃN LÆ¯U PHIáº¾U CHI VÃ€ Tá»° Äá»˜NG PHÃ‚N LUá»’NG Háº CH TOÃN
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
            $expense_category = !empty($_POST['expense_category']) ? $_POST['expense_category'] : 'Chi phÃ­ váº­n hÃ nh';
            $description = trim($_POST['description']);

            $branch_id = intval($_POST['branch_id']);
            $transaction_date = $_POST['transaction_date'];

            // Xá»­ lÃ½ CÆ¡ cháº¿ Tá»± sinh mÃ£ phiáº¿u chi chuáº©n Há»‡ thá»‘ng (Náº¿u Ä‘á»ƒ trá»‘ng)
            $expense_code = trim($_POST['expense_code']);
            if (empty($expense_code)) {
                $expense_code = 'PC' . date('YmdHis') . rand(10, 99);
            }

            $is_debt_affected = isset($_POST['is_debt_affected']) ? 1 : 0;
            $reference_code = trim($_POST['reference_code'] ?? '');

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                // BÆ°á»›c 1: LÆ°u phiáº¿u chi thá»§ cÃ´ng vÃ o báº£ng expenses
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

                // BÆ°á»›c 2: Kiá»ƒm tra nghiá»‡p vá»¥ tÃ­ch chá»n cáº­p nháº­t cÃ´ng ná»£ (Chá»‰ Ã¡p dá»¥ng khi chi cho KhÃ¡ch hÃ ng hoáº·c NhÃ  cung cáº¥p)
                if ($is_debt_affected === 1 && $recipient_group === 'customer' && !empty($recipient_id)) {
                    // TÄƒng cÃ´ng ná»£ khÃ¡ch hÃ ng (KhÃ¡ch ná»£ thÃªm cá»­a hÃ ng)
                    $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                    $stmt_cus->execute([$recipient_id]);
                    $current_debt = floatval($stmt_cus->fetchColumn());

                    $new_debt = $current_debt + $amount;

                    $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                    $stmt_upd->execute([$new_debt, $recipient_id]);

                    // Ghi Sá»• chi tiáº¿t cÃ´ng ná»£
                    $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_increase, balance, description) VALUES (?, 'payment', ?, ?, ?, ?)");
                    $stmt_log->execute([$recipient_id, $expense_code, $amount, $new_debt, "Háº¡ch toÃ¡n tÄƒng ná»£ tá»« phiáº¿u chi thá»§ cÃ´ng $expense_code"]);
                }

                $db->commit();

                // Tráº£ vá» trang danh sÃ¡ch (Táº¡m chuyá»ƒn vá» danh sÃ¡ch khÃ¡ch hÃ ng hoáº·c mÃ n hÃ¬nh cÃ´ng ná»£ tÃ¹y tÃ­ch chá»n)
                if ($is_debt_affected === 1 && $recipient_group === 'customer') {
                    header("Location: index.php?action=customer_debt&id=$recipient_id&success=expense_created");
                } else {
                    header("Location: index.php?action=customer_list&success=expense_created_manual");
                }
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lá»—i há»‡ thá»‘ng khi táº¡o phiáº¿u chi thá»§ cÃ´ng: " . $e->getMessage());
            }
        }
    }
    // 3. DANH SÃCH PHIáº¾U CHI (Há»— trá»£ XÃ³a hÃ ng loáº¡t)
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM expenses ORDER BY created_at DESC");
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/fund/expense_list.php';
    }

    // 4. XEM CHI TIáº¾T & IN PHIáº¾U CHI
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

        if (!$expense) die("KhÃ´ng tÃ¬m tháº¥y phiáº¿u chi!");

        require_once __DIR__ . '/../views/fund/expense_detail.php';
    }

    // 5. Cáº¬P NHáº¬T PHIáº¾U CHI (KhÃ³a cá»©ng theo luáº­t Há»‡ thá»‘ng)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);
            // CÃ¡c trÆ°á»ng khÃ¡c nhÆ° Sá»‘ tiá»n, Äá»‘i tÆ°á»£ng, Chi nhÃ¡nh khÃ´ng Ä‘Æ°á»£c phÃ©p láº¥y tá»« POST Ä‘á»ƒ chá»‘ng hack

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE expenses SET description = ?, reference_code = ? WHERE id = ?");
            $stmt->execute([$description, $reference_code, $id]);

            header("Location: index.php?action=expense_detail&id=$id&success=updated");
            exit;
        }
    }

    // 6. XÃ“A PHIáº¾U CHI (ÄÆ¡n láº» & HÃ ng loáº¡t kÃ¨m HoÃ n tÃ¡c CÃ´ng ná»£)
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = isset($data['ids']) ? $data['ids'] : [$data['id']];

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    // 1. Láº¥y thÃ´ng tin phiáº¿u chi trÆ°á»›c khi xÃ³a
                    $stmt_ex = $db->prepare("SELECT amount, is_debt_affected, recipient_group, recipient_id, expense_code FROM expenses WHERE id = ?");
                    $stmt_ex->execute([$id]);
                    $expense = $stmt_ex->fetch(PDO::FETCH_ASSOC);

                    if ($expense) {
                        // 2. Náº¿u phiáº¿u chi nÃ y tá»«ng lÃ m TÄ‚NG cÃ´ng ná»£ khÃ¡ch -> XÃ³a phiáº¿u thÃ¬ pháº£i GIáº¢M cÃ´ng ná»£ tráº£ láº¡i
                        if ($expense['is_debt_affected'] == 1 && $expense['recipient_group'] == 'customer' && $expense['recipient_id']) {
                            $customer_id = $expense['recipient_id'];

                            $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                            $stmt_cus->execute([$customer_id]);
                            $current_debt = floatval($stmt_cus->fetchColumn());

                            $new_debt = $current_debt - $expense['amount']; // Trá»« ngÆ°á»£c láº¡i

                            $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                            $stmt_upd->execute([$new_debt, $customer_id]);

                            // Ghi log hoÃ n tÃ¡c
                            $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_decrease, balance, description) VALUES (?, 'adjustment', ?, ?, ?, ?)");
                            $stmt_log->execute([$customer_id, $expense['expense_code'], $expense['amount'], $new_debt, "HoÃ n tÃ¡c xÃ³a phiáº¿u chi " . $expense['expense_code']]);
                        }

                        // 3. XÃ³a phiáº¿u chi
                        $stmt_del = $db->prepare("DELETE FROM expenses WHERE id = ?");
                        $stmt_del->execute([$id]);
                    }
                }

                $db->commit();
                echo json_encode(['status' => 'success', 'msg' => 'ÄÃ£ xÃ³a chá»©ng tá»« vÃ  hoÃ n tÃ¡c cÃ´ng ná»£ (náº¿u cÃ³) thÃ nh cÃ´ng!']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'msg' => 'Lá»—i há»‡ thá»‘ng: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}

