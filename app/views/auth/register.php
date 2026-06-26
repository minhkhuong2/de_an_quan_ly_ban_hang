<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>ÄÄƒng kÃ½ nhÃ¢n viÃªn - AKC Store</title>
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
        <div class="logo-title">ðŸ›’ AKC Store</div>
        <div class="sub-title">Khá»Ÿi táº¡o tÃ i khoáº£n nhÃ¢n viÃªn há»‡ thá»‘ng</div>

        <?php if (!empty($message)) echo $message; ?>

        <form action="index.php?action=register" method="POST">
            <div class="form-group">
                <label>TÃ i khoáº£n (Username) *</label>
                <input type="text" name="username" class="form-control" placeholder="TÃªn Ä‘Äƒng nháº­p khÃ´ng dáº¥u, viáº¿t liá»n" required>
            </div>
            <div class="form-group">
                <label>Máº­t kháº©u *</label>
                <input type="password" name="password" class="form-control" placeholder="Nháº­p máº­t kháº©u" required>
            </div>
            <div class="form-group">
                <label>Há» vÃ  tÃªn nhÃ¢n viÃªn *</label>
                <input type="text" name="full_name" class="form-control" placeholder="VÃ­ dá»¥: Nguyá»…n VÄƒn A" required>
            </div>
            <div class="form-group">
                <label>Vai trÃ² / PhÃ¢n quyá»n há»‡ thá»‘ng</label>
                <select name="role" class="form-control" style="background:#fff;">
                    <option value="Thu ngÃ¢n">Thu ngÃ¢n (Sá»­ dá»¥ng mÃ n hÃ¬nh POS)</option>
                    <option value="NhÃ¢n viÃªn kho">NhÃ¢n viÃªn kho (Quáº£n lÃ½ sáº£n pháº©m, IMEI)</option>
                    <option value="Admin">Admin (ToÃ n quyá»n quáº£n trá»‹ há»‡ thá»‘ng)</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">ÄÄ‚NG KÃ TÃ€I KHOáº¢N</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 14px;">
            ÄÃ£ cÃ³ tÃ i khoáº£n? <a href="index.php?action=login" style="color:#0088ff; text-decoration:none;">ÄÄƒng nháº­p ngay</a>
        </div>
    </div>

</body>

</html>

