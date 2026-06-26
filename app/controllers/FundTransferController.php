<?php
// ÄÆ°á»ng dáº«n: app/controllers/FundTransferController.php
require_once __DIR__ . '/../../config/database.php';

class FundTransferController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 1. TRANG DANH SÃCH PHIáº¾U CHUYá»‚N QUá»¸
    public function index()
    {
        // Truy váº¥n táº¥t cáº£ phiáº¿u chuyá»ƒn quá»¹
        $stmt = $this->db->query("SELECT * FROM fund_transfers ORDER BY id DESC");
        $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_list.php';
    }

    // Giao diá»‡n Táº¡o Phiáº¿u chuyá»ƒn quá»¹ ná»™i bá»™ (Giá»¯ nguyÃªn bÃ i cÅ©)
    public function create()
    {
        try {
            $stmt_br = $this->db->query("SELECT id, branch_name FROM branches");
            $branches = $stmt_br->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhÃ¡nh máº·c Ä‘á»‹nh'], ['id' => 2, 'branch_name' => 'Chi nhÃ¡nh miá»n Nam']];
        }

        $stmt_bank = $this->db->query("SELECT id, bank_name, account_number, account_name FROM bank_accounts WHERE status = 'active'");
        $bank_accounts = $stmt_bank->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_create.php';
    }

    // Xá»­ lÃ½ lÆ°u Phiáº¿u chuyá»ƒn quá»¹ (Giá»¯ nguyÃªn bÃ i cÅ©)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from_type = $_POST['from_type'];
            $from_id = ($from_type === 'cash') ? $_POST['from_branch_id'] : $_POST['from_bank_id'];

            $to_type = $_POST['to_type'];
            // Náº¿u ngÆ°á»i dÃ¹ng chá»n trá»‘ng á»Ÿ bÆ°á»›c táº¡o (cho phÃ©p bá»• sung sau)
            $to_id = 0;
            if ($to_type === 'cash' && isset($_POST['to_branch_id'])) $to_id = $_POST['to_branch_id'];
            if ($to_type === 'bank' && isset($_POST['to_bank_id'])) $to_id = $_POST['to_bank_id'];

            $amount = floatval($_POST['amount']);
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            // Xá»­ lÃ½ ngÃ y nháº­n tiá»n (náº¿u trá»‘ng thÃ¬ cho phÃ©p cáº­p nháº­t sau)
            $transaction_date = !empty($_POST['transaction_date']) ? $_POST['transaction_date'] : null;

            $transfer_code = trim($_POST['transfer_code']);
            if (empty($transfer_code)) {
                $transfer_code = 'PCQ' . date('Ymd') . rand(100, 999);
            }

            $stmt = $this->db->prepare("INSERT INTO fund_transfers (transfer_code, from_type, from_id, to_type, to_id, amount, description, transaction_date, reference_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$transfer_code, $from_type, $from_id, $to_type, $to_id, $amount, $description, $transaction_date, $reference_code]);

            header("Location: index.php?action=fund_transfers&success=created");
            exit;
        }
    }

    // 2. TRANG XEM CHI TIáº¾T & CHá»ˆNH Sá»¬A PHIáº¾U CHUYá»‚N QUá»¸ (Má»¥c 2 & 3 tÃ i liá»‡u Há»‡ thá»‘ng)
    public function detail()
    {
        $id = $_GET['id'] ?? 0;

        // Láº¥y thÃ´ng tin chi tiáº¿t phiáº¿u chi chuyá»ƒn quá»¹
        $stmt = $this->db->prepare("SELECT * FROM fund_transfers WHERE id = ?");
        $stmt->execute([$id]);
        $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transfer) {
            die("KhÃ´ng tÃ¬m tháº¥y phiáº¿u chuyá»ƒn quá»¹ há»£p lá»‡!");
        }

        // Láº¥y máº£ng Chi nhÃ¡nh vÃ  NgÃ¢n hÃ ng Ä‘á»™ng Ä‘á»ƒ phá»¥c vá»¥ cho Ã´ Select sá»­a Ä‘á»•i
        try {
            $branches = $this->db->query("SELECT id, branch_name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhÃ¡nh máº·c Ä‘á»‹nh'], ['id' => 2, 'branch_name' => 'Chi nhÃ¡nh miá»n Nam']];
        }
        $bank_accounts = $this->db->query("SELECT id, bank_name, account_number FROM bank_accounts")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_detail.php';
    }

    // 3. Xá»¬ LÃ Cáº¬P NHáº¬T PHIáº¾U CHUYá»‚N QUá»¸ (ÃP Dá»¤NG ÄÃšNG RÃ€NG BUá»˜C KHÃ“A Cá»¦A Há»‡ thá»‘ng)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            // Äá»c láº¡i dá»¯ liá»‡u cÅ© tá»« DB trÆ°á»›c Ä‘á»ƒ kiá»ƒm tra Ä‘iá»u kiá»‡n cháº·n sá»­a (Má»¥c 2)
            $stmt_check = $this->db->prepare("SELECT to_id, transaction_date FROM fund_transfers WHERE id = ?");
            $stmt_check->execute([$id]);
            $old = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$old) {
                die("YÃªu cáº§u khÃ´ng há»£p lá»‡.");
            }

            // Quy táº¯c Há»‡ thá»‘ng: Náº¿u to_id cÅ© báº±ng 0 (chÆ°a nháº­p) -> Cho phÃ©p cáº­p nháº­t. NgÆ°á»£c láº¡i -> Giá»¯ nguyÃªn dá»¯ liá»‡u cÅ©.
            $to_id = $old['to_id'];
            if ($old['to_id'] == 0 && isset($_POST['to_id_update'])) {
                $to_id = $_POST['to_id_update'];
            }

            // Quy táº¯c Há»‡ thá»‘ng: Náº¿u ngÃ y cÅ© Ä‘ang NULL -> Cho phÃ©p Ä‘iá»n ngÃ y vÃ o sá»•. NgÆ°á»£c láº¡i -> Giá»¯ nguyÃªn.
            $transaction_date = $old['transaction_date'];
            if (empty($old['transaction_date']) && !empty($_POST['transaction_date_update'])) {
                $transaction_date = $_POST['transaction_date_update'];
            }

            // Tiáº¿n hÃ nh cáº­p nháº­t an toÃ n
            $stmt_up = $this->db->prepare("UPDATE fund_transfers SET description = ?, reference_code = ?, to_id = ?, transaction_date = ? WHERE id = ?");
            $stmt_up->execute([$description, $reference_code, $to_id, $transaction_date, $id]);

            header("Location: index.php?action=fund_transfer_detail&id=" . $id . "&success=updated");
            exit;
        }
    }
    // API: XÃ“A PHIáº¾U CHUYá»‚N QUá»¸ ÄÆ N Láºº (Má»¥c 1 tÃ i liá»‡u Há»‡ thá»‘ng)
    public function delete_single()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                // Thá»±c hiá»‡n xÃ³a chá»©ng tá»« trong DB
                $stmt = $this->db->prepare("DELETE FROM fund_transfers WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'msg' => 'ÄÃ£ xÃ³a vÄ©nh viá»…n phiáº¿u chuyá»ƒn quá»¹ ná»™i bá»™ Ä‘Æ¡n láº» thÃ nh cÃ´ng!']);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'ID phiáº¿u khÃ´ng há»£p lá»‡.']);
            exit;
        }
    }

    // API: XÃ“A PHIáº¾U CHUYá»‚N QUá»¸ HÃ€NG LOáº T (Má»¥c 2 tÃ i liá»‡u Há»‡ thá»‘ng)
    public function delete_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = $data['ids'] ?? [];

            if (!empty($ids) && is_array($ids)) {
                // Biáº¿n máº£ng ID thÃ nh chuá»—i cháº¥m há»i ?,?,? Ä‘á»ƒ trÃ¡nh lá»—i SQL Injection
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $stmt = $this->db->prepare("DELETE FROM fund_transfers WHERE id IN ($placeholders)");
                $stmt->execute($ids);

                echo json_encode([
                    'status' => 'success',
                    'msg' => 'ÄÃ£ xÃ³a hÃ ng loáº¡t ' . count($ids) . ' phiáº¿u chuyá»ƒn quá»¹ ná»™i bá»™ thÃ nh cÃ´ng!'
                ]);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'Vui lÃ²ng chá»n Ã­t nháº¥t 1 phiáº¿u Ä‘á»ƒ xÃ³a.']);
            exit;
        }
    }
}

