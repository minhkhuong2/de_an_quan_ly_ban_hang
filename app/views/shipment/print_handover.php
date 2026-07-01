<?php
// Giao diện In phiếu bàn giao vận đơn
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Biên Bản Bàn Giao Vận Đơn</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 30px; font-size: 14px; }
        h1 { text-align: center; margin-bottom: 5px; }
        .meta { text-align: center; margin-bottom: 20px; font-style: italic; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
        .signatures { display: flex; justify-content: space-around; margin-top: 50px; }
        .signatures div { text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">🖨️ In Ngay</button>
    </div>

    <h1>BIÊN BẢN BÀN GIAO VẬN ĐƠN</h1>
    <div class="meta">
        Ngày lập: <?php echo date('d/m/Y H:i'); ?><br>
        Bên giao: AKC Store
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã vận đơn</th>
                <th>Mã đơn hàng</th>
                <th>Người nhận</th>
                <th>Số điện thoại</th>
                <th>Đối tác vận chuyển</th>
                <th>Tiền thu hộ (COD)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $total_cod = 0;
            foreach ($shipments as $s): 
                $total_cod += $s['cod_partner'];
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $i++; ?></td>
                <td><b><?php echo htmlspecialchars($s['waybill_code']); ?></b></td>
                <td><?php echo htmlspecialchars($s['order_code']); ?></td>
                <td><?php echo htmlspecialchars($s['customer_name'] ?: 'Khách lẻ'); ?></td>
                <td><?php echo htmlspecialchars($s['phone']); ?></td>
                <td><?php echo htmlspecialchars($s['shipping_partner_name']); ?></td>
                <td style="text-align: right;"><?php echo number_format($s['cod_partner'], 0, '', '.'); ?> đ</td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="6" style="text-align: right;">TỔNG CỘNG:</th>
                <th style="text-align: right;"><?php echo number_format($total_cod, 0, '', '.'); ?> đ</th>
            </tr>
        </tbody>
    </table>

    <div class="signatures">
        <div>
            <b>Đại diện Cửa hàng</b><br>
            <i>(Ký và ghi rõ họ tên)</i>
        </div>
        <div>
            <b>Đại diện Đơn vị Vận chuyển</b><br>
            <i>(Ký và ghi rõ họ tên)</i>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
