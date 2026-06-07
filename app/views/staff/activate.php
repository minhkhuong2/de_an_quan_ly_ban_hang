<?php
/** @var array|null $staff */
$staffId = $staff['id'] ?? 0;
$staffFirstName = $staff['first_name'] ?? 'Bạn';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận tham gia cửa hàng</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .activate-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .form-control { width: 100%; padding: 12px; margin: 10px 0 20px 0; border: 1px solid #c4cdd5; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #0088ff; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="activate-box">
        <h2 style="color: #212b36; margin-top: 0;">Xác nhận tham gia</h2>
        <p style="color: #637381; font-size: 14px; margin-bottom: 25px;">
            Chào <b><?php echo htmlspecialchars($staffFirstName); ?></b>, vui lòng thiết lập mật khẩu để hoàn tất việc tạo tài khoản.
        </p>
        
        <form action="index.php?action=activate_staff&id=<?php echo $staffId; ?>" method="POST">
            <div style="text-align: left;">
                <label style="font-weight: bold; font-size: 14px; color: #212b36;">Tạo mật khẩu mới</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu (ít nhất 6 ký tự)" required minlength="6">
            </div>
            <button type="submit" class="btn-submit">Kích hoạt tài khoản</button>
        </form>
    </div>
</body>
</html>
