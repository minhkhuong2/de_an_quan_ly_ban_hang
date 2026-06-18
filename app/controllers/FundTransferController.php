<?php
// Đường dẫn: app/controllers/FundTransferController.php
require_once __DIR__ . '/../../config/database.php';

class FundTransferController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 1. TRANG DANH SÁCH PHIẾU CHUYỂN QUỸ
    public function index()
    {
        // Truy vấn tất cả phiếu chuyển quỹ
        $stmt = $this->db->query("SELECT * FROM fund_transfers ORDER BY id DESC");
        $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_list.php';
    }

    // Giao diện Tạo Phiếu chuyển quỹ nội bộ (Giữ nguyên bài cũ)
    public function create()
    {
        try {
            $stmt_br = $this->db->query("SELECT id, branch_name FROM branches");
            $branches = $stmt_br->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhánh mặc định'], ['id' => 2, 'branch_name' => 'Chi nhánh miền Nam']];
        }

        $stmt_bank = $this->db->query("SELECT id, bank_name, account_number, account_name FROM bank_accounts WHERE status = 'active'");
        $bank_accounts = $stmt_bank->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_create.php';
    }

    // Xử lý lưu Phiếu chuyển quỹ (Giữ nguyên bài cũ)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from_type = $_POST['from_type'];
            $from_id = ($from_type === 'cash') ? $_POST['from_branch_id'] : $_POST['from_bank_id'];

            $to_type = $_POST['to_type'];
            // Nếu người dùng chọn trống ở bước tạo (cho phép bổ sung sau)
            $to_id = 0;
            if ($to_type === 'cash' && isset($_POST['to_branch_id'])) $to_id = $_POST['to_branch_id'];
            if ($to_type === 'bank' && isset($_POST['to_bank_id'])) $to_id = $_POST['to_bank_id'];

            $amount = floatval($_POST['amount']);
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            // Xử lý ngày nhận tiền (nếu trống thì cho phép cập nhật sau)
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

    // 2. TRANG XEM CHI TIẾT & CHỈNH SỬA PHIẾU CHUYỂN QUỸ (Mục 2 & 3 tài liệu Sapo)
    public function detail()
    {
        $id = $_GET['id'] ?? 0;

        // Lấy thông tin chi tiết phiếu chi chuyển quỹ
        $stmt = $this->db->prepare("SELECT * FROM fund_transfers WHERE id = ?");
        $stmt->execute([$id]);
        $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transfer) {
            die("Không tìm thấy phiếu chuyển quỹ hợp lệ!");
        }

        // Lấy mảng Chi nhánh và Ngân hàng động để phục vụ cho ô Select sửa đổi
        try {
            $branches = $this->db->query("SELECT id, branch_name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $branches = [['id' => 1, 'branch_name' => 'Chi nhánh mặc định'], ['id' => 2, 'branch_name' => 'Chi nhánh miền Nam']];
        }
        $bank_accounts = $this->db->query("SELECT id, bank_name, account_number FROM bank_accounts")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/fund/transfer_detail.php';
    }

    // 3. XỬ LÝ CẬP NHẬT PHIẾU CHUYỂN QUỸ (ÁP DỤNG ĐÚNG RÀNG BUỘC KHÓA CỦA SAPO)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            $reference_code = trim($_POST['reference_code']);

            // Đọc lại dữ liệu cũ từ DB trước để kiểm tra điều kiện chặn sửa (Mục 2)
            $stmt_check = $this->db->prepare("SELECT to_id, transaction_date FROM fund_transfers WHERE id = ?");
            $stmt_check->execute([$id]);
            $old = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$old) {
                die("Yêu cầu không hợp lệ.");
            }

            // Quy tắc Sapo: Nếu to_id cũ bằng 0 (chưa nhập) -> Cho phép cập nhật. Ngược lại -> Giữ nguyên dữ liệu cũ.
            $to_id = $old['to_id'];
            if ($old['to_id'] == 0 && isset($_POST['to_id_update'])) {
                $to_id = $_POST['to_id_update'];
            }

            // Quy tắc Sapo: Nếu ngày cũ đang NULL -> Cho phép điền ngày vào sổ. Ngược lại -> Giữ nguyên.
            $transaction_date = $old['transaction_date'];
            if (empty($old['transaction_date']) && !empty($_POST['transaction_date_update'])) {
                $transaction_date = $_POST['transaction_date_update'];
            }

            // Tiến hành cập nhật an toàn
            $stmt_up = $this->db->prepare("UPDATE fund_transfers SET description = ?, reference_code = ?, to_id = ?, transaction_date = ? WHERE id = ?");
            $stmt_up->execute([$description, $reference_code, $to_id, $transaction_date, $id]);

            header("Location: index.php?action=fund_transfer_detail&id=" . $id . "&success=updated");
            exit;
        }
    }
    // API: XÓA PHIẾU CHUYỂN QUỸ ĐƠN LẺ (Mục 1 tài liệu Sapo)
    public function delete_single()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                // Thực hiện xóa chứng từ trong DB
                $stmt = $this->db->prepare("DELETE FROM fund_transfers WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'msg' => 'Đã xóa vĩnh viễn phiếu chuyển quỹ nội bộ đơn lẻ thành công!']);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'ID phiếu không hợp lệ.']);
            exit;
        }
    }

    // API: XÓA PHIẾU CHUYỂN QUỸ HÀNG LOẠT (Mục 2 tài liệu Sapo)
    public function delete_bulk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $ids = $data['ids'] ?? [];

            if (!empty($ids) && is_array($ids)) {
                // Biến mảng ID thành chuỗi chấm hỏi ?,?,? để tránh lỗi SQL Injection
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $stmt = $this->db->prepare("DELETE FROM fund_transfers WHERE id IN ($placeholders)");
                $stmt->execute($ids);

                echo json_encode([
                    'status' => 'success',
                    'msg' => 'Đã xóa hàng loạt ' . count($ids) . ' phiếu chuyển quỹ nội bộ thành công!'
                ]);
                exit;
            }
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng chọn ít nhất 1 phiếu để xóa.']);
            exit;
        }
    }
}
