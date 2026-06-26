<?php
// ÄÆ°á»ng dáº«n: app/controllers/OrderSourceController.php
require_once __DIR__ . '/../../config/database.php';

class OrderSourceController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Hiá»ƒn thá»‹ danh sÃ¡ch nguá»“n Ä‘Æ¡n hÃ ng
    public function index()
    {
        // Láº¥y toÃ n bá»™ nguá»“n Ä‘Æ¡n sáº¯p xáº¿p theo thá»© tá»± Æ°u tiÃªn
        $stmt = $this->db->query("SELECT * FROM order_sources ORDER BY sort_order ASC, id DESC");
        $all_sources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Äáº¿m giá»›i háº¡n (Má»¥c 1 tÃ i liá»‡u Há»‡ thá»‘ng)
        $total_created = count($all_sources);

        // TÃ¡ch lÃ m 2 máº£ng theo tráº¡ng thÃ¡i tiáº¿ng Viá»‡t cá»§a KhÆ°Æ¡ng
        $active_sources = array_filter($all_sources, fn($s) => $s['status'] === 'Äang sá»­ dá»¥ng');
        $inactive_sources = array_filter($all_sources, fn($s) => $s['status'] === 'Ngá»«ng sá»­ dá»¥ng');

        require_once __DIR__ . '/../views/settings/order_sources.php';
    }

    // Táº¡o má»›i nguá»“n Ä‘Æ¡n hÃ ng tÃ¹y chá»‰nh (Má»¥c 4)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['source_name']);
            $category = $_POST['category_name'];
            $logo = trim($_POST['logo_url'] ?? '');

            if (!empty($name) && !empty($category)) {
                $stmt = $this->db->prepare("INSERT INTO order_sources (source_name, category_name, source_type, status, logo_url, sort_order) VALUES (?, ?, 'TÃ¹y chá»‰nh', 'Äang sá»­ dá»¥ng', ?, 99)");
                $stmt->execute([$name, $category, !empty($logo) ? $logo : null]);
                header("Location: index.php?action=order_sources&success=create");
                exit;
            }
        }
    }

    // Chá»‰nh sá»­a nguá»“n Ä‘Æ¡n hÃ ng (Má»¥c 5.b)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['source_name']);
            $category = $_POST['category_name'];
            $logo = trim($_POST['logo_url'] ?? '');

            // Chá»‰ cho sá»­a náº¿u lÃ  nguá»“n TÃ¹y chá»‰nh Ä‘á»ƒ báº£o vá»‡ nguá»“n Máº·c Ä‘á»‹nh
            $stmt_check = $this->db->prepare("SELECT source_type FROM order_sources WHERE id = ?");
            $stmt_check->execute([$id]);
            $src = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($src && $src['source_type'] === 'TÃ¹y chá»‰nh') {
                $stmt = $this->db->prepare("UPDATE order_sources SET source_name = ?, category_name = ?, logo_url = ? WHERE id = ?");
                $stmt->execute([$name, $category, !empty($logo) ? $logo : null, $id]);
                header("Location: index.php?action=order_sources&success=update");
                exit;
            }
        }
    }

    // Thay Ä‘á»•i tráº¡ng thÃ¡i Sá»­ dá»¥ng / Ngá»«ng sá»­ dá»¥ng (Má»¥c 5)
    public function toggle_status()
    {
        $id = $_GET['id'] ?? 0;
        $current_status = $_GET['status'] ?? '';
        $new_status = ($current_status === 'Äang sá»­ dá»¥ng') ? 'Ngá»«ng sá»­ dá»¥ng' : 'Äang sá»­ dá»¥ng';

        if ($id && !empty($current_status)) {
            $stmt = $this->db->prepare("UPDATE order_sources SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            header("Location: index.php?action=order_sources&success=toggle");
            exit;
        }
    }

    // XÃ³a hoÃ n toÃ n nguá»“n tÃ¹y chá»‰nh (Má»¥c 5.b)
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            // Chá»‰ cho xÃ³a nguá»“n TÃ¹y chá»‰nh
            $stmt = $this->db->prepare("DELETE FROM order_sources WHERE id = ? AND source_type = 'TÃ¹y chá»‰nh'");
            $stmt->execute([$id]);
            header("Location: index.php?action=order_sources&success=delete");
            exit;
        }
    }
}

