<?php
// Đường dẫn: app/controllers/TaxSettingController.php
require_once __DIR__ . '/../../config/database.php';

class TaxSettingController
{
    // 1. GIAO DIỆN CẤU HÌNH THUẾ
    public function index()
    {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM tax_settings WHERE id = 1");
        $tax = $stmt->fetch(PDO::FETCH_ASSOC);

        // Đề phòng chưa có dữ liệu
        if (!$tax) {
            $db->query("INSERT INTO tax_settings (id) VALUES (1)");
            $stmt = $db->query("SELECT * FROM tax_settings WHERE id = 1");
            $tax = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../views/settings/tax_settings.php';
    }

    // 2. LƯU CẬP NHẬT CẤU HÌNH
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $is_tax_enabled = isset($_POST['is_tax_enabled']) ? 1 : 0;
            $default_tax_sales = isset($_POST['default_tax_sales']) ? 1 : 0;
            $default_tax_purchases = isset($_POST['default_tax_purchases']) ? 1 : 0;
            $price_includes_tax = isset($_POST['price_includes_tax']) ? 1 : 0;
            $tax_on_shipping = isset($_POST['tax_on_shipping']) ? 1 : 0;

            // Xử lý số thập phân
            $general_purchase_tax_rate = floatval($_POST['general_purchase_tax_rate'] ?? 0);
            $general_sales_tax_rate = floatval($_POST['general_sales_tax_rate'] ?? 0);
            $shipping_tax_rate = floatval($_POST['shipping_tax_rate'] ?? 0);

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("
                UPDATE tax_settings SET 
                    is_tax_enabled = ?, default_tax_sales = ?, default_tax_purchases = ?, 
                    price_includes_tax = ?, tax_on_shipping = ?, general_purchase_tax_rate = ?, 
                    general_sales_tax_rate = ?, shipping_tax_rate = ? 
                WHERE id = 1
            ");
            $stmt->execute([
                $is_tax_enabled,
                $default_tax_sales,
                $default_tax_purchases,
                $price_includes_tax,
                $tax_on_shipping,
                $general_purchase_tax_rate,
                $general_sales_tax_rate,
                $shipping_tax_rate
            ]);

            header("Location: index.php?action=tax_settings&success=1");
            exit;
        }
    }
}
