<?php
// Đường dẫn: app/controllers/PurchaseOrderController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PurchaseOrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class PurchaseOrderController
{

    // HÀM MỚI BỔ SUNG ĐỂ SỬA LỖI FATAL ERROR
    public function list()
    {
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);

        $orders = $poModel->getAllOrders();

        require_once __DIR__ . '/../views/purchase_order/list.php';
    }

    public function add()
    {
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_name = $_POST['supplier_name'] ?? 'Khách lẻ';
            $branch = $_POST['branch'] ?? 'Cửa hàng chính';
            $employee = $_POST['employee'] ?? 'Admin';
            $expected_date = $_POST['expected_date'] ?? date('Y-m-d');
            $reference = $_POST['reference'] ?? '';

            // Phân biệt trạng thái duyệt đơn theo đúng tài liệu
            $status = isset($_POST['btn_approve']) ? 'Chờ nhập' : 'Đơn nháp';

            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            $products = [];
            $total_amount = 0;

            for ($i = 0; $i < count($product_ids); $i++) {
                if (!empty($product_ids[$i])) {
                    $qty = (int)$quantities[$i];
                    $price = (float)$prices[$i];
                    $products[] = [
                        'product_id' => $product_ids[$i],
                        'quantity' => $qty,
                        'price' => $price
                    ];
                    $total_amount += ($qty * $price);
                }
            }

            if (!empty($products)) {
                $order_id = $poModel->createPurchaseOrder($supplier_name, $branch, $employee, $expected_date, $reference, $status, $products, $total_amount);
                if ($order_id) {
                    // Cập nhật lại đường dẫn về trang danh sách
                    header("Location: index.php?action=purchase_list&success=1");
                    exit;
                }
            }
        }

        // Lấy danh sách sản phẩm để thả vào Dropdown tìm kiếm
        $allProducts = $productModel->getAllProducts();

        require_once __DIR__ . '/../views/purchase_order/add.php';
    }
    // Hiển thị Chi tiết đơn nhập hàng
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);

        $order = $poModel->getOrderById($id);
        if (!$order) {
            header("Location: index.php?action=purchase_list");
            exit;
        }
        $details = $poModel->getOrderDetails($id);

        require_once __DIR__ . '/../views/purchase_order/detail.php';
    }

    // Xử lý Hủy đơn hàng
    public function cancel()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new Database())->getConnection();
            $poModel = new PurchaseOrderModel($db);
            $poModel->cancelOrder($id);
        }
        header("Location: index.php?action=view_purchase&id=" . $id . "&success_cancel=1");
        exit;
    }
    // Hiển thị Form Nhập hàng thực tế
    public function receiveForm()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);

        $order = $poModel->getOrderById($id);
        if (!$order || $order['status'] != 'Chờ nhập') {
            header("Location: index.php?action=purchase_list");
            exit;
        }

        // Lấy chi tiết sản phẩm để hiển thị lên Form
        $details = $poModel->getOrderDetails($id);

        require_once __DIR__ . '/../views/purchase_order/receive.php';
    }

    // Xử lý khi bấm nút "Nhập hàng"
    public function processReceive()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $product_ids = $_POST['product_id'];
            $received_qtys = $_POST['receive_qty']; // Số lượng thực tế nhập vào

            $received_items = [];
            for ($i = 0; $i < count($product_ids); $i++) {
                $received_items[] = [
                    'product_id' => $product_ids[$i],
                    'qty' => (int)$received_qtys[$i]
                ];
            }

            $db = (new Database())->getConnection();
            $poModel = new PurchaseOrderModel($db);

            if ($poModel->processReceiveOrder($order_id, $received_items)) {
                // Chuyển về trang chi tiết kèm thông báo thành công
                header("Location: index.php?action=view_purchase&id=" . $order_id . "&success_receive=1");
                exit;
            }
        }
    }
    // Giao diện và Xử lý form Tạo đơn Nhập hàng trực tiếp
    public function direct_receive()
    {
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_name = $_POST['supplier_name'] ?? 'Nhà cung cấp lẻ';
            $branch = $_POST['branch'] ?? 'Cửa hàng chính';
            $employee = $_POST['employee'] ?? 'Admin';
            $expected_date = $_POST['expected_date'] ?? date('Y-m-d');
            $reference = $_POST['reference'] ?? '';

            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            $products = [];
            $total_amount = 0;

            for ($i = 0; $i < count($product_ids); $i++) {
                if (!empty($product_ids[$i])) {
                    $qty = (int)$quantities[$i];
                    $price = (float)$prices[$i];
                    $products[] = [
                        'product_id' => $product_ids[$i],
                        'quantity' => $qty,
                        'price' => $price
                    ];
                    $total_amount += ($qty * $price);
                }
            }

            if (!empty($products)) {
                $order_id = $poModel->createDirectReceipt($supplier_name, $branch, $employee, $expected_date, $reference, $products, $total_amount);
                if ($order_id) {
                    header("Location: index.php?action=purchase_list&success_direct=1");
                    exit;
                }
            }
        }

        $allProducts = $productModel->getAllProducts();
        require_once __DIR__ . '/../views/purchase_order/direct_receive.php';
    }
    // Hiển thị danh sách Phiếu nhập hàng (Đã nhập kho)
    public function receiptList()
    {
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);

        // Hứng dữ liệu tìm kiếm từ thanh địa chỉ (URL)
        $search = $_GET['search'] ?? '';

        // Lấy danh sách phiếu nhập
        $receipts = $poModel->getReceipts($search);

        require_once __DIR__ . '/../views/purchase_order/receipt_list.php';
    }
    // Xử lý nút Xác nhận thanh toán
    public function pay()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $amount = (float)$_POST['amount'];

            $db = (new Database())->getConnection();
            $poModel = new PurchaseOrderModel($db);

            if ($amount > 0) {
                $poModel->addPayment($order_id, $amount);
            }

            // Chuyển hướng lại về trang chi tiết kèm thông báo
            header("Location: index.php?action=view_purchase&id=" . $order_id . "&success_pay=1");
            exit;
        }
    }
}
