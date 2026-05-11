<?php
// Đường dẫn file: app/models/ProductModel.php

class ProductModel
{
    private $conn;
    private $table_name = "products";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hàm lấy danh sách tất cả điện thoại đang có trong hệ thống
    public function getAllProducts()
    {
        $query = "SELECT id, product_name FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addProduct($name, $brand, $price)
    {
        $query = "INSERT INTO " . $this->table_name . " (product_name, brand, base_price) 
                  VALUES (:name, :brand, :price)";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu đầu vào
        $name = htmlspecialchars(strip_tags($name));
        $brand = htmlspecialchars(strip_tags($brand));
        $price = htmlspecialchars(strip_tags($price));

        // Gán tham số
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
