<?php
// Đường dẫn file: config/database.php

class Database
{
    private $host = "localhost";
    private $db_name = "quanly_imei";
    private $username = "root"; // Mặc định của XAMPP/Laragon
    private $password = "";     // Mặc định thường để trống
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Khởi tạo kết nối PDO
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Thiết lập font chữ UTF-8 để không lỗi tiếng Việt
            $this->conn->exec("set names utf8");
            // Cài đặt chế độ báo lỗi
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
