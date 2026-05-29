<?php
// Đường dẫn: app/controllers/InventoryController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/InventoryModel.php';

class InventoryController
{
    public function list()
    {
        $db = (new Database())->getConnection();
        $inventoryModel = new InventoryModel($db);

        // Hứng các tham số bộ lọc từ URL
        $filters = [
            'search'    => $_GET['search'] ?? '',
            'stock_min' => $_GET['stock_min'] ?? '',
            'stock_max' => $_GET['stock_max'] ?? ''
        ];

        // Lấy danh sách kho
        $inventoryList = $inventoryModel->getInventoryList($filters);

        // Gọi ra giao diện
        require_once __DIR__ . '/../views/inventory/list.php';
    }
}
