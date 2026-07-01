<?php

class ReturnOrderController
{
    public function index()
    {
        $db = (new Database())->getConnection();
        
        $status = $_GET['status'] ?? 'all';
        $keyword = $_GET['keyword'] ?? '';
        
        $sql = "SELECT r.*, o.order_code, c.last_name, c.first_name, c.phone as customer_phone
                FROM order_returns r
                JOIN orders o ON r.order_id = o.id
                LEFT JOIN customers c ON o.customer_id = c.id
                WHERE r.is_archived = 0";
                
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (r.return_code LIKE ? OR o.order_code LIKE ? OR c.phone LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        
        if ($status === 'pending_receive') {
            $sql .= " AND r.receive_status = 'pending'";
        } elseif ($status === 'pending_refund') {
            $sql .= " AND r.refund_status = 'pending'";
        } elseif ($status === 'completed') {
            $sql .= " AND r.receive_status = 'received' AND r.refund_status = 'refunded'";
        }

        $sql .= " ORDER BY r.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/return/list.php';
    }

    public function create()
    {
        $order_id = $_GET['order_id'] ?? 0;
        if (!$order_id) {
            die("Missing order ID");
        }

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Order not found");
        }

        // Lấy danh sách sản phẩm trong đơn gốc để có thể trả hàng
        // Chỉ lấy những item nào số lượng đã mua > số lượng đã trả
        $stmt_items = $db->prepare("
            SELECT oi.*, p.product_name, p.sku
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ? AND oi.quantity > oi.returned_qty
        ");
        $stmt_items->execute([$order_id]);
        $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách toàn bộ sản phẩm để phục vụ khối "Đổi hàng"
        $query_products = "SELECT id, product_name, sku, retail_price AS price, stock_quantity AS stock FROM products";
        $stmt_prod = $db->query($query_products);
        $all_products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
        $products_json = json_encode($all_products);

        require_once __DIR__ . '/../views/return/create.php';
    }

    public function store()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['order_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $db = (new Database())->getConnection();
        $order_id = $data['order_id'];
        $action_type = $data['action_type'] ?? 'draft'; // 'draft', 'confirm', 'refund_now'
        
        $return_items = $data['return_items'] ?? [];
        $exchange_items = $data['exchange_items'] ?? [];
        $summary = $data['summary'] ?? [];

        try {
            $db->beginTransaction();

            // Lấy thông tin order gốc để sinh return_code
            $stmt = $db->prepare("SELECT order_code, grand_total, customer_id FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) throw new Exception("Order not found");

            // Sinh mã return_code (SON-XXXXX-R1)
            $stmt_count = $db->prepare("SELECT COUNT(*) FROM order_returns WHERE order_id = ?");
            $stmt_count->execute([$order_id]);
            $count = $stmt_count->fetchColumn() + 1;
            $return_code = $order['order_code'] . '-R' . $count;

            $receive_status = ($action_type === 'draft') ? 'pending' : 'received';
            $refund_status = ($action_type === 'refund_now') ? 'refunded' : 'pending';

            $total_return_value = $summary['total_return_value'] ?? 0;
            $total_exchange_value = $summary['total_exchange_value'] ?? 0;
            $refund_amount = $summary['refund_amount'] ?? 0; // >0 hoàn khách, <0 khách trả thêm

            $stmt_ret = $db->prepare("
                INSERT INTO order_returns 
                (order_id, return_code, branch_id, staff_id, total_return_value, total_exchange_value, refund_amount, receive_status, refund_status, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_ret->execute([
                $order_id,
                $return_code,
                $data['branch_id'] ?? 1,
                $data['staff_id'] ?? 1,
                $total_return_value,
                $total_exchange_value,
                $refund_amount,
                $receive_status,
                $refund_status,
                $data['note'] ?? ''
            ]);
            $return_id = $db->lastInsertId();

            // Insert return items
            $stmt_ri = $db->prepare("INSERT INTO order_return_items (return_id, order_item_id, product_id, qty_returned, return_price, line_total) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($return_items as $ri) {
                if ($ri['qty_returned'] > 0) {
                    $stmt_ri->execute([$return_id, $ri['order_item_id'], $ri['product_id'], $ri['qty_returned'], $ri['price'], $ri['line_total']]);
                    
                    if ($action_type !== 'draft') {
                        // 1. Cập nhật số lượng trả trong order_items
                        $db->prepare("UPDATE order_items SET returned_qty = returned_qty + ? WHERE id = ?")->execute([$ri['qty_returned'], $ri['order_item_id']]);
                        // 2. Tăng tồn kho
                        $db->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?")->execute([$ri['qty_returned'], $ri['product_id']]);
                    }
                }
            }

            // Insert exchange items
            $stmt_ei = $db->prepare("INSERT INTO order_exchange_items (return_id, product_id, qty, price, discount, line_total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_oi = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, discount, line_total) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($exchange_items as $ei) {
                if ($ei['qty'] > 0) {
                    $stmt_ei->execute([$return_id, $ei['product_id'], $ei['qty'], $ei['price'], $ei['discount'] ?? 0, $ei['line_total']]);
                    
                    if ($action_type !== 'draft') {
                        // 1. Thêm sản phẩm đổi vào đơn gốc
                        $stmt_oi->execute([$order_id, $ei['product_id'], $ei['qty'], $ei['price'], $ei['discount'] ?? 0, $ei['line_total']]);
                        // 2. Giảm tồn kho
                        $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?")->execute([$ei['qty'], $ei['product_id']]);
                    }
                }
            }

            // Nếu confirm, cập nhật giá trị đơn gốc
            if ($action_type !== 'draft') {
                $new_grand_total = $order['grand_total'] - $total_return_value + $total_exchange_value;
                $db->prepare("UPDATE orders SET grand_total = ? WHERE id = ?")->execute([$new_grand_total, $order_id]);
                
                // Nếu refund_now, ghi nhận thanh toán (phiếu thu / phiếu chi)
                if ($action_type === 'refund_now') {
                    if ($refund_amount > 0) {
                        // Hoàn tiền cho khách (Phiếu chi)
                        $stmt_exp = $db->prepare("INSERT INTO expenses (expense_date, amount, expense_category, note, created_at, status) VALUES (NOW(), ?, 'Hoàn tiền trả hàng', ?, NOW(), 'Đã thanh toán')");
                        $stmt_exp->execute([$refund_amount, "Hoàn tiền cho đơn trả $return_code"]);
                    } elseif ($refund_amount < 0) {
                        // Khách nợ thêm -> thu thêm tiền khách (Phiếu thu)
                        $abs_amount = abs($refund_amount);
                        $stmt_rec = $db->prepare("INSERT INTO receipts (receipt_date, amount, payer_name, payer_phone, payment_method, note, created_at, status, customer_id) VALUES (NOW(), ?, ?, ?, 'Chuyển khoản', ?, NOW(), 'Đã thu', ?)");
                        $stmt_rec->execute([$abs_amount, "Khách hàng", "", "Thu thêm tiền hàng đổi đơn $return_code", $order['customer_id']]);
                    }
                }
            }

            $db->commit();
            echo json_encode(['success' => true, 'return_id' => $return_id, 'return_code' => $return_code, 'message' => 'Tạo đơn đổi trả thành công']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT r.*, o.order_code, c.last_name, c.first_name FROM order_returns r JOIN orders o ON r.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id WHERE r.id = ?");
        $stmt->execute([$id]);
        $return_order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$return_order) die("Return order not found");

        $stmt_ri = $db->prepare("SELECT ri.*, p.product_name, p.sku FROM order_return_items ri JOIN products p ON ri.product_id = p.id WHERE ri.return_id = ?");
        $stmt_ri->execute([$id]);
        $return_items = $stmt_ri->fetchAll(PDO::FETCH_ASSOC);

        $stmt_ei = $db->prepare("SELECT ei.*, p.product_name, p.sku FROM order_exchange_items ei JOIN products p ON ei.product_id = p.id WHERE ei.return_id = ?");
        $stmt_ei->execute([$id]);
        $exchange_items = $stmt_ei->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/return/detail.php';
    }

    public function receive_items()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $return_id = $data['return_id'] ?? 0;
        if (!$return_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $db = (new Database())->getConnection();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT * FROM order_returns WHERE id = ?");
            $stmt->execute([$return_id]);
            $return_order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($return_order['receive_status'] === 'received') {
                throw new Exception("Đơn trả đã được nhận hàng trước đó.");
            }

            // Lấy danh sách return items
            $stmt_ri = $db->prepare("SELECT * FROM order_return_items WHERE return_id = ?");
            $stmt_ri->execute([$return_id]);
            $return_items = $stmt_ri->fetchAll(PDO::FETCH_ASSOC);

            foreach ($return_items as $ri) {
                // 1. Tăng tồn kho
                $db->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?")->execute([$ri['qty_returned'], $ri['product_id']]);
                // 2. Cập nhật số lượng trả trong order_items gốc
                $db->prepare("UPDATE order_items SET returned_qty = returned_qty + ? WHERE id = ?")->execute([$ri['qty_returned'], $ri['order_item_id']]);
            }

            // Xử lý luôn exchange items (nếu có)
            $stmt_ei = $db->prepare("SELECT * FROM order_exchange_items WHERE return_id = ?");
            $stmt_ei->execute([$return_id]);
            $exchange_items = $stmt_ei->fetchAll(PDO::FETCH_ASSOC);
            if (count($exchange_items) > 0) {
                $stmt_oi = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, discount, line_total) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($exchange_items as $ei) {
                    // Thêm vào đơn gốc
                    $stmt_oi->execute([$return_order['order_id'], $ei['product_id'], $ei['qty'], $ei['price'], $ei['discount'], $ei['line_total']]);
                    // Giảm tồn kho
                    $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?")->execute([$ei['qty'], $ei['product_id']]);
                }
                
                // Cập nhật tổng tiền đơn gốc
                $new_grand_total = $db->query("SELECT grand_total FROM orders WHERE id = " . $return_order['order_id'])->fetchColumn() - $return_order['total_return_value'] + $return_order['total_exchange_value'];
                $db->prepare("UPDATE orders SET grand_total = ? WHERE id = ?")->execute([$new_grand_total, $return_order['order_id']]);
            }

            $db->prepare("UPDATE order_returns SET receive_status = 'received' WHERE id = ?")->execute([$return_id]);
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Nhận hàng thành công']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function refund()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $return_id = $data['return_id'] ?? 0;
        
        $db = (new Database())->getConnection();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT r.*, o.customer_id FROM order_returns r JOIN orders o ON r.order_id = o.id WHERE r.id = ?");
            $stmt->execute([$return_id]);
            $return_order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($return_order['refund_status'] === 'refunded') {
                throw new Exception("Đơn trả đã được hoàn tiền.");
            }

            $refund_amount = $return_order['refund_amount'];
            if ($refund_amount > 0) {
                $stmt_exp = $db->prepare("INSERT INTO expenses (expense_date, amount, expense_category, note, created_at, status) VALUES (NOW(), ?, 'Hoàn tiền trả hàng', ?, NOW(), 'Đã thanh toán')");
                $stmt_exp->execute([$refund_amount, "Hoàn tiền cho đơn trả " . $return_order['return_code']]);
            } elseif ($refund_amount < 0) {
                $abs_amount = abs($refund_amount);
                $stmt_rec = $db->prepare("INSERT INTO receipts (receipt_date, amount, payer_name, payer_phone, payment_method, note, created_at, status, customer_id) VALUES (NOW(), ?, ?, ?, 'Chuyển khoản', ?, NOW(), 'Đã thu', ?)");
                $stmt_rec->execute([$abs_amount, "Khách hàng", "", "Thu thêm tiền hàng đổi đơn " . $return_order['return_code'], $return_order['customer_id']]);
            }

            $db->prepare("UPDATE order_returns SET refund_status = 'refunded' WHERE id = ?")->execute([$return_id]);
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Hoàn tiền thành công']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cancel()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $return_id = $data['return_id'] ?? 0;

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT receive_status, refund_status FROM order_returns WHERE id = ?");
        $stmt->execute([$return_id]);
        $status = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($status['receive_status'] === 'received' || $status['refund_status'] === 'refunded') {
            echo json_encode(['success' => false, 'message' => 'Không thể hủy đơn trả hàng đã nhận hàng hoặc hoàn tiền.']);
            return;
        }

        $db->prepare("DELETE FROM order_return_items WHERE return_id = ?")->execute([$return_id]);
        $db->prepare("DELETE FROM order_exchange_items WHERE return_id = ?")->execute([$return_id]);
        $db->prepare("DELETE FROM order_returns WHERE id = ?")->execute([$return_id]);

        echo json_encode(['success' => true, 'message' => 'Hủy phiếu trả hàng thành công']);
    }
}
