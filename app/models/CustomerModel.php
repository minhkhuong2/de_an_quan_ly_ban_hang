<?php
// Đường dẫn file: app/models/CustomerModel.php
class CustomerModel
{
    private $conn;
    private $table_name = "customers";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCustomers()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCustomer($name, $phone, $email, $address, $group)
    {
        $query = "INSERT INTO " . $this->table_name . " (customer_name, phone, email, address, customer_group) VALUES (:n, :p, :e, :a, :g)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':n', $name);
        $stmt->bindParam(':p', $phone);
        $stmt->bindParam(':e', $email);
        $stmt->bindParam(':a', $address);
        $stmt->bindParam(':g', $group);
        return $stmt->execute();
    }

    public function deleteCustomer($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
