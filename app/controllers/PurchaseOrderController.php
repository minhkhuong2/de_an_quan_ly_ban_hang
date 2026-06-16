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

        // Lấy toàn bộ đơn hàng như cũ
        $orders = $poModel->getAllOrders();

        // Lấy từ khóa tìm kiếm và trạng thái từ URL
        $keyword = trim($_GET['keyword'] ?? '');
        $status = $_GET['status'] ?? '';

        // Xử lý Lọc Dữ Liệu bằng PHP
        if ($keyword !== '' || $status !== '') {
            $orders = array_filter($orders, function ($o) use ($keyword, $status) {
                $matchKeyword = true;
                $matchStatus = true;

                if ($keyword !== '') {
                    $kw = mb_strtolower($keyword, 'UTF-8');
                    $code = 'pon' . $o['id']; // Mã đơn hàng ảo để dễ tìm
                    $supplier = mb_strtolower($o['supplier_name'], 'UTF-8');

                    $matchKeyword = (strpos($code, $kw) !== false) || (strpos($supplier, $kw) !== false);
                }

                if ($status !== '') {
                    $matchStatus = ($o['status'] === $status);
                }

                return $matchKeyword && $matchStatus;
            });
        }

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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $order_id = $_POST['order_id'] ?? 0;
            $amount = (float)$_POST['amount'] ?? 0;

            if ($order_id && $amount > 0) {
                $db = (new Database())->getConnection();
                $poModel = new PurchaseOrderModel($db);

                // Lấy thông tin đơn hàng để biết tên Nhà cung cấp
                $order = $poModel->getOrderById($order_id);
                if ($order) {
                    // 1. Cập nhật số tiền đã thanh toán vào đơn hàng
                    $poModel->addPayment($order_id, $amount);

                    // 2. TỰ ĐỘNG TRỪ CÔNG NỢ CỦA NHÀ CUNG CẤP (Truyền vào số âm)
                    require_once __DIR__ . '/../models/SupplierModel.php';
                    $supplierModel = new SupplierModel($db);
                    $supplierModel->updateDebt($order['supplier_name'], -$amount);

                    // Quay lại trang chi tiết đơn hàng và báo thành công
                    header("Location: index.php?action=view_purchase&id=$order_id&success_pay=1");
                    exit;
                }
            }
        }
        header("Location: index.php?action=purchase_list");
        exit;
    }
    // Hàm xử lý Giao diện và Ghi nhận dữ liệu Form Sửa đơn đặt hàng nhập
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);
        $productModel = new ProductModel($db);

        $order = $poModel->getOrderById($id);
        if (!$order || $order['status'] == 'Nhập toàn bộ' || $order['status'] == 'Đã hủy') {
            // Không cho sửa các đơn đã hoàn thành hoặc đã hủy theo logic
            header("Location: index.php?action=purchase_list");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_name = $_POST['supplier_name'] ?? 'Khách lẻ';
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
                if ($poModel->updatePurchaseOrder($id, $supplier_name, $branch, $employee, $expected_date, $reference, $products, $total_amount)) {
                    header("Location: index.php?action=view_purchase&id=" . $id . "&success_edit=1");
                    exit;
                }
            }
        }

        $details = $poModel->getOrderDetails($id);
        $allProducts = $productModel->getAllProducts();

        require_once __DIR__ . '/../views/purchase_order/edit.php';
    }

    // Hàm xử lý hành động Xóa đơn đặt hàng nhập
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new Database())->getConnection();
            $poModel = new PurchaseOrderModel($db);
            $poModel->deletePurchaseOrder($id);
        }
        header("Location: index.php?action=purchase_list&success_delete=1");
        exit;
    }
}
