<?php
// Giao diện In phiếu giao hàng đơn giản
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Phiếu Giao Hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; font-size: 14px; }
        .label-container { width: 400px; border: 2px dashed #000; padding: 15px; margin-bottom: 20px; page-break-inside: avoid; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .barcode { text-align: center; margin: 15px 0; }
        .barcode img { max-width: 100%; height: 60px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-col { flex: 1; }
        .footer { border-top: 2px solid #000; padding-top: 10px; font-size: 16px; font-weight: bold; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">🖨️ In Ngay</button>
    </div>

    <?php foreach ($shipments as $s): ?>
    <div class="label-container">
        <div class="header">
            <h2><?php echo htmlspecialchars($s['shipping_partner_name']); ?></h2>
            <div>Mã đơn: <b><?php echo htmlspecialchars($s['order_code']); ?></b></div>
        </div>
        
        <div class="barcode">
            <!-- Giả lập mã vạch -->
            <div style="background: repeating-linear-gradient(90deg, #000, #000 2px, #fff 2px, #fff 4px); height: 50px; margin-bottom: 5px;"></div>
            <b><?php echo htmlspecialchars($s['waybill_code']); ?></b>
        </div>
        
        <div class="info-row">
            <div class="info-col">
                <b>Từ:</b><br>
                AKC Store<br>
                0987654321
            </div>
            <div class="info-col">
                <b>Đến:</b><br>
                <?php echo htmlspecialchars($s['customer_name'] ?: 'Khách lẻ'); ?><br>
                <?php echo htmlspecialchars($s['phone']); ?><br>
                <?php echo htmlspecialchars($s['address'] ?? ''); ?>
            </div>
        </div>
        
        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px; font-size: 12px;">
            <b>Ghi chú:</b> Cho xem hàng, không thử
        </div>
        
        <div class="footer">
            Thu hộ (COD): <?php echo number_format($s['cod_partner'], 0, '', '.'); ?> đ
        </div>
    </div>
    <?php endforeach; ?>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
