<?php
// Đường dẫn file: app/models/OrderModel.php

class OrderModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hàm tạo hóa đơn với kỹ thuật Transaction
    public function createOrder($customerName, $customerPhone, $totalAmount, $cartItems)
    {
        try {
            // Bắt đầu giao dịch (Nếu có lỗi ở bất kỳ đâu, hệ thống sẽ Rollback)
            $this->conn->beginTransaction();

            // 1. Lưu vào bảng orders
            $queryOrder = "INSERT INTO orders (customer_name, customer_phone, total_amount) VALUES (:cname, :cphone, :total)";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([
                ':cname' => $customerName,
                ':cphone' => $customerPhone,
                ':total' => $totalAmount
            ]);
            $orderId = $this->conn->lastInsertId(); // Lấy ID hóa đơn vừa tạo

            // 2. Vòng lặp lưu từng máy vào order_details và cập nhật trạng thái kho
            $queryDetail = "INSERT INTO order_details (order_id, imei_code, price) VALUES (:oid, :imei, :price)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            $queryUpdateImei = "UPDATE product_items SET status = 'Đã bán', customer_name = :cname, customer_phone = :cphone WHERE imei_code = :imei";
            $stmtUpdateImei = $this->conn->prepare($queryUpdateImei);

            foreach ($cartItems as $item) {
                // Lưu chi tiết
                $stmtDetail->execute([
                    ':oid' => $orderId,
                    ':imei' => $item['imei_code'],
                    ':price' => $item['base_price']
                ]);

                // Đổi trạng thái IMEI sang "Đã bán"
                $stmtUpdateImei->execute([
                    ':cname' => $customerName,
                    ':cphone' => $customerPhone,
                    ':imei' => $item['imei_code']
                ]);
            }

            // Lưu thành công tất cả thì Commit (Xác nhận)
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Nếu có lỗi, hoàn tác mọi thứ
            $this->conn->rollBack();
            return false;
        }
    }
}
