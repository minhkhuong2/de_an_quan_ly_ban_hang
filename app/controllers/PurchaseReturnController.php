<?php
// Đường dẫn: app/controllers/PurchaseReturnController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/PurchaseReturnModel.php';
require_once __DIR__ . '/../models/PurchaseOrderModel.php';

class PurchaseReturnController
{

    public function list()
    {
        $db = (new Database())->getConnection();
        $returnModel = new PurchaseReturnModel($db);

        // Hứng dữ liệu tìm kiếm & lọc từ URL
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $returns = $returnModel->getAllReturns($search, $status);
        require_once __DIR__ . '/../views/purchase_return/list.php';
    }

    // Hiển thị Form trả hàng (Lấy dữ liệu từ phiếu nhập gốc)
    public function add()
    {
        $order_id = $_GET['order_id'] ?? 0;
        $db = (new Database())->getConnection();
        $poModel = new PurchaseOrderModel($db);

        $order = $poModel->getOrderById($order_id);
        if (!$order || $order['status'] != 'Nhập toàn bộ') {
            header("Location: index.php?action=purchase_list");
            exit;
        }
        $details = $poModel->getOrderDetails($order_id);
        require_once __DIR__ . '/../views/purchase_return/add.php';
    }

    // Xử lý Lưu khi bấm nút "Hoàn trả"
    public function process()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $order_id = $_POST['order_id'];
            $supplier_name = $_POST['supplier_name'];
            $branch = $_POST['branch'] ?? 'Cửa hàng chính';
            $employee = $_POST['employee'] ?? 'Admin';
            $reason = $_POST['reason'] ?? '';

            $product_ids = $_POST['product_id'] ?? [];
            $return_qtys = $_POST['return_qty'] ?? [];
            $prices = $_POST['price'] ?? [];

            $products = [];
            $total_amount = 0;

            for ($i = 0; $i < count($product_ids); $i++) {
                $qty = (int)$return_qtys[$i];
                if ($qty > 0) { // Chỉ xử lý những sản phẩm có số lượng trả > 0
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
                $db = (new Database())->getConnection();
                $returnModel = new PurchaseReturnModel($db);
                if ($returnModel->createReturn($order_id, $supplier_name, $branch, $employee, $reason, $products, $total_amount)) {
                    header("Location: index.php?action=purchase_return_list&success=1");
                    exit;
                }
            } else {
                header("Location: index.php?action=add_purchase_return&order_id=$order_id&error=1");
                exit;
            }
        }
    }
    // Giao diện và Xử lý form Trả hàng không theo đơn
    public function addDirect()
    {
        $db = (new Database())->getConnection();
        $returnModel = new PurchaseReturnModel($db);

        // Gọi ProductModel để lấy danh sách sản phẩm hiển thị ra ô Tìm kiếm
        require_once __DIR__ . '/../models/ProductModel.php';
        $productModel = new ProductModel($db);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $supplier_name = $_POST['supplier_name'] ?? 'Khách lẻ / NCC Khác';
            $branch = $_POST['branch'] ?? 'Cửa hàng chính';
            $employee = $_POST['employee'] ?? 'Admin';
            $reason = $_POST['reason'] ?? 'Trả hàng xuất thẳng từ kho';

            $product_ids = $_POST['product_id'] ?? [];
            $return_qtys = $_POST['return_qty'] ?? [];
            $prices = $_POST['price'] ?? [];

            $products = [];
            $total_amount = 0;

            for ($i = 0; $i < count($product_ids); $i++) {
                $qty = (int)$return_qtys[$i];
                if ($qty > 0) {
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
                // order_id = 0 mang ý nghĩa "Không theo đơn nhập nào"
                if ($returnModel->createReturn(0, $supplier_name, $branch, $employee, $reason, $products, $total_amount)) {
                    header("Location: index.php?action=purchase_return_list&success_direct=1");
                    exit;
                }
            }
        }

        $allProducts = $productModel->getAllProducts();
        require_once __DIR__ . '/../views/purchase_return/add_direct.php';
    }
}
