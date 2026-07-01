<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Hàng Loạt Đơn Hàng</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 13px; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .print-container { max-width: 800px; margin: 0 auto; padding: 20px; page-break-after: always; }
        .print-container:last-child { page-break-after: auto; }
        h1 { text-align: center; font-size: 20px; text-transform: uppercase; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .order-info { margin-top: 20px; display: flex; justify-content: space-between; border-bottom: 1px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { text-align: center; background: #f0f0f0; }
        .text-right { text-align: right; }
        .totals-table { width: 50%; float: right; margin-top: 10px; border: none; }
        .totals-table td { border: none; padding: 4px 8px; }
        .footer { margin-top: 150px; text-align: center; font-size: 12px; clear: both; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .print-container { padding: 10px; width: 100%; height: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body onload="window.print()">
    <?php foreach ($orders as $order): ?>
    <?php $items = $order['items'] ?? []; ?>
    <div class="print-container">
        <h1>HÓA ĐƠN BÁN HÀNG</h1>
        <div class="text-center">Ngày: <?php echo date('d/m/Y H:i'); ?></div>
        
        <div class="order-info">
            <div>
                <strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? 'Khách lẻ'); ?><br>
                <strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?? '---'); ?><br>
                <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address'] ?? '---'); ?>
            </div>
            <div class="text-right">
                <strong>Mã ĐH:</strong> <?php echo htmlspecialchars($order['order_code'] ?? $order['id']); ?><br>
                <strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?><br>
                <strong>Nhân viên:</strong> <?php echo $order['assigned_staff_id'] ? '#' . $order['assigned_staff_id'] : '---'; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%">STT</th>
                    <th style="width: 45%">Tên sản phẩm</th>
                    <th style="width: 10%">SL</th>
                    <th style="width: 20%">Đơn giá</th>
                    <th style="width: 20%">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; foreach ($items as $item): ?>
                <tr>
                    <td class="text-center"><?php echo $stt++; ?></td>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?>
                        <?php if(!empty($item['sku'])): ?> (<?php echo htmlspecialchars($item['sku']); ?>)<?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $item['qty']; ?></td>
                    <td class="text-right"><?php echo number_format($item['final_price'], 0, '', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($item['line_total'], 0, '', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Cộng tiền hàng:</td>
                <td class="text-right"><?php echo number_format($order['subtotal'] ?? 0, 0, '', '.'); ?> đ</td>
            </tr>
            <?php 
            $discount = ($order['total_product_discount'] ?? 0) + ($order['total_order_discount'] ?? 0);
            if ($discount > 0): 
            ?>
            <tr>
                <td>Chiết khấu:</td>
                <td class="text-right">-<?php echo number_format($discount, 0, '', '.'); ?> đ</td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Phí vận chuyển:</td>
                <td class="text-right"><?php echo number_format(($order['original_shipping_fee'] ?? 0) - ($order['total_shipping_discount'] ?? 0), 0, '', '.'); ?> đ</td>
            </tr>
            <tr>
                <td><strong>Tổng cộng:</strong></td>
                <td class="text-right"><strong><?php echo number_format($order['grand_total'] ?? 0, 0, '', '.'); ?> đ</strong></td>
            </tr>
        </table>

        <div class="footer">
            <p><strong>Xin cảm ơn quý khách! Hẹn gặp lại!</strong></p>
        </div>
    </div>
    <?php endforeach; ?>
</body>
</html>
