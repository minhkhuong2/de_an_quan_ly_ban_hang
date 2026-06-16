<?php require_once __DIR__ . '/../layout/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 22px;
        font-weight: bold;
        color: #212b36;
    }

    /* Lưới Card Thống kê */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .metric-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
    }

    .metric-title {
        font-size: 14px;
        color: #637381;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metric-value {
        font-size: 24px;
        font-weight: bold;
        color: #212b36;
    }

    .metric-tooltip {
        font-size: 12px;
        color: #8c98a4;
        margin-top: 5px;
    }

    .chart-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
    }

    .v3-table th {
        background: #f4f6f8;
        text-align: left;
        padding: 12px 20px;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #dfe3e8;
    }

    .v3-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }
</style>

<div class="v3-header">
    <div class="v3-title">📊 Báo cáo cuối ngày</div>
    <form method="GET" action="index.php" style="display: flex; gap: 10px;">
        <input type="hidden" name="action" value="end_of_day_report">
        <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; cursor: pointer;">

        <button type="submit" style="display: none;">Xem báo cáo</button>
    </form>
</div>

<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-title">Tổng tiền hàng <span>🛒</span></div>
        <div class="metric-value"><?php echo number_format($gross_sales, 0, ',', '.'); ?> ₫</div>
        <div class="metric-tooltip">Bao gồm cả đơn Hủy/Chưa thanh toán</div>
    </div>
    <div class="metric-card">
        <div class="metric-title" style="color: #0088ff;">Doanh thu thuần <span>📈</span></div>
        <div class="metric-value" style="color: #0088ff;"><?php echo number_format($net_sales, 0, ',', '.'); ?> ₫</div>
        <div class="metric-tooltip">= Tổng tiền hàng - Giảm giá - Trả hàng</div>
    </div>
    <div class="metric-card">
        <div class="metric-title" style="color: #108043;">Tổng doanh thu <span>💰</span></div>
        <div class="metric-value" style="color: #108043;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> ₫</div>
        <div class="metric-tooltip">= Doanh thu thuần + Thuế (VAT)</div>
    </div>
</div>

<div class="chart-container">
    <h3 style="font-size: 16px; color: #212b36; margin-bottom: 20px;">Biểu đồ Doanh thu & Đơn hàng ngày <?php echo date('d/m/Y', strtotime($date)); ?></h3>
    <canvas id="revenueChart" height="80"></canvas>
</div>

<div class="chart-container" style="padding: 0;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #dfe3e8; font-weight: 600;">Chi tiết <?php echo $total_orders; ?> đơn hàng</div>
    <table class="v3-table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th style="text-align: right;">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders_list)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #8c98a4;">Không có giao dịch nào trong ngày.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders_list as $o): ?>
                    <tr>
                        <td style="color: #0088ff; font-weight: 600;">#<?php echo $o['order_code']; ?></td>
                        <td><?php echo date('H:i', strtotime($o['created_at'])); ?></td>
                        <td>
                            <?php if ($o['order_status'] == 'cancelled') echo '<span style="color:#d82c0d; background:#fff1f0; padding:2px 8px; border-radius:4px; font-size:12px;">Đã hủy</span>';
                            else echo '<span style="color:#108043; background:#eafff0; padding:2px 8px; border-radius:4px; font-size:12px;">Thành công</span>'; ?>
                        </td>
                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($o['grand_total'], 0, ',', '.'); ?> ₫</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line', // Biểu đồ dạng đường
        data: {
            labels: <?php echo json_encode($chart_labels); ?>, // Mốc thời gian 24h
            datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?php echo json_encode(array_values($chart_revenues)); ?>,
                    borderColor: '#0088ff',
                    backgroundColor: 'rgba(0, 136, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4, // Làm cong đường vẽ cho đẹp
                    yAxisID: 'y'
                },
                {
                    label: 'Số đơn hàng',
                    data: <?php echo json_encode(array_values($chart_orders)); ?>,
                    type: 'bar', // Biểu đồ cột lồng vào biểu đồ đường
                    backgroundColor: '#ffea8a',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Doanh thu'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Số đơn'
                    }
                }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
