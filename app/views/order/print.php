<?php

/** @var array $order */
/** @var array $items */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn <?php echo $order['order_code']; ?></title>
    <style>
        /* CSS CHUYÊN DỤNG CHO MÁY IN NHIỆT KHỔ 80mm */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            margin: 0;
            padding: 0;
            color: #000;
            background: #ececec;
        }

        .ticket {
            width: 80mm;
            max-width: 80mm;
            margin: 20px auto;
            padding: 15px;
            background: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        th,
        td {
            border-bottom: 1px dashed #000;
            padding: 6px 0;
            vertical-align: top;
        }

        /* Ẩn các nút bấm khi máy in chạy */
        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 0;
                background: #fff;
            }

            .ticket {
                margin: 0;
                box-shadow: none;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print text-center" style="margin-bottom: 20px; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; font-weight:bold; background:#0088ff; color:#fff; border:none; border-radius:4px; cursor: pointer;">🖨️ IN HÓA ĐƠN NÀY</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; border:1px solid #c4cdd5; background:#fff; border-radius:4px; cursor: pointer;">Đóng lại</button>
    </div>

    <div class="ticket">
        <div class="text-center">
            <h2 style="margin: 0; font-size: 18px;">CỬA HÀNG ĐIỆN THOẠI</h2>
            <p style="margin: 5px 0;">ĐC: Đại học Kỹ Thuật, Hà Nội</p>
            <p style="margin: 5px 0;">SĐT: 0988.888.888</p>
            <h3 style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 8px 0; margin-top: 15px; font-size: 16px;">HÓA ĐƠN BÁN HÀNG</h3>
        </div>

        <div style="margin-bottom: 10px;">
            <p style="margin: 3px 0;">Mã đơn: <b><?php echo $order['order_code']; ?></b></p>
            <p style="margin: 3px 0;">Ngày: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p style="margin: 3px 0;">Khách hàng: <?php echo !empty($order['customer_name']) ? $order['customer_name'] : 'Khách lẻ'; ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: left; width: 50%;">Sản phẩm</th>
                    <th class="text-center" style="width: 15%;">SL</th>
                    <th class="text-right" style="width: 35%;">T.Tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item['product_name']; ?>
                            <?php if ($item['is_gift'] == 1) echo "<br><i>(Quà tặng)</i>"; ?>
                        </td>
                        <td class="text-center"><?php echo $item['qty']; ?></td>
                        <td class="text-right"><?php echo number_format($item['line_total'], 0, '', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Tổng tiền hàng:</span>
                <span><?php echo number_format($order['subtotal'], 0, '', '.'); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Khuyến mại:</span>
                <span>- <?php echo number_format($order['total_product_discount'] + $order['total_order_discount'], 0, '', '.'); ?></span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 16px; margin-top: 10px;" class="font-bold">
            <span>TỔNG CỘNG:</span>
            <span><?php echo number_format($order['grand_total'], 0, '', '.'); ?>đ</span>
        </div>

        <div class="text-center" style="margin-top: 25px; font-style: italic;">
            <p>Cảm ơn quý khách và hẹn gặp lại!</p>
            <p>***</p>
            <p style="font-size: 10px; margin-top: 10px;">Cung cấp bởi Hệ thống </p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
