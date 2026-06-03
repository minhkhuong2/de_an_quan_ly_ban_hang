<?php
// Đường dẫn: app/models/SupplierModel.php
class SupplierModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách nhà cung cấp (Có tìm kiếm & Lọc theo ngày tạo)
    public function getAllSuppliers($search = '', $start_date = '', $end_date = '')
    {
        $query = "SELECT * FROM suppliers WHERE 1=1 ";
        $params = [];

        // Lọc theo từ khóa (Tên, Mã NCC, Số điện thoại)
        if (!empty($search)) {
            $query .= " AND (supplier_name LIKE ? OR supplier_code LIKE ? OR phone LIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Lọc theo khoảng thời gian tạo
        if (!empty($start_date)) {
            $query .= " AND DATE(created_at) >= ? ";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $query .= " AND DATE(created_at) <= ? ";
            $params[] = $end_date;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm mới nhà cung cấp
    public function addSupplier($code, $name, $phone, $email, $address, $tax_code)
    {
        $query = "INSERT INTO suppliers (supplier_code, supplier_name, phone, email, address, tax_code) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$code, $name, $phone, $email, $address, $tax_code]);
        $id = $this->conn->lastInsertId();

        // Nếu người dùng để trống mã NCC, hệ thống tự động sinh mã SUP + ID
        if (empty($code)) {
            $newCode = 'SUP' . str_pad($id, 4, '0', STR_PAD_LEFT);
            $this->conn->prepare("UPDATE suppliers SET supplier_code = ? WHERE id = ?")->execute([$newCode, $id]);
        }
        return $id;
    }
    // Lấy thông tin 1 nhà cung cấp theo ID để đổ ra form Sửa
    public function getSupplierById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin Nhà cung cấp
    public function updateSupplier($id, $code, $name, $phone, $email, $address, $tax_code)
    {
        $query = "UPDATE suppliers SET supplier_code = ?, supplier_name = ?, phone = ?, email = ?, address = ?, tax_code = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$code, $name, $phone, $email, $address, $tax_code, $id]);
    }

    // Xóa Nhà cung cấp
    public function deleteSupplier($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
