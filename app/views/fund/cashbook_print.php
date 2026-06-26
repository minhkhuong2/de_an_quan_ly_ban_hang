<?php
/**
 * @var array $cashbook_data
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>In Sá»• Quá»¹ - ThÃ´ng tÆ° 88-2021/TT-BTC</title>
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
            <b style="color:#212b36; font-size:15px;">ðŸ–¨ï¸ Xem trÆ°á»›c báº£n in Sá»• káº¿ toÃ¡n tá»•ng há»£p (ThÃ´ng tÆ° 88)</b>
            <p style="margin:5px 0 0 0; font-size:13px; color:#637381;">Há»‡ thá»‘ng tá»± Ä‘á»™ng káº¿t chuyá»ƒn sá»‘ dÆ° Ä‘áº§u ká»³ vÃ  lÅ©y káº¿ thu chi thá»±c táº¿ qua cÃ¡c nÄƒm.</p>
        </div>
        <button onclick="window.print()" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">Thá»±c hiá»‡n In Sá»• quá»¹</button>
    </div>

    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <span class="bold">ÄÆ¡n vá»‹ kinh doanh:</span> AKC Store ÄIá»†N THOáº I DI Äá»˜NG<br>
                <span class="bold">Äá»‹a chá»‰:</span> Chi nhÃ¡nh trung tÃ¢m váº­n hÃ nh há»‡ thá»‘ng
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <span class="bold">Máº«u sá»‘ S1-HKD</span><br>
                (Ban hÃ nh kÃ¨m theo ThÃ´ng tÆ° sá»‘ 88/2021/TT-BTC<br>ngÃ y 11/10/2021 cá»§a Bá»™ TÃ i chÃ­nh)
            </td>
        </tr>
    </table>

    <div class="text-center">
        <div class="title-block">Sá»” THEO DÃ•I TÃŒNH HÃŒNH THá»°C HIá»†N NGHÄ¨A Vá»¤ THU CHI VÃ€ Sá»” QUá»¸</div>
        <div class="bold" style="font-size:16px;">Loáº¡i quá»¹ háº¡ch toÃ¡n: <?php echo $_GET['type'] === 'bank' ? 'QUá»¸ TIá»€N Gá»¬I NGÃ‚N HÃ€NG (Sá»” TIá»€N Gá»¬I)' : 'QUá»¸ TIá»€N Máº¶T Táº I Cá»¬A HÃ€NG'; ?></div>
        <div>NÄƒm háº¡ch toÃ¡n tÃ i chÃ­nh: 2026</div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 10%;">NgÃ y thÃ¡ng ghi sá»•</th>
                <th colspan="2" style="width: 15%;">Chá»©ng tá»« háº¡ch toÃ¡n</th>
                <th rowspan="2" style="width: 30%;">Diá»…n giáº£i ná»™i dung kinh táº¿</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Thu vÃ o (+)</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Chi ra (-)</th>
                <th rowspan="2" style="width: 15%; text-align: right;">Tá»“n quá»¹ lÅ©y káº¿</th>
            </tr>
            <tr>
                <th>Sá»‘ hiá»‡u phiáº¿u</th>
                <th>NgÃ y táº¡o</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="color:#637381;">---</td>
                <td class="bold" style="color:#637381;">SDDK</td>
                <td class="text-center" style="color:#637381;">---</td>
                <td class="bold">Sá»‘ dÆ° lÅ©y káº¿ Ä‘áº§u ká»³ káº¿t chuyá»ƒn sang</td>
                <td class="text-right">---</td>
                <td class="text-right">---</td>
                <td class="text-right bold">0 â‚«</td>
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
                        Äá»‘i tÃ¡c: <?php echo htmlspecialchars($row['partner']); ?> (Ná»™i dung: <?php echo htmlspecialchars($row['reason']); ?>)
                    </td>
                    <td class="text-right" style="color:#108043; font-weight:600;">
                        <?php echo $row['f_type'] === 'receipt' ? number_format($row['amount'], 0, ',', '.') . ' â‚«' : '---'; ?>
                    </td>
                    <td class="text-right" style="color:#d82c0d; font-weight:600;">
                        <?php echo $row['f_type'] === 'expense' ? number_format($row['amount'], 0, ',', '.') . ' â‚«' : '---'; ?>
                    </td>
                    <td class="text-right bold">
                        <?php echo number_format($running_balance, 0, ',', '.') . ' â‚«'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td></td>
            <td></td>
            <td class="bold">NgÃ y ..... thÃ¡ng ..... nÄƒm 2026</td>
        </tr>
        <tr class="bold">
            <td>NGÆ¯á»œI Láº¬P BIá»‚U KHáº¢O SÃT<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(KÃ½, ghi rÃµ há» tÃªn)</span></td>
            <td>Káº¾ TOÃN TRÆ¯á»žNG Äá»I SOÃT<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(KÃ½, ghi rÃµ há» tÃªn)</span></td>
            <td>CHá»¦ Há»˜ KINH DOANH / Äáº I DIá»†N<br><span style="font-weight:normal; font-size:12px; font-style:italic;">(KÃ½, Ä‘Ã³ng dáº¥u náº¿u cÃ³)</span></td>
        </tr>
    </table>

</body>

</html>

