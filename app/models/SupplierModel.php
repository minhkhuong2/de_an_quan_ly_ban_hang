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

    public function getSupplierById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addSupplier($name, $code, $phone, $email, $group, $address, $fax, $tax, $web, $debt, $assignee, $desc, $tags, $tax_set, $import_price, $status)
    {
        if (empty($code)) {
            $code = 'SUP' . time();
        }
        $query = "INSERT INTO " . $this->table_name . " 
        (supplier_name, supplier_code, phone, email, supplier_group, address, fax, tax_code, website, debt, assignee, description, tags, tax_setting, default_import_price, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $code, $phone, $email, $group, $address, $fax, $tax, $web, $debt, $assignee, $desc, $tags, $tax_set, $import_price, $status]);
    }

    public function updateSupplier($id, $name, $code, $phone, $email, $group, $address, $fax, $tax, $web, $debt, $assignee, $desc, $tags, $tax_set, $import_price, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET 
            supplier_name = ?, supplier_code = ?, phone = ?, email = ?, supplier_group = ?, 
            address = ?, fax = ?, tax_code = ?, website = ?, debt = ?, 
            assignee = ?, description = ?, tags = ?, tax_setting = ?, 
            default_import_price = ?, status = ? 
            WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $code, $phone, $email, $group, $address, $fax, $tax, $web, $debt, $assignee, $desc, $tags, $tax_set, $import_price, $status, $id]);
    }

    public function deleteSupplier($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
