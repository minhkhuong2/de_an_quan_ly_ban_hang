<?php
// Đường dẫn: app/models/CustomerModel.php
class CustomerModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCustomers($search = '')
    {
        $query = "SELECT * FROM customers WHERE 1=1 ";
        $params = [];
        if (!empty($search)) {
            $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR customer_code LIKE ?) ";
            $params = array_fill(0, 4, "%$search%");
        }
        $query .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCustomer($code, $last_name, $first_name, $phone, $email, $marketing, $province, $district, $ward, $address, $tax_code, $company, $inv_address, $inv_email, $notes, $tags)
    {
        $query = "INSERT INTO customers (customer_code, last_name, first_name, phone, email, accept_marketing, province, district, ward, address, tax_code, company_name, invoice_address, invoice_email, notes, tags) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$code, $last_name, $first_name, $phone, $email, $marketing, $province, $district, $ward, $address, $tax_code, $company, $inv_address, $inv_email, $notes, $tags]);
        $id = $this->conn->lastInsertId();

        // Tự sinh mã KH nếu để trống
        if (empty($code)) {
            $newCode = 'KH' . str_pad($id, 5, '0', STR_PAD_LEFT);
            $this->conn->prepare("UPDATE customers SET customer_code = ? WHERE id = ?")->execute([$newCode, $id]);
        }
        return $id;
    }

    public function updateCustomer($id, $code, $last_name, $first_name, $phone, $email, $marketing, $province, $district, $ward, $address, $tax_code, $company, $inv_address, $inv_email, $notes, $tags)
    {
        $query = "UPDATE customers SET customer_code=?, last_name=?, first_name=?, phone=?, email=?, accept_marketing=?, province=?, district=?, ward=?, address=?, tax_code=?, company_name=?, invoice_address=?, invoice_email=?, notes=?, tags=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$code, $last_name, $first_name, $phone, $email, $marketing, $province, $district, $ward, $address, $tax_code, $company, $inv_address, $inv_email, $notes, $tags, $id]);
    }

    public function deleteCustomer($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateDebt($customer_id, $amount)
    {
        $stmt = $this->conn->prepare("UPDATE customers SET debt = debt + ? WHERE id = ?");
        return $stmt->execute([$amount, $customer_id]);
    }
}
