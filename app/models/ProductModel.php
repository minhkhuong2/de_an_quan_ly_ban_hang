<?php
// Đường dẫn: app/models/ProductModel.php
class ProductModel
{
    private $conn;
    private $table_name = "products";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllProducts()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsWithStock()
    {
        // ĐÃ NÂNG CẤP: Đọc trực tiếp dữ liệu "Tồn kho" (stock) và "Có thể bán" (available) từ Module Kho
        $query = "SELECT p.*, p.stock as ton_kho, p.available as co_the_ban FROM " . $this->table_name . " p ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $brand, $price, $sku, $barcode, $unit, $description, $image, $compare_price, $cost_price, $apply_tax, $category, $tags)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_name, brand, base_price, sku, barcode, unit, description, image, compare_price, cost_price, apply_tax, category, tags) 
                  VALUES (:name, :brand, :price, :sku, :barcode, :unit, :description, :image, :compare_price, :cost_price, :apply_tax, :category, :tags)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':compare_price', $compare_price);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':apply_tax', $apply_tax);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':tags', $tags);

        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function updateProduct($id, $name, $brand, $price, $sku, $barcode, $unit, $description, $image, $compare_price, $cost_price, $apply_tax, $category, $tags)
    {
        $query = "UPDATE " . $this->table_name . " SET product_name=:name, brand=:brand, base_price=:price, sku=:sku, barcode=:barcode, unit=:unit, description=:description, image=:image, compare_price=:compare_price, cost_price=:cost_price, apply_tax=:apply_tax, category=:category, tags=:tags WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':compare_price', $compare_price);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':apply_tax', $apply_tax);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':tags', $tags);
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
