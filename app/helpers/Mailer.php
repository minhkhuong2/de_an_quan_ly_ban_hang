<?php
// Đường dẫn file: app/helpers/Mailer.php

// 1. Nhúng các file cốt lõi của PHPMailer vào hệ thống
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

// Khai báo sử dụng namespace của PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SettingModel.php';

class Mailer
{

    public static function sendEmail($to_email, $subject, $html_content)
    {
        $db = (new Database())->getConnection();
        $settings = (new SettingModel($db))->getAllSettings();

        // Lấy cấu hình SMTP từ Database mà bạn đã lưu ở form Cấu hình chung
        $smtp_host = $settings['smtp_host'] ?? 'smtp.gmail.com';
        $smtp_port = $settings['smtp_port'] ?? '587';
        $smtp_email = $settings['smtp_email'] ?? 'khuongbuivan826@gmail.com';
        $smtp_password = $settings['smtp_password'] ?? 'yaej jcxf rmqw dmub';
        $store_name = $settings['store_name'] ?? 'Sapo Store';

        // Nếu người dùng chưa cấu hình Email, ghi log giả lập (tránh sập web)
        if (empty($smtp_email) || empty($smtp_password)) {
            $logFile = __DIR__ . '/../../public/email_logs.txt';
            $logContent = "Thời gian: " . date('Y-m-d H:i:s') . "\n Gửi tới: " . $to_email . "\n Tiêu đề: " . $subject . "\n Nội dung: " . strip_tags($html_content) . "\n--------------------\n";
            file_put_contents($logFile, $logContent, FILE_APPEND);
            return true;
        }

        // =========================================================================
        // KÍCH HOẠT PHPMAILER ĐỂ GỬI EMAIL THẬT
        // =========================================================================
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server (SMTP)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Bỏ comment dòng này nếu muốn xem lỗi chi tiết khi gửi mail bị xịt
            $mail->isSMTP();
            $mail->Host       = $smtp_host;                     // VD: smtp.gmail.com
            $mail->SMTPAuth   = true;                           // Bật xác thực SMTP
            $mail->Username   = $smtp_email;                    // Email gửi
            $mail->Password   = $smtp_password;                 // Mật khẩu ứng dụng (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Mã hóa TLS (khuyên dùng cho cổng 587)
            $mail->Port       = $smtp_port;                     // Cổng (Thường là 587)
            $mail->CharSet    = 'UTF-8';                        // Tránh lỗi font tiếng Việt

            // Cấu hình người gửi và người nhận
            $mail->setFrom($smtp_email, $store_name);
            $mail->addAddress($to_email);

            // Nội dung Email
            $mail->isHTML(true);                                // Gửi dưới dạng HTML
            $mail->Subject = $subject;
            $mail->Body    = $html_content;
            $mail->AltBody = strip_tags($html_content);         // Nội dung text thuần cho các máy khách không hỗ trợ HTML

            // Lệnh gửi
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Nếu lỗi, ghi vào log hệ thống để debug
            error_log("Lỗi gửi mail PHPMailer: {$mail->ErrorInfo}");
            return false;
        }
    }
}
