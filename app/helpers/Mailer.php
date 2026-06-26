<?php
// ÄÆ°á»ng dáº«n file: app/helpers/Mailer.php

// 1. NhÃºng cÃ¡c file cá»‘t lÃµi cá»§a PHPMailer vÃ o há»‡ thá»‘ng
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

// Khai bÃ¡o sá»­ dá»¥ng namespace cá»§a PHPMailer
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

        // Láº¥y cáº¥u hÃ¬nh SMTP tá»« Database mÃ  báº¡n Ä‘Ã£ lÆ°u á»Ÿ form Cáº¥u hÃ¬nh chung
        $smtp_host = $settings['smtp_host'] ?? 'smtp.gmail.com';
        $smtp_port = $settings['smtp_port'] ?? '587';
        $smtp_email = $settings['smtp_email'] ?? 'khuongbuivan826@gmail.com';
        $smtp_password = $settings['smtp_password'] ?? 'yaej jcxf rmqw dmub';
        $store_name = $settings['store_name'] ?? 'AKC Store';

        // Náº¿u ngÆ°á»i dÃ¹ng chÆ°a cáº¥u hÃ¬nh Email, ghi log giáº£ láº­p (trÃ¡nh sáº­p web)
        if (empty($smtp_email) || empty($smtp_password)) {
            $logFile = __DIR__ . '/../../public/email_logs.txt';
            $logContent = "Thá»i gian: " . date('Y-m-d H:i:s') . "\n Gá»­i tá»›i: " . $to_email . "\n TiÃªu Ä‘á»: " . $subject . "\n Ná»™i dung: " . strip_tags($html_content) . "\n--------------------\n";
            file_put_contents($logFile, $logContent, FILE_APPEND);
            return true;
        }

        // =========================================================================
        // KÃCH HOáº T PHPMAILER Äá»‚ Gá»¬I EMAIL THáº¬T
        // =========================================================================
        $mail = new PHPMailer(true);

        try {
            // Cáº¥u hÃ¬nh Server (SMTP)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Bá» comment dÃ²ng nÃ y náº¿u muá»‘n xem lá»—i chi tiáº¿t khi gá»­i mail bá»‹ xá»‹t
            $mail->isSMTP();
            $mail->Host       = $smtp_host;                     // VD: smtp.gmail.com
            $mail->SMTPAuth   = true;                           // Báº­t xÃ¡c thá»±c SMTP
            $mail->Username   = $smtp_email;                    // Email gá»­i
            $mail->Password   = $smtp_password;                 // Máº­t kháº©u á»©ng dá»¥ng (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // MÃ£ hÃ³a TLS (khuyÃªn dÃ¹ng cho cá»•ng 587)
            $mail->Port       = $smtp_port;                     // Cá»•ng (ThÆ°á»ng lÃ  587)
            $mail->CharSet    = 'UTF-8';                        // TrÃ¡nh lá»—i font tiáº¿ng Viá»‡t

            // Cáº¥u hÃ¬nh ngÆ°á»i gá»­i vÃ  ngÆ°á»i nháº­n
            $mail->setFrom($smtp_email, $store_name);
            $mail->addAddress($to_email);

            // Ná»™i dung Email
            $mail->isHTML(true);                                // Gá»­i dÆ°á»›i dáº¡ng HTML
            $mail->Subject = $subject;
            $mail->Body    = $html_content;
            $mail->AltBody = strip_tags($html_content);         // Ná»™i dung text thuáº§n cho cÃ¡c mÃ¡y khÃ¡ch khÃ´ng há»— trá»£ HTML

            // Lá»‡nh gá»­i
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Náº¿u lá»—i, ghi vÃ o log há»‡ thá»‘ng Ä‘á»ƒ debug
            error_log("Lá»—i gá»­i mail PHPMailer: {$mail->ErrorInfo}");
            return false;
        }
    }
}

