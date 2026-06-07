<?php
class BranchModel
{
    private $conn;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllBranches()
    {
        $stmt = $this->conn->prepare("SELECT * FROM branches ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranchById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBranch($name, $phone, $address, $status)
    {
        $stmt = $this->conn->prepare("INSERT INTO branches (branch_name, phone, address, status) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $phone, $address, $status]);
    }

    public function updateBranch($id, $name, $phone, $address, $status)
    {
        $stmt = $this->conn->prepare("UPDATE branches SET branch_name=?, phone=?, address=?, status=? WHERE id=?");
        return $stmt->execute([$name, $phone, $address, $status, $id]);
    }

    public function deleteBranch($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM branches WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
