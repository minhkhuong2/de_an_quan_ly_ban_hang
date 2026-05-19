<?php
// Đường dẫn file: app/models/SupplierModel.php
class SupplierModel
{
    private $conn;
    private $table_name = "suppliers";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllSuppliers()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSupplier($name, $phone, $email, $address)
    {
        $query = "INSERT INTO " . $this->table_name . " (supplier_name, phone, email, address) VALUES (:n, :p, :e, :a)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':p', $phone);
        $stmt->bindParam(':e', $email);
        $stmt->bindParam(':a', $address);
        return $stmt->execute();
    }

    public function deleteSupplier($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
