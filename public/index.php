<?php
// Đường dẫn file: public/index.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Sử dụng hằng số __DIR__ để định vị chính xác file khởi động, không bao giờ lo lỗi đường dẫn
require_once __DIR__ . '/../app/bootstrap.php';

// Khởi tạo bộ định tuyến xử lý hệ thống
$app = new App();
