<?php
// Đường dẫn file: app/bootstrap.php

// Nạp file cấu hình kết nối Database
require_once __DIR__ . '/../config/database.php';

// Nạp file lõi định tuyến chính của hệ thống
require_once __DIR__ . '/core/App.php';
