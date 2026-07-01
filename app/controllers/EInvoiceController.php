<?php

class EInvoiceController
{
    public function request_invoice()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $order_id = $data['order_id'] ?? 0;
        
        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $sql = "UPDATE orders SET 
                        invoice_status = 'pending_issue',
                        invoice_tax_code = ?,
                        invoice_company_name = ?,
                        invoice_address = ?,
                        invoice_buyer_name = ?,
                        invoice_phone = ?,
                        invoice_email = ?,
                        invoice_no_receipt = ?
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['tax_code'] ?? '',
                $data['company_name'] ?? '',
                $data['address'] ?? '',
                $data['buyer_name'] ?? '',
                $data['phone'] ?? '',
                $data['email'] ?? '',
                $data['no_receipt'] ?? 0,
                $order_id
            ]);

            echo json_encode(['success' => true, 'message' => 'Đã lưu yêu cầu xuất hóa đơn']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function issue_invoice()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $order_id = $data['order_id'] ?? 0;
        $symbol = $data['symbol'] ?? '1C24TML';

        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            
            // Generate mock invoice data
            $invoice_number = sprintf("%07d", rand(1, 9999999));
            $cqt_code = strtoupper(uniqid('CQT-'));
            $lookup_code = strtoupper(substr(md5(uniqid()), 0, 10));

            $sql = "UPDATE orders SET 
                        invoice_status = 'issued',
                        invoice_date = NOW(),
                        invoice_symbol = ?,
                        invoice_number = ?,
                        invoice_cqt_code = ?,
                        invoice_lookup_code = ?
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $symbol,
                $invoice_number,
                $cqt_code,
                $lookup_code,
                $order_id
            ]);

            echo json_encode(['success' => true, 'message' => 'Phát hành hóa đơn thành công!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function bulk_issue()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Không có đơn hàng nào được chọn']);
            return;
        }

        try {
            $db = (new Database())->getConnection();
            $in_placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            // Lấy danh sách các đơn đang ở trạng thái not_requested hoặc pending_issue
            $stmt = $db->prepare("SELECT id FROM orders WHERE id IN ($in_placeholders) AND invoice_status != 'issued'");
            $stmt->execute($ids);
            $valid_orders = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($valid_orders)) {
                echo json_encode(['success' => false, 'message' => 'Không có đơn hàng nào đủ điều kiện phát hành HĐĐT']);
                return;
            }

            foreach ($valid_orders as $order_id) {
                $invoice_number = sprintf("%07d", rand(1, 9999999));
                $cqt_code = strtoupper(uniqid('CQT-'));
                $lookup_code = strtoupper(substr(md5(uniqid()), 0, 10));

                $sql = "UPDATE orders SET 
                            invoice_status = 'issued',
                            invoice_date = NOW(),
                            invoice_symbol = '1C24TML',
                            invoice_number = ?,
                            invoice_cqt_code = ?,
                            invoice_lookup_code = ?,
                            invoice_buyer_name = COALESCE(invoice_buyer_name, customer_name),
                            invoice_phone = COALESCE(invoice_phone, phone),
                            invoice_no_receipt = CASE WHEN invoice_tax_code IS NULL OR invoice_tax_code = '' THEN 1 ELSE invoice_no_receipt END
                        WHERE id = ?";
                $update_stmt = $db->prepare($sql);
                $update_stmt->execute([$invoice_number, $cqt_code, $lookup_code, $order_id]);
            }

            echo json_encode(['success' => true, 'message' => 'Đã phát hành ' . count($valid_orders) . ' hóa đơn thành công!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $db = (new Database())->getConnection();

        $keyword = trim($_GET['keyword'] ?? '');
        
        $sql = "SELECT id, order_code, invoice_date, invoice_symbol, invoice_number, invoice_cqt_code, 
                       invoice_buyer_name, invoice_company_name, invoice_tax_code, grand_total, invoice_status
                FROM orders 
                WHERE invoice_status IN ('issued', 'pending_issue')";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (invoice_number LIKE ? OR order_code LIKE ? OR invoice_company_name LIKE ? OR invoice_tax_code LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY invoice_date DESC, created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/invoice/list.php';
    }
}
