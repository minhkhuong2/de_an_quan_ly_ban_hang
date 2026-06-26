<?php
// ÄÆ°á»ng dáº«n: app/controllers/ReceiptController.php
require_once __DIR__ . '/../../config/database.php';

class ReceiptController
{
    // 1. GIAO DIá»†N Táº O PHIáº¾U THU THá»¦ CÃ”NG ÄA Äá»I TÆ¯á»¢NG
    public function create()
    {
        $db = (new Database())->getConnection();

        // KÃ©o dá»¯ liá»‡u phá»¥c vá»¥ Dropdown (KhÃ¡ch hÃ ng Æ°u tiÃªn ngÆ°á»i Ä‘ang CÃ“ Ná»¢)
        $customers = $db->query("SELECT id, last_name, first_name, phone, debt FROM customers")->fetchAll(PDO::FETCH_ASSOC);

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

        require_once __DIR__ . '/../views/fund/receipt_create.php';
    }

    // 2. Xá»¬ LÃ LÆ¯U PHIáº¾U THU & KÃCH HOáº T THUáº¬T TOÃN FIFO (Náº¾U LÃ€ KHÃCH HÃ€NG)
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

                // BÆ°á»›c 1: LÆ°u Phiáº¿u Thu vÃ o báº£ng receipts
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

                // BÆ°á»›c 2: Náº¾U Äá»I TÆ¯á»¢NG LÃ€ KHÃCH HÃ€NG -> KÃCH HOáº T Ráº¢I TIá»€N FIFO TRá»ª Ná»¢
                if ($payer_group === 'customer' && !empty($payer_id)) {
                    $stmt_check = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                    $stmt_check->execute([$payer_id]);
                    $current_debt = floatval($stmt_check->fetchColumn());

                    // Thuáº­t toÃ¡n ráº£i tiá»n vÃ o cÃ¡c Ä‘Æ¡n hÃ ng Ä‘ang ná»£ (FIFO)
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

                    // Giáº£m tá»•ng cÃ´ng ná»£ khÃ¡ch hÃ ng & Ghi Sá»• chi tiáº¿t
                    $new_debt = $current_debt - $amount;
                    $stmt_upd_cus = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                    $stmt_upd_cus->execute([$new_debt, $payer_id]);

                    $stmt_log = $db->prepare("INSERT INTO customer_debt_history (customer_id, transaction_type, reference_code, debt_decrease, balance, description) VALUES (?, 'receipt', ?, ?, ?, ?)");
                    $stmt_log->execute([$payer_id, $receipt_code, $amount, $new_debt, "KhÃ¡ch thanh toÃ¡n cÃ´ng ná»£ theo phiáº¿u thu $receipt_code"]);

                    $db->commit();
                    header("Location: index.php?action=customer_debt&id=$payer_id&success=receipt_created");
                    exit;
                }

                $db->commit();
                // Náº¿u thu cá»§a NCC, NhÃ¢n viÃªn hoáº·c KhÃ¡c -> Vá» danh sÃ¡ch khÃ¡ch hÃ ng (táº¡m thá»i)
                header("Location: index.php?action=customer_list&success=receipt_created_manual");
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                die("Lá»—i há»‡ thá»‘ng khi táº¡o phiáº¿u thu: " . $e->getMessage());
            }
        }
    }
    // 3. DANH SÃCH PHIáº¾U THU (Há»— trá»£ XÃ³a hÃ ng loáº¡t)
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM receipts ORDER BY created_at DESC");
        $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/fund/receipt_list.php';
    }

    // 4. XEM CHI TIáº¾T PHIáº¾U THU
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
            die("KhÃ´ng tÃ¬m tháº¥y dá»¯ liá»‡u phiáº¿u thu!");
        }

        require_once __DIR__ . '/../views/fund/receipt_detail.php';
    }

    // 5. Cáº¬P NHáº¬T PHIáº¾U THU (Quy táº¯c khÃ³a cá»©ng Há»‡ thá»‘ng)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            $db = (new Database())->getConnection();

            // Chá»‰ cáº­p nháº­t cÃ¡c trÆ°á»ng Ä‘Æ°á»£c chá»‰nh sá»­a khÃ´ng giá»›i háº¡n theo tÃ i liá»‡u (Diá»…n giáº£i, tham chiáº¿u)
            $stmt = $db->prepare("UPDATE receipts SET description = ?, reference_code = ? WHERE id = ?");
            $stmt->execute([$description, $reference_code, $id]);

            header("Location: index.php?action=receipt_detail&id=$id&success=updated");
            exit;
        }
    }

    // 6. XÃ“A PHIáº¾U THU (Há»— trá»£ ÄÆ¡n láº» & HÃ ng loáº¡t + CÆ¡ cháº¿ hoÃ n tÃ¡c cá»™ng ngÆ°á»£c láº¡i ná»£ khÃ¡ch)
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = isset($data['ids']) ? $data['ids'] : [$data['id']];

            $db = (new Database())->getConnection();

            try {
                $db->beginTransaction();

                foreach ($ids as $id) {
                    // Láº¥y dá»¯ liá»‡u phiáº¿u thu Ä‘á»ƒ kiá»ƒm tra xem cÃ³ áº£nh hÆ°á»Ÿng Ä‘áº¿n ná»£ cá»§a khÃ¡ch hÃ ng khÃ´ng
                    $stmt_rc = $db->prepare("SELECT amount, payer_group, payer_id, receipt_code FROM receipts WHERE id = ?");
                    $stmt_rc->execute([$id]);
                    $receipt = $stmt_rc->fetch(PDO::FETCH_ASSOC);

                    if ($receipt) {
                        // Náº¿u thu tá»« KhÃ¡ch hÃ ng -> XÃ³a phiáº¿u thu nghÄ©a lÃ  chÆ°a thu tiá»n -> Pháº£i Cá»˜NG NGÆ¯á»¢C Láº I Ná»¢ cho khÃ¡ch
                        if ($receipt['payer_group'] === 'customer' && !empty($receipt['payer_id'])) {
                            $customer_id = $receipt['payer_id'];

                            $stmt_cus = $db->prepare("SELECT debt FROM customers WHERE id = ?");
                            $stmt_cus->execute([$customer_id]);
                            $current_debt = floatval($stmt_cus->fetchColumn());

                            $new_debt = $current_debt + $receipt['amount']; // Cá»™ng tráº£ láº¡i ná»£ cÅ©

                            $stmt_upd = $db->prepare("UPDATE customers SET debt = ? WHERE id = ?");
                            $stmt_upd->execute([$new_debt, $customer_id]);

                            // Ghi nháº­n dÃ²ng hoÃ n tÃ¡c vÃ o lá»‹ch sá»­ cÃ´ng ná»£
                            $stmt_log = $db->prepare("
                                INSERT INTO customer_debt_history 
                                (customer_id, transaction_type, reference_code, debt_increase, balance, description) 
                                VALUES (?, 'adjustment', ?, ?, ?, ?)
                            ");
                            $stmt_log->execute([$customer_id, $receipt['receipt_code'], $receipt['amount'], $new_debt, "HoÃ n tÃ¡c tÄƒng ná»£ do xÃ³a phiáº¿u thu " . $receipt['receipt_code']]);
                        }

                        // Thá»±c hiá»‡n xÃ³a chá»©ng tá»« khá»i sá»• quá»¹
                        $stmt_del = $db->prepare("DELETE FROM receipts WHERE id = ?");
                        $stmt_del->execute([$id]);
                    }
                }

                $db->commit();
                echo json_encode(['status' => 'success', 'msg' => 'ÄÃ£ xÃ³a phiáº¿u thu vÃ  hoÃ n tÃ¡c cÃ´ng ná»£ Ä‘á»‘i tÆ°á»£ng liÃªn quan thÃ nh cÃ´ng!']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'msg' => 'Lá»—i há»‡ thá»‘ng: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}

