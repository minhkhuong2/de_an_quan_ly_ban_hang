<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tra cứu bảo hành thiết bị</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
            background: #f4f6f8;
        }

        .search-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        input[type="text"] {
            width: 70%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .result-card {
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
            text-align: left;
        }

        /* CSS cho phần Lời chào */
        .greeting {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
    </style>
</head>

<body>

    <div class="search-box">
        <h2>Tra cứu thông tin bảo hành</h2>
        <p>Nhập mã IMEI hoặc Số Serial in trên thiết bị của bạn</p>

        <form action="index.php" method="GET">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="VD: 123456789..." required>
            <button type="submit">Tra cứu ngay</button>
        </form>

        <?php if (isset($_GET['keyword'])): ?>
            <div class="result-card">
                <?php if ($result): ?>

                    <?php if (!empty($result['customer_name'])): ?>
                        <div class="greeting">
                            <h4 style="margin: 0 0 5px 0;">👋 Xin chào anh/chị: <strong><?php echo htmlspecialchars($result['customer_name']); ?></strong></h4>
                            <?php
                            // Thuật toán che số điện thoại để bảo mật
                            $phone = $result['customer_phone'];
                            // Lấy 3 số đầu + **** + 3 số cuối
                            $masked_phone = substr($phone, 0, 3) . '****' . substr($phone, -3);
                            ?>
                            <span style="font-size: 14px;">Số điện thoại bảo hành: <strong><?php echo htmlspecialchars($masked_phone); ?></strong></span>
                        </div>
                    <?php endif; ?>

                    <h3 style="color: green; margin-top: 0;">✅ Thông tin thiết bị</h3>
                    <p><strong>Tên thiết bị:</strong> <?php echo $result['product_name']; ?></p>
                    <p><strong>Mã IMEI:</strong> <?php echo $result['imei_code']; ?></p>
                    <p><strong>Số Serial:</strong> <?php echo $result['serial_number']; ?></p>
                    <p><strong>Tình trạng:</strong>
                        <?php
                        if ($result['status'] == 'Đã bán') {
                            echo '<span style="color:blue; font-weight:bold;">Đã kích hoạt bảo hành</span>';
                        } else if ($result['status'] == 'Trong kho') {
                            echo '<span style="color:orange; font-weight:bold;">Máy chưa được bán ra (Chưa kích hoạt)</span>';
                        } else {
                            echo '<span style="color:red; font-weight:bold;">Đang được bảo hành tại trung tâm</span>';
                        }
                        ?>
                    </p>
                <?php else: ?>
                    <h3 style="color: red;">❌ Không tìm thấy thiết bị!</h3>
                    <p>Mã IMEI/Serial này không tồn tại trong hệ thống. Vui lòng kiểm tra lại.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-top: 20px;">
        <a href="index.php?action=dashboard" style="color: #666; text-decoration: none;">Quay lại trang quản trị</a>
    </div>

</body>

</html>
