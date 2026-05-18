<?php
// Đường dẫn file: app/models/UserModel.php

class UserModel
{
    private $conn;
    private $table_name = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hàm xử lý Đăng ký tài khoản mới
    public function register($username, $password, $full_name, $role)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)";
        $stmt = $this->conn->prepare($query);

        // Mã hóa mật khẩu bằng MD5 để nộp đồ án cho nhanh và an toàn
        $hashed_password = md5($password);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Hàm xử lý Đăng nhập
    public function login($username, $password)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username AND password = :password LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $hashed_password = md5($password);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về thông tin user nếu đúng tài khoản
    }
}
