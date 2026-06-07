<?php
// Đường dẫn: app/models/ShippingModel.php
class ShippingModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllPartners()
    {
        $stmt = $this->conn->prepare("SELECT * FROM shipping_partners ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPartnerById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM shipping_partners WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addPartner($name, $code, $fee, $cod, $retry, $status, $notes)
    {
        $stmt = $this->conn->prepare("INSERT INTO shipping_partners (partner_name, partner_code, base_fee, allow_cod, max_retry, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $code, $fee, $cod, $retry, $status, $notes]);
    }

    public function updatePartner($id, $name, $code, $fee, $cod, $retry, $status, $notes)
    {
        $stmt = $this->conn->prepare("UPDATE shipping_partners SET partner_name=?, partner_code=?, base_fee=?, allow_cod=?, max_retry=?, status=?, notes=? WHERE id=?");
        return $stmt->execute([$name, $code, $fee, $cod, $retry, $status, $notes, $id]);
    }

    public function deletePartner($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM shipping_partners WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
