<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Phiếu <?php echo $type == 'shipping' ? 'Giao Hàng' : 'Nhặt Hàng'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .page {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            page-break-after: always;
            padding-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .store-info h2 { margin: 0 0 5px 0; font-size: 18px; }
        .store-info p { margin: 2px 0; font-size: 12px; }
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #000;
            padding: 10px;
            border-radius: 4px;
        }
        .info-box h3 { margin: 0 0 10px 0; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 5px;}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th { background: #f4f4f4; }
        .totals {
            width: 300px;
            float: right;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .totals-row.grand { font-weight: bold; font-size: 16px; border-top: 1px solid #000; padding-top: 10px; }
        .clear { clear: both; }
        
        @media print {
            body { padding: 0; }
            .page { border-bottom: none; margin-bottom: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #0088ff; color: #fff; border: none; cursor: pointer; border-radius: 4px;">🖨️ IN NGAY</button>
    </div>

    <?php foreach ($orders as $o): ?>
        <div class="page">
            <div class="header">
                <div class="store-info">
                    <h2><?php echo htmlspecialchars($store['store_name'] ?? 'Cửa hàng mặc định'); ?></h2>
                    <p>Hotline: <?php echo htmlspecialchars($store['phone'] ?? ''); ?></p>
                    <p>Địa chỉ: <?php echo htmlspecialchars($store['address'] ?? ''); ?></p>
                </div>
                <div style="text-align: right;">
                    <p>Mã đơn: <b><?php echo htmlspecialchars($o['order_code']); ?></b></p>
                    <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></p>
                </div>
            </div>

            <div class="doc-title">
                <h1><?php echo $type == 'shipping' ? 'PHIẾU GIAO HÀNG' : 'PHIẾU NHẶT HÀNG'; ?></h1>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <h3>NGƯỜI NHẬN (KHÁCH HÀNG)</h3>
                    <p><b><?php echo htmlspecialchars($o['customer_name'] ?? 'Khách lẻ'); ?></b></p>
                    <p>SĐT: <?php echo htmlspecialchars($o['customer_phone'] ?? ''); ?></p>
                    <p>Đ/C: <?php echo htmlspecialchars($o['customer_address'] ?? ''); ?></p>
                </div>
                <div class="info-box">
                    <h3>THÔNG TIN GIAO HÀNG</h3>
                    <p>Kênh bán: <?php echo strtoupper($o['sales_channel'] ?? 'POS'); ?></p>
                    <p>Ghi chú: <?php echo htmlspecialchars($o['note'] ?? 'Không có ghi chú'); ?></p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">STT</th>
                        <th>Tên sản phẩm</th>
                        <th>SKU / Mã SP</th>
                        <th style="width: 80px; text-align: center;">Số lượng</th>
                        <?php if ($type == 'shipping'): ?>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: right;">Thành tiền</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $items = $items_by_order[$o['id']] ?? []; 
                        $stt = 1;
                    ?>
                    <?php if(empty($items)): ?>
                        <tr><td colspan="6" style="text-align:center;">Không tìm thấy chi tiết sản phẩm. (Có thể do bảng order_items chưa lưu đúng)</td></tr>
                    <?php else: ?>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $stt++; ?></td>
                            <td><?php echo htmlspecialchars($item['product_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['sku'] ?? ''); ?></td>
                            <td style="text-align: center;"><b><?php echo $item['qty'] ?? 1; ?></b></td>
                            <?php if ($type == 'shipping'): ?>
                            <td style="text-align: right;"><?php echo number_format($item['final_price'] ?? 0, 0, '', '.'); ?>đ</td>
                            <td style="text-align: right;"><?php echo number_format($item['line_total'] ?? 0, 0, '', '.'); ?>đ</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($type == 'shipping'): ?>
            <div class="totals">
                <div class="totals-row">
                    <span>Tổng tiền hàng:</span>
                    <span><?php echo number_format($o['subtotal'] ?? 0, 0, '', '.'); ?>đ</span>
                </div>
                <div class="totals-row">
                    <span>Chiết khấu:</span>
                    <span>-<?php echo number_format(($o['total_product_discount']??0) + ($o['total_order_discount']??0), 0, '', '.'); ?>đ</span>
                </div>
                <div class="totals-row">
                    <span>Phí giao hàng:</span>
                    <span><?php echo number_format(($o['original_shipping_fee']??0) - ($o['total_shipping_discount']??0), 0, '', '.'); ?>đ</span>
                </div>
                <div class="totals-row grand">
                    <span>KHÁCH PHẢI TRẢ (COD):</span>
                    <span><?php 
                        $cod = max(0, floatval($o['grand_total'] ?? 0) - floatval($o['amount_paid'] ?? 0));
                        echo number_format($cod, 0, '', '.'); 
                    ?>đ</span>
                </div>
            </div>
            <div class="clear"></div>
            <?php endif; ?>
            
            <div style="margin-top: 50px; display: flex; justify-content: space-around; text-align: center;">
                <div>
                    <b>Người lập phiếu</b><br>
                    <i style="font-size: 12px; color: #666;">(Ký, ghi rõ họ tên)</i>
                </div>
                <div>
                    <b>Người nhận hàng</b><br>
                    <i style="font-size: 12px; color: #666;">(Ký, ghi rõ họ tên)</i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
