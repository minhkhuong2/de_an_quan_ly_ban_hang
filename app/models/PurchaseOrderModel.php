<?php
// Đường dẫn: app/models/PurchaseOrderModel.php
class PurchaseOrderModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createPurchaseOrder($supplier_name, $branch, $employee, $expected_date, $reference, $status, $products, $total_amount)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lưu thông tin chung của Đơn đặt hàng
            $query = "INSERT INTO purchase_orders (supplier_name, branch, employee, expected_date, reference, status, total_amount, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$supplier_name, $branch, $employee, $expected_date, $reference, $status, $total_amount]);
            $order_id = $this->conn->lastInsertId();

            // 2. Lưu chi tiết sản phẩm & Cập nhật trạng thái "Đang về kho"
            $queryDetail = "INSERT INTO purchase_order_details (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            foreach ($products as $item) {
                $stmtDetail->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

                // Dựa theo tài liệu Sapo: Duyệt đơn thì mới tăng "Đang về kho"
                if ($status == 'Chờ nhập') {
                    $updateStock = "UPDATE products SET dang_ve_kho = dang_ve_kho + ? WHERE id = ?";
                    $stmtStock = $this->conn->prepare($updateStock);
                    $stmtStock->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function getAllOrders()
    {
        $query = "SELECT * FROM purchase_orders ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOrderById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các sản phẩm trong đơn đặt hàng
    public function getOrderDetails($order_id)
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, p.product_name, p.sku, p.image 
            FROM purchase_order_details d
            JOIN products p ON d.product_id = p.id
            WHERE d.order_id = ?
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hủy đơn hàng và xử lý trừ Tồn kho (Đang về kho)
    public function cancelOrder($id)
    {
        try {
            $this->conn->beginTransaction();
            $order = $this->getOrderById($id);

            // Chỉ cho phép hủy nếu là Đơn nháp hoặc Chờ nhập
            if ($order && in_array($order['status'], ['Đơn nháp', 'Chờ nhập'])) {

                // Nếu đơn đang "Chờ nhập", phải TRỪ đi số lượng "Đang về kho"
                if ($order['status'] == 'Chờ nhập') {
                    $details = $this->getOrderDetails($id);
                    $updateStock = "UPDATE products SET dang_ve_kho = dang_ve_kho - ? WHERE id = ?";
                    $stmtStock = $this->conn->prepare($updateStock);

                    foreach ($details as $item) {
                        $stmtStock->execute([$item['quantity'], $item['product_id']]);
                    }
                }

                // Cập nhật trạng thái thành Đã hủy
                $stmt = $this->conn->prepare("UPDATE purchase_orders SET status = 'Đã hủy' WHERE id = ?");
                $stmt->execute([$id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    // Xử lý Nhập hàng vào kho từ Đơn đặt hàng
    public function processReceiveOrder($order_id, $received_items)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy thông tin đơn hàng hiện tại
            $order = $this->getOrderById($order_id);
            if (!$order || $order['status'] != 'Chờ nhập') {
                throw new Exception("Đơn hàng không hợp lệ hoặc không ở trạng thái Chờ nhập!");
            }

            // 2. Cập nhật số lượng cho từng sản phẩm
            $updateStock = "UPDATE products SET dang_ve_kho = dang_ve_kho - ?, stock = stock + ?, available = available + ? WHERE id = ?";
            $stmtStock = $this->conn->prepare($updateStock);

            foreach ($received_items as $item) {
                // $item['qty'] là số lượng thực tế nhận được
                $stmtStock->execute([
                    $item['qty'], // Trừ số đang về kho
                    $item['qty'], // Cộng tồn kho tổng
                    $item['qty'], // Cộng số có thể bán
                    $item['product_id']
                ]);
            }

            // 3. Đổi trạng thái đơn đặt hàng thành "Nhập toàn bộ" (Tạm thời làm full để dễ quản lý)
            $stmtUpdateStatus = $this->conn->prepare("UPDATE purchase_orders SET status = 'Nhập toàn bộ' WHERE id = ?");
            $stmtUpdateStatus->execute([$order_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    // Xử lý tạo Đơn Nhập Hàng Trực Tiếp (Tăng tồn kho ngay lập tức)
    public function createDirectReceipt($supplier_name, $branch, $employee, $expected_date, $reference, $products, $total_amount)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lưu hóa đơn với trạng thái "Nhập toàn bộ" (Hoàn thành)
            $query = "INSERT INTO purchase_orders (supplier_name, branch, employee, expected_date, reference, status, total_amount, created_at) 
                      VALUES (?, ?, ?, ?, ?, 'Nhập toàn bộ', ?, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$supplier_name, $branch, $employee, $expected_date, $reference, $total_amount]);
            $order_id = $this->conn->lastInsertId();

            // 2. Lưu chi tiết sản phẩm và CỘNG TỒN KHO THỰC TẾ
            $queryDetail = "INSERT INTO purchase_order_details (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            $updateStock = "UPDATE products SET stock = stock + ?, available = available + ? WHERE id = ?";
            $stmtStock = $this->conn->prepare($updateStock);

            foreach ($products as $item) {
                // Lưu lịch sử chi tiết
                $stmtDetail->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

                // CỘNG TRỰC TIẾP VÀO KHO (Bỏ qua bước Đang về kho)
                $stmtStock->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
            }

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    // Lấy Danh sách Phiếu Nhập Hàng (Chỉ lấy những đơn đã nhập kho)
    public function getReceipts($search = '', $status = '')
    {
        // Lấy các đơn có trạng thái "Nhập toàn bộ" (Đã nhập kho)
        $query = "SELECT p.*, COALESCE(SUM(d.quantity), 0) as total_qty 
                  FROM purchase_orders p
                  LEFT JOIN purchase_order_details d ON p.id = d.order_id
                  WHERE p.status = 'Nhập toàn bộ' ";

        $params = [];

        // Xử lý Tìm kiếm theo Mã đơn hoặc Tên nhà cung cấp
        if (!empty($search)) {
            $query .= " AND (p.id LIKE ? OR p.supplier_name LIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Gom nhóm theo ID đơn hàng và sắp xếp mới nhất lên đầu
        $query .= " GROUP BY p.id ORDER BY p.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Xử lý Thanh toán công nợ cho Đơn nhập hàng
    public function addPayment($order_id, $amount)
    {
        // Dùng COALESCE để phòng trường hợp paid_amount đang bị NULL
        $query = "UPDATE purchase_orders SET paid_amount = COALESCE(paid_amount, 0) + ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$amount, $order_id]);
    }
    // Hàm xử lý Cập nhật đơn đặt hàng nhập và tính toán lại số lượng Đang về kho
    public function updatePurchaseOrder($order_id, $supplier_name, $branch, $employee, $expected_date, $reference, $products, $total_amount)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy thông tin đơn cũ trước khi sửa để hoàn tác số lượng Đang về kho cũ
            $old_order = $this->getOrderById($order_id);
            if ($old_order && $old_order['status'] == 'Chờ nhập') {
                $old_details = $this->getOrderDetails($order_id);
                // Trừ bớt hàng đang về của đơn cũ đi trước
                $stmtReturnStock = $this->conn->prepare("UPDATE products SET dang_ve_kho = dang_ve_kho - ? WHERE id = ?");
                foreach ($old_details as $old_item) {
                    $stmtReturnStock->execute([$old_item['quantity'], $old_item['product_id']]);
                }
            }

            // 2. Cập nhật thông tin chung của Đơn hàng
            $query = "UPDATE purchase_orders SET supplier_name = ?, branch = ?, employee = ?, expected_date = ?, reference = ?, total_amount = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$supplier_name, $branch, $employee, $expected_date, $reference, $total_amount, $order_id]);

            // 3. Xóa chi tiết cũ để ghi đè chi tiết mới
            $this->conn->prepare("DELETE FROM purchase_order_details WHERE order_id = ?")->execute([$order_id]);

            // 4. Ghi chi tiết mới & Cộng lại số lượng Đang về kho mới
            $queryDetail = "INSERT INTO purchase_order_details (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            foreach ($products as $item) {
                $stmtDetail->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

                // Nếu đơn ở trạng thái Chờ nhập thì cộng vào hàng đang về theo số lượng mới
                if ($old_order && $old_order['status'] == 'Chờ nhập') {
                    $stmtAddStock = $this->conn->prepare("UPDATE products SET dang_ve_kho = dang_ve_kho + ? WHERE id = ?");
                    $stmtAddStock->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Hàm xử lý Xóa đơn đặt hàng nhập và hoàn trả lại số lượng Đang về kho
    public function deletePurchaseOrder($id)
    {
        try {
            $this->conn->beginTransaction();
            $order = $this->getOrderById($id);

            if ($order) {
                // Nếu đơn đang "Chờ nhập", bắt buộc phải TRỪ số lượng "Đang về kho" của sản phẩm đó đi
                if ($order['status'] == 'Chờ nhập') {
                    $details = $this->getOrderDetails($id);
                    $stmtStock = $this->conn->prepare("UPDATE products SET dang_ve_kho = dang_ve_kho - ? WHERE id = ?");
                    foreach ($details as $item) {
                        $stmtStock->execute([$item['quantity'], $item['product_id']]);
                    }
                }

                // Xóa dữ liệu đơn hàng trong Database
                $this->conn->prepare("DELETE FROM purchase_order_details WHERE order_id = ?")->execute([$id]);
                $this->conn->prepare("DELETE FROM purchase_orders WHERE id = ?")->execute([$id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
