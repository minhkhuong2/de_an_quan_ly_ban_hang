<?php
// Đường dẫn file: app/controllers/DashboardController.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ImeiModel.php';

class DashboardController
{
    public function index()
    {
        $database = new Database();
        $db = $database->getConnection();
        $imeiModel = new ImeiModel($db);

        // Gọi Model để đếm số lượng thực tế trong CSDL
        $inStock = $imeiModel->countByStatus('Trong kho');
        $sold = $imeiModel->countByStatus('Đã bán');
        $warranty = $imeiModel->countByStatus('Đang bảo hành');

        // Truyền 3 biến này ra View
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
