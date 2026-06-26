<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>ÄÄƒng nháº­p há»‡ thá»‘ng - AKC Store</title>
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

        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
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
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
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

        .form-control:focus {
            border-color: #0088ff;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #0088ff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #0070d2;
        }

        .error-msg {
            color: #d82c0d;
            background: #fff1f0;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ffa39e;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo-title">ðŸ›’ AKC Store</div>
        <div class="sub-title">ÄÄƒng nháº­p Ä‘á»ƒ quáº£n lÃ½ cá»­a hÃ ng cá»§a báº¡n</div>

        <?php if (!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>

        <form action="index.php?action=login" method="POST">
            <div class="form-group">
                <label>TÃ i khoáº£n Ä‘Äƒng nháº­p</label>
                <input type="text" name="username" class="form-control" placeholder="Nháº­p tÃ i khoáº£n (VD: admin)" required autofocus>
            </div>
            <div class="form-group">
                <label>Máº­t kháº©u</label>
                <input type="password" name="password" class="form-control" placeholder="Nháº­p máº­t kháº©u" required>
            </div>
            <button type="submit" class="btn-submit">ÄÄ‚NG NHáº¬P</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 14px;">
            ChÆ°a cÃ³ tÃ i khoáº£n nhÃ¢n viÃªn? <a href="index.php?action=register" style="color:#0088ff; text-decoration:none;">ÄÄƒng kÃ½ ngay</a>
        </div>
    </div>

</body>

</html>

