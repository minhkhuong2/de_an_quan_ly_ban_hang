<?php
// Đường dẫn: app/models/StaffModel.php
class StaffModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllStaffs()
    {
        $stmt = $this->conn->prepare("SELECT * FROM staffs ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaffById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM staffs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addStaff($last_name, $first_name, $email, $phone, $role)
    {
        $stmt = $this->conn->prepare("INSERT INTO staffs (last_name, first_name, email, phone, role, status) VALUES (?, ?, ?, ?, ?, 'Chờ xác nhận')");
        if ($stmt->execute([$last_name, $first_name, $email, $phone, $role])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateStaff($id, $last_name, $first_name, $phone, $role)
    {
        $stmt = $this->conn->prepare("UPDATE staffs SET last_name=?, first_name=?, phone=?, role=? WHERE id=?");
        return $stmt->execute([$last_name, $first_name, $phone, $role, $id]);
    }

    public function deleteStaff($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM staffs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Hàm dành cho nhân viên bấm vào link để đặt mật khẩu và kích hoạt
    public function activateStaff($id, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE staffs SET password=?, status='Đang kích hoạt' WHERE id=?");
        return $stmt->execute([$hash, $id]);
    }
}
