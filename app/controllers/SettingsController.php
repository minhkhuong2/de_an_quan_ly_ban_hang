<?php

class SettingsController
{
    public function checkout()
    {
        // Mock data
        $checkout_settings = [
            'auto_email_reminder' => '1h' // 'never', '1h', '6h', '10h', '24h'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $checkout_settings['auto_email_reminder'] = $_POST['auto_email_reminder'] ?? 'never';
            $success_message = "Lưu cấu hình gửi email nhắc nhở thành công!";
        }

        require_once __DIR__ . '/../views/settings/checkout.php';
    }
}
