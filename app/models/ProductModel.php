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
    public function addProduct($name, $brand, $price, $sku, $barcode, $unit, $description, $compare_price, $cost_price, $apply_tax, $category, $tags)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_name, brand, base_price, sku, barcode, unit, description, compare_price, cost_price, apply_tax, category, tags) 
                  VALUES (:name, :brand, :price, :sku, :barcode, :unit, :description, :compare_price, :cost_price, :apply_tax, :category, :tags)";

        $stmt = $this->conn->prepare($query);

        // Gán tham số trực tiếp (Bạn có thể thêm htmlspecialchars để bảo mật thêm)
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':compare_price', $compare_price);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':apply_tax', $apply_tax);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':tags', $tags);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
