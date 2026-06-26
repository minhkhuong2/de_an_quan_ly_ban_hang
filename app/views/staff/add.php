<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>

<div style="max-width: 600px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=staff_list" style="text-decoration:none; color:#637381;">â†</a> ThÃªm má»›i nhÃ¢n viÃªn</h2>
</div>

<form action="index.php?action=add_staff" method="POST" class="Há»‡ thá»‘ng-card">
    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:1;"><label>Há»</label><input type="text" name="last_name" class="form-control" placeholder="Há» nhÃ¢n viÃªn"></div>
        <div class="form-group" style="flex:1;"><label>TÃªn <span style="color:red;">*</span></label><input type="text" name="first_name" class="form-control" placeholder="TÃªn nhÃ¢n viÃªn" required></div>
    </div>
    <div class="form-group"><label>Email <span style="color:red;">*</span></label><input type="email" name="email" class="form-control" placeholder="Email nhÃ¢n viÃªn" required></div>
    <div class="form-group"><label>Äiá»‡n thoáº¡i <span style="color:red;">*</span></label><input type="text" name="phone" class="form-control" placeholder="Sá»‘ Ä‘iá»‡n thoáº¡i" required></div>
    <div class="form-group">
        <label>Vai trÃ² phÃ¢n quyá»n</label>
        <select name="role" class="form-control">
            <option value="NhÃ¢n viÃªn bÃ¡n hÃ ng">NhÃ¢n viÃªn bÃ¡n hÃ ng</option>
            <option value="NhÃ¢n viÃªn kho">NhÃ¢n viÃªn kho</option>
            <option value="Quáº£n lÃ½ chi nhÃ¡nh">Quáº£n lÃ½ chi nhÃ¡nh</option>
        </select>
    </div>
    <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 20px; background: #fafbfc; padding: 10px; border: 1px solid #dfe3e8; border-radius: 4px;">
        <input type="checkbox" checked id="send_mail" style="width: 16px; height: 16px;">
        <label for="send_mail" style="margin:0;">Gá»­i thÃ´ng bÃ¡o má»i truy cáº­p cá»­a hÃ ng qua email</label>
    </div>
    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">ðŸ“¨ Gá»­i lá»i má»i</button>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

