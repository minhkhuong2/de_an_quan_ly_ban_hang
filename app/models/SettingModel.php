<?php
// Đường dẫn: app/models/SettingModel.php
class SettingModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy toàn bộ cấu hình dưới dạng mảng Key => Value
    public function getAllSettings()
    {
        $stmt = $this->conn->prepare("SELECT setting_key, setting_value FROM settings");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // Cập nhật hoặc Thêm mới một cấu hình
    public function updateSetting($key, $value)
    {
        $stmt = $this->conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        return $stmt->execute([$key, $value, $value]);
    }
}
