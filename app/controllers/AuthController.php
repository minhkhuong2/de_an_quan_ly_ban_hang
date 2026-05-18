<?php
// Đường dẫn file: app/controllers/AuthController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{

    // Hàm xử lý trang Đăng nhập
    public function login()
    {
        // Nếu đã đăng nhập rồi thì tự nhảy vào Dashboard luôn
        if (isset($_SESSION['user'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new UserModel($db);

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $userModel->login($username, $password);
            if ($user) {
                // Lưu thông tin vào SESSION để toàn hệ thống nhận diện
                $_SESSION['user'] = $user;
                header("Location: index.php?action=dashboard");
                exit;
            } else {
                $error = "❌ Tài khoản hoặc mật khẩu không chính xác!";
            }
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Hàm xử lý trang Đăng ký
    public function register()
    {
        $message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new UserModel($db);

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $role = $_POST['role'] ?? 'Thu ngân';

            if ($userModel->register($username, $password, $full_name, $role)) {
                $message = "<div style='color:green; font-weight:bold; margin-bottom:15px; padding:10px; background:#eafff0; border:1px solid #33d067; border-radius:4px;'>✅ Đăng ký tài khoản thành công! <a href='index.php?action=login' style='color:#0088ff;'>Đăng nhập ngay</a></div>";
            } else {
                $message = "<div style='color:red; font-weight:bold; margin-bottom:15px; padding:10px; background:#fff1f0; border:1px solid #ffa39e; border-radius:4px;'>❌ Đăng ký thất bại! Tài khoản có thể đã tồn tại.</div>";
            }
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // Hàm xử lý Đăng xuất
    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
