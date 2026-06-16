<?php
// Đường dẫn: app/controllers/ReportController.php
require_once __DIR__ . '/../../config/database.php';

class ReportController
{
    public function end_of_day()
    {
        $db = (new Database())->getConnection();

        // Lấy ngày cần xem báo cáo (Mặc định là hôm nay)
        $date = $_GET['date'] ?? date('Y-m-d');

        // 1. THUẬT TOÁN KẾ TOÁN (Tính toán các chỉ số)
        $sql_metrics = "SELECT 
            COUNT(id) as total_orders,
            -- Tổng tiền hàng (Bao gồm cả đơn hủy/chờ)
            SUM(subtotal) as gross_sales,
            
            -- Tổng giảm giá (Chỉ tính đơn thành công)
            SUM(CASE WHEN order_status != 'cancelled' THEN (total_product_discount + total_order_discount) ELSE 0 END) as total_discount,
            
            -- Thuế (Chỉ tính đơn thành công)
            SUM(CASE WHEN order_status != 'cancelled' THEN tax_amount ELSE 0 END) as total_tax,
            
            -- Tổng doanh thu thực tế (Grand Total của đơn thành công)
            SUM(CASE WHEN order_status != 'cancelled' THEN grand_total ELSE 0 END) as total_revenue
        FROM orders 
        WHERE DATE(created_at) = ?";

        $stmt_metrics = $db->prepare($sql_metrics);
        $stmt_metrics->execute([$date]);
        $metrics = $stmt_metrics->fetch(PDO::FETCH_ASSOC);

        // Xử lý null khi không có đơn nào
        $gross_sales = $metrics['gross_sales'] ?? 0;
        $total_discount = $metrics['total_discount'] ?? 0;
        $total_tax = $metrics['total_tax'] ?? 0;
        $total_revenue = $metrics['total_revenue'] ?? 0;
        $total_orders = $metrics['total_orders'] ?? 0;

        // Công thức: Doanh thu thuần = Tổng tiền hàng (của đơn thành công) - Giảm giá
        $sql_net = "SELECT SUM(subtotal) as valid_gross FROM orders WHERE DATE(created_at) = ? AND order_status != 'cancelled'";
        $stmt_net = $db->prepare($sql_net);
        $stmt_net->execute([$date]);
        $valid_gross = $stmt_net->fetchColumn() ?? 0;

        $net_sales = $valid_gross - $total_discount;

        // 2. LẤY DỮ LIỆU VẼ BIỂU ĐỒ (Nhóm theo giờ trong ngày)
        $sql_chart = "SELECT HOUR(created_at) as hour, SUM(grand_total) as revenue, COUNT(id) as orders 
                      FROM orders 
                      WHERE DATE(created_at) = ? AND order_status != 'cancelled'
                      GROUP BY HOUR(created_at)";
        $stmt_chart = $db->prepare($sql_chart);
        $stmt_chart->execute([$date]);
        $chart_data = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

        // Format mảng 24h cho biểu đồ
        $chart_labels = [];
        $chart_revenues = [];
        $chart_orders = [];
        for ($i = 0; $i <= 23; $i++) {
            $chart_labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $chart_revenues[$i] = 0;
            $chart_orders[$i] = 0;
        }
        foreach ($chart_data as $row) {
            $chart_revenues[(int)$row['hour']] = (float)$row['revenue'];
            $chart_orders[(int)$row['hour']] = (int)$row['orders'];
        }

        // 3. LẤY DANH SÁCH ĐƠN HÀNG TRONG NGÀY
        $sql_orders = "SELECT * FROM orders WHERE DATE(created_at) = ? ORDER BY created_at DESC";
        $stmt_orders = $db->prepare($sql_orders);
        $stmt_orders->execute([$date]);
        $orders_list = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/report/end_of_day.php';
    }
}
