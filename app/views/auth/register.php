<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký nhân viên - Sapo AAKC</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-card {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 440px;
        }

        .logo-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #0088ff;
            margin-bottom: 5px;
        }

        .sub-title {
            text-align: center;
            color: #637381;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 14px;
            color: #212b36;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
            outline: none;
            font-size: 14px;
            box-sizing: border-box;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #52c41a;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #389e0d;
        }
    </style>
</head>

<body>

    <div class="register-card">
        <div class="logo-title">🛒 SAPO OMNIAI</div>
        <div class="sub-title">Khởi tạo tài khoản nhân viên hệ thống</div>

        <?php if (!empty($message)) echo $message; ?>

        <form action="index.php?action=register" method="POST">
            <div class="form-group">
                <label>Tài khoản (Username) *</label>
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập không dấu, viết liền" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu *</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            <div class="form-group">
                <label>Họ và tên nhân viên *</label>
                <input type="text" name="full_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
            </div>
            <div class="form-group">
                <label>Vai trò / Phân quyền hệ thống</label>
                <select name="role" class="form-control" style="background:#fff;">
                    <option value="Thu ngân">Thu ngân (Sử dụng màn hình POS)</option>
                    <option value="Nhân viên kho">Nhân viên kho (Quản lý sản phẩm, IMEI)</option>
                    <option value="Admin">Admin (Toàn quyền quản trị hệ thống)</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">ĐĂNG KÝ TÀI KHOẢN</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 14px;">
            Đã có tài khoản? <a href="index.php?action=login" style="color:#0088ff; text-decoration:none;">Đăng nhập ngay</a>
        </div>
    </div>

</body>

</html>
