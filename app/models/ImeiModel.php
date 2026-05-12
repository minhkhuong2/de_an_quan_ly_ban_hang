<?php
// Đường dẫn file: app/models/ImeiModel.php

class ImeiModel
{
    private $conn;
    private $table_name = "product_items";

    // Truyền kết nối database vào Model
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hàm thêm một mã Serial/IMEI mới vào kho
    public function addSingleItem($product_id, $imei_code, $serial_number)
    {
        // Kiểm tra xem Serial đã tồn tại chưa (chống trùng lặp)
        $check_sql = "SELECT id FROM " . $this->table_name . " WHERE serial_number = :serial LIMIT 1";
        $stmt_check = $this->conn->prepare($check_sql);
        $stmt_check->bindParam(':serial', $serial_number);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            return false; // Đã tồn tại mã này
        }

        // Nếu chưa tồn tại thì tiến hành thêm mới
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_id, imei_code, serial_number, status, import_date) 
                  VALUES (:product_id, :imei_code, :serial_number, 'Trong kho', CURDATE())";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $product_id = htmlspecialchars(strip_tags($product_id));
        $imei_code = htmlspecialchars(strip_tags($imei_code));
        $serial_number = htmlspecialchars(strip_tags($serial_number));

        // Gán dữ liệu
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':imei_code', $imei_code);
        $stmt->bindParam(':serial_number', $serial_number);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getAllItems()
    {
        // Viết câu lệnh SQL kết nối 2 bảng để lấy cả Tên sản phẩm và Mã IMEI
        $query = "SELECT product_items.*, products.product_name 
                  FROM " . $this->table_name . " 
                  LEFT JOIN products ON product_items.product_id = products.id
                  ORDER BY product_items.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Trả về tất cả dữ liệu dưới dạng mảng
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Thêm vào ImeiModel.php
    // Hàm cập nhật trạng thái IMEI thành "Đã bán"
    public function sellItem($id, $customer_name, $customer_phone)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'Đã bán', 
                      customer_name = :c_name, 
                      customer_phone = :c_phone 
                  WHERE id = :id AND status = 'Trong kho'";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $customer_name = htmlspecialchars(strip_tags($customer_name));
        $customer_phone = htmlspecialchars(strip_tags($customer_phone));

        // Gán tham số
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':c_name', $customer_name);
        $stmt->bindParam(':c_phone', $customer_phone);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function searchImei($keyword)
    {
        // Tìm kiếm khớp chính xác với IMEI hoặc Serial
        $query = "SELECT product_items.*, products.product_name 
                  FROM " . $this->table_name . " 
                  LEFT JOIN products ON product_items.product_id = products.id
                  WHERE product_items.imei_code = :keyword OR product_items.serial_number = :keyword 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu đầu vào
        $keyword = htmlspecialchars(strip_tags($keyword));
        $stmt->bindParam(':keyword', $keyword);

        $stmt->execute();

        // Trả về 1 mảng dữ liệu duy nhất (nếu tìm thấy)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function receiveWarranty($id)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'Đang bảo hành' 
                  WHERE id = :id AND status = 'Đã bán'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Hàm 2: Sửa xong và trả máy lại cho khách (Chuyển về Đã bán)
    public function returnWarranty($id)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'Đã bán' 
                  WHERE id = :id AND status = 'Đang bảo hành'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public function countByStatus($status)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    public function getImeiForSale($imei_code)
    {
        $query = "SELECT i.*, p.product_name, p.base_price 
                  FROM " . $this->table_name . " i
                  JOIN products p ON i.product_id = p.id
                  WHERE (i.imei_code = :code OR i.serial_number = :code) 
                  AND i.status = 'Trong kho' LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $imei_code);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
