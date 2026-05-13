<?php
// Đường dẫn: app/models/PriceModel.php
class PriceModel
{
    private $conn;
    private $table_name = "price_lists";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllPrices()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPriceById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addPrice($name, $type, $value, $auto_add, $branch, $status)
    {
        $query = "INSERT INTO " . $this->table_name . " (price_name, adjust_type, adjust_value, auto_add, branch, status) VALUES (:name, :type, :value, :auto_add, :branch, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':auto_add', $auto_add);
        $stmt->bindParam(':branch', $branch);
        $stmt->bindParam(':status', $status);
        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function deletePrice($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
