<?php
/**
 * @var array $cashbook_data
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>In Sổ Quỹ - Thông tư 88-2021/TT-BTC</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            color: #000;
            padding: 20px;
            line-height: 1.4;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .title-block {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 13px;
            text-align: left;
        }

        .report-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .footer-sign {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        .footer-sign td {
            text-align: center;
            width: 33%;
            vertical-align: top;
            padding-bottom: 60px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="background: #f4f6f8; padding: 15px; border-radius: 6px; border: 1px solid #dfe3e8; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <b style="color:#212b36; font-size:15px;">🖨️ Xem trước bản in Sổ kế toán tổng hợp (Thông tư 88)</b>
            <p style="margin:5px 0 0 0; font-size:13px; color:#637381;">Hệ thống tự động kết chuyển số dư đầu kỳ và lũy kế thu chi thực tế qua các năm.</p>
        </div>
        <button onclick="window.print()" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">Thực hiện In Sổ quỹ</button>
    </div>

    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <span class="bold">Đơn vị kinh doanh:</span> AAKC STORE ĐIỆN THOẠI DI ĐỘNG<br>
                <span class="bold">Địa chỉ:</span> Chi nhánh trung tâm vận hành hệ thống
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <span class="bold">Mẫu số S1-HKD</span><br>
                (Ban hành kèm theo Thông tư số 88/2021/TT-BTC<br>ngày 11/10/2021 của Bộ Tài chính)
            </td>
        </tr>
    </table>

    <div class="text-center">
        <div class="title-block">SỔ THEO DÕI TÌNH HÌNH THỰC HIỆN NGHĨA VỤ THU CHI VÀ SỔ QUỸ</div>
        <div class="bold" style="font-size:16px;">Loại quỹ hạch toán: <?php echo $_GET['type'] === 'bank' ? 'QUỸ TIỀN GỬI NGÂN HÀNG (SỔ TIỀN GỬI)' : 'QUỸ TIỀN MẶT TẠI CỬA HÀNG'; ?></div>
        <div>Năm hạch toán tài chính: 2026</div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 10%;">Ngày tháng ghi sổ</th>
                <th colspan="2" style="width: 15%;">Chứng từ hạch toán</th>
                <th rowspan="2" style="width: 30%;">Diễn giải nội dung kinh tế</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Thu vào (+)</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Chi ra (-)</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Tồn quỹ lũy kế</th>
            </tr>
            <tr>
                <th>Số hiệu phiếu</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="color:#637381;">---</td>
                <td class="bold" style="color:#637381;">SDDK</td>
                <td class="text-center" style="color:#637381;">---</td>
                <td class="bold">Số dư lũy kế đầu kỳ kết chuyển sang</td>
                <td class="text-right">---</td>
                <td class="text-right">---</td>
                <td class="text-right bold">0 ₫</td>
            </tr>

            <?php
            $running_balance = 0;
            foreach ($cashbook_data as $row):
                if ($row['f_type'] === 'receipt') {
                    $running_balance += $row['amount'];
                } else {
                    $running_balance -= $row['amount'];
                }
            ?>
                <tr>
                    <td class="text-center"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td class="bold" style="color: <?php echo $row['f_type'] === 'receipt' ? '#108043' : '#d82c0d'; ?>;"><?php echo $row['code']; ?></td>
                    <td class="text-center"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <span class="bold">[<?php echo htmlspecialchars($row['reason']); ?>]</span> -
                        Đối tác: <?php echo htmlspecialchars($row['partner']); ?> (Nội dung: <?php echo htmlspecialchars($row['reason']); ?>)
                    </td>
                    <td class="text-right" style="color:#108043; font-weight:600;">
                        <?php echo $row['f_type'] === 'receipt' ? number_format($row['amount'], 0, ',', '.') . ' ₫' : '---'; ?>
                    </td>
                    <td class="text-right" style="color:#d82c0d; font-weight:600;">
                        <?php echo $row['f_type'] === 'expense' ? number_format($row['amount'], 0, ',', '.') . ' ₫' : '---'; ?>
                    </td>
                    <td class="text-right bold">
                        <?php echo number_format($running_balance, 0, ',', '.') . ' ₫'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td></td>
            <td></td>
            <td class="bold">Ngày ..... tháng ..... năm 2026</td>
        </tr>
        <tr class="bold">
            <td>NGƯỜI LẬP BIỂU KHẢO SÁT<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(Ký, ghi rõ họ tên)</span></td>
            <td>KẾ TOÁN TRƯỞNG ĐỐI SOÁT<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(Ký, ghi rõ họ tên)</span></td>
            <td>CHỦ HỘ KINH DOANH / ĐẠI DIỆN<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(Ký, đóng dấu nếu có)</span></td>
        </tr>
    </table>

</body>

</html>
