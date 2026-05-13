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

    public function addCategory($name, $desc, $alias, $seo_title, $seo_desc, $status, $sel_type, $match_type, $auto_rules, $sort_order)
    {
        $query = "INSERT INTO " . $this->table_name . " (category_name, description, alias, seo_title, seo_description, status, selection_type, match_type, auto_rules, sort_order) VALUES (:n, :d, :a, :st, :sd, :stat, :selt, :mt, :ar, :so)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':d', $desc);
        $stmt->bindParam(':a', $alias);
        $stmt->bindParam(':st', $seo_title);
        $stmt->bindParam(':sd', $seo_desc);
        $stmt->bindParam(':stat', $status);
        $stmt->bindParam(':selt', $sel_type);
        $stmt->bindParam(':mt', $match_type);
        $stmt->bindParam(':ar', $auto_rules);
        $stmt->bindParam(':so', $sort_order);
        if ($stmt->execute()) return $this->conn->lastInsertId();
        return false;
    }

    public function updateCategory($id, $name, $desc, $alias, $seo_title, $seo_desc, $status, $sel_type, $match_type, $auto_rules, $sort_order)
    {
        $query = "UPDATE " . $this->table_name . " SET category_name=:n, description=:d, alias=:a, seo_title=:st, seo_description=:sd, status=:stat, selection_type=:selt, match_type=:mt, auto_rules=:ar, sort_order=:so WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':d', $desc);
        $stmt->bindParam(':a', $alias);
        $stmt->bindParam(':st', $seo_title);
        $stmt->bindParam(':sd', $seo_desc);
        $stmt->bindParam(':stat', $status);
        $stmt->bindParam(':selt', $sel_type);
        $stmt->bindParam(':mt', $match_type);
        $stmt->bindParam(':ar', $auto_rules);
        $stmt->bindParam(':so', $sort_order);
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
