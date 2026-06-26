<?php
// ÄÆ°á»ng dáº«n: app/controllers/BranchController.php
require_once __DIR__ . '/../../config/database.php';

class BranchController
{
    // 1. GIAO DIá»†N DANH SÃCH CHI NHÃNH
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM branches ORDER BY routing_priority ASC, is_default DESC, created_at ASC");
        $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Láº¥y danh sÃ¡ch chi nhÃ¡nh Ä‘ang hoáº¡t Ä‘á»™ng Ä‘á»ƒ lÃ m dropdown Chuyá»ƒn giao dá»¯ liá»‡u
        $active_branches = array_filter($branches, function ($b) {
            return $b['status'] === 'active';
        });

        require_once __DIR__ . '/../views/settings/branch_list.php';
    }

    // 2. LÆ¯U THÃŠM Má»šI HOáº¶C Cáº¬P NHáº¬T
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : 0;

            $branch_name = trim($_POST['branch_name'] ?? '');
            $branch_code = trim($_POST['branch_code'] ?? '');
            if (empty($branch_code)) $branch_code = 'CN' . date('YmdHis'); // Tá»± sinh mÃ£ náº¿u Ä‘á»ƒ trá»‘ng

            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $country = $_POST['country'] ?? 'Vietnam';
            $province = $_POST['province'] ?? '';
            $ward = $_POST['ward'] ?? '';
            $address_detail = $_POST['address_detail'] ?? '';

            $is_new_address_format = isset($_POST['is_new_address_format']) ? 1 : 0;
            $district = ($is_new_address_format == 1) ? NULL : ($_POST['district'] ?? '');

            $has_inventory = isset($_POST['has_inventory']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $is_pickup_location = isset($_POST['is_pickup_location']) ? 1 : 0;

            $db = (new Database())->getConnection();

            // Náº¿u set lÃ m máº·c Ä‘á»‹nh, pháº£i gá»¡ máº·c Ä‘á»‹nh cá»§a cÃ¡c chi nhÃ¡nh khÃ¡c
            if ($is_default == 1) {
                $db->query("UPDATE branches SET is_default = 0");
                $has_inventory = 1; // Máº·c Ä‘á»‹nh báº¯t buá»™c pháº£i cÃ³ quáº£n lÃ½ kho
                $is_pickup_location = 1; // Máº·c Ä‘á»‹nh báº¯t buá»™c lÃ  Ä‘iá»ƒm láº¥y hÃ ng
            }

            if ($id > 0) {
                // UPDATE (LÆ°u Ã½: KhÃ´ng cho sá»­a branch_code theo tÃ i liá»‡u Há»‡ thá»‘ng)
                $stmt = $db->prepare("UPDATE branches SET branch_name=?, phone=?, email=?, country=?, province=?, district=?, ward=?, address_detail=?, is_new_address_format=?, has_inventory=?, is_default=?, is_pickup_location=? WHERE id=?");
                $stmt->execute([$branch_name, $phone, $email, $country, $province, $district, $ward, $address_detail, $is_new_address_format, $has_inventory, $is_default, $is_pickup_location, $id]);
            } else {
                // INSERT
                $stmt = $db->prepare("INSERT INTO branches (branch_code, branch_name, phone, email, country, province, district, ward, address_detail, is_new_address_format, has_inventory, is_default, is_pickup_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$branch_code, $branch_name, $phone, $email, $country, $province, $district, $ward, $address_detail, $is_new_address_format, $has_inventory, $is_default, $is_pickup_location]);
            }

            header("Location: index.php?action=branch_list&success=1");
            exit;
        }
    }

    // 3. Äá»”I TRáº NG THÃI (Ngá»«ng hoáº¡t Ä‘á»™ng / KÃ­ch hoáº¡t)
    public function toggle_status()
    {
        $id = intval($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? 'active';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE branches SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        header("Location: index.php?action=branch_list&success=1");
    }

    // 4. CHUYá»‚N GIAO GIAO Dá»ŠCH & XÃ“A KHO CHI NHÃNH ÄÃ“NG Cá»¬A
    public function transfer_and_delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from_branch_id = intval($_POST['from_branch_id']);
            $to_branch_id = intval($_POST['to_branch_id']);

            $db = (new Database())->getConnection();
            try {
                $db->beginTransaction();

                // Chuyá»ƒn ÄÆ¡n hÃ ng chÆ°a giao xong sang chi nhÃ¡nh má»›i
                $db->prepare("UPDATE orders SET branch_id = ? WHERE branch_id = ? AND shipping_status != 'delivered'")->execute([$to_branch_id, $from_branch_id]);

                // Chuyá»ƒn Phiáº¿u thu / chi liÃªn quan sang chi nhÃ¡nh má»›i
                $db->prepare("UPDATE receipts SET branch_id = ? WHERE branch_id = ?")->execute([$to_branch_id, $from_branch_id]);
                $db->prepare("UPDATE expenses SET branch_id = ? WHERE branch_id = ?")->execute([$to_branch_id, $from_branch_id]);

                // XÃ³a cá» quáº£n lÃ½ kho cá»§a chi nhÃ¡nh cÅ©
                $db->prepare("UPDATE branches SET has_inventory = 0 WHERE id = ?")->execute([$from_branch_id]);

                $db->commit();
                header("Location: index.php?action=branch_list&success_transfer=1");
            } catch (Exception $e) {
                $db->rollBack();
                die("Lá»—i há»‡ thá»‘ng khi chuyá»ƒn giao dá»¯ liá»‡u: " . $e->getMessage());
            }
        }
    }
    // 5. Cáº¬P NHáº¬T THá»¨ Tá»° Æ¯U TIÃŠN NHáº¬N ÄÆ N ONLINE (KÃ‰O THáº¢)
    public function update_priority()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['priorities']) && is_array($data['priorities'])) {
                $db = (new Database())->getConnection();
                try {
                    $db->beginTransaction();
                    // LÆ°u thá»© tá»± 1, 2, 3... dá»±a trÃªn máº£ng gá»­i lÃªn tá»« Javascript
                    foreach ($data['priorities'] as $index => $id) {
                        $stmt = $db->prepare("UPDATE branches SET routing_priority = ? WHERE id = ?");
                        $stmt->execute([$index + 1, $id]);
                    }
                    $db->commit();
                    echo json_encode(['status' => 'success', 'msg' => 'ÄÃ£ lÆ°u cáº¥u hÃ¬nh Æ°u tiÃªn nháº­n Ä‘Æ¡n Online!']);
                } catch (Exception $e) {
                    $db->rollBack();
                    echo json_encode(['status' => 'error', 'msg' => 'Lá»—i: ' . $e->getMessage()]);
                }
            }
            exit;
        }
    }
}

