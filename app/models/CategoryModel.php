<?php
// Đường dẫn file: app/models/CategoryModel.php
class CategoryModel
{
    private $conn;
    private $table_name = "categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCategories()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name, $description, $status)
    {
        $query = "INSERT INTO " . $this->table_name . " (category_name, description, status) VALUES (:name, :description, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function updateCategory($id, $name, $description, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET category_name=:name, description=:description, status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function deleteCategory($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
