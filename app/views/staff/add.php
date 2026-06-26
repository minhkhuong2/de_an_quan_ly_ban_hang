<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-card {
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
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=staff_list" style="text-decoration:none; color:#637381;">←</a> Thêm mới nhân viên</h2>
</div>

<form action="index.php?action=add_staff" method="POST" class="sapo-card">
    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:1;"><label>Họ</label><input type="text" name="last_name" class="form-control" placeholder="Họ nhân viên"></div>
        <div class="form-group" style="flex:1;"><label>Tên <span style="color:red;">*</span></label><input type="text" name="first_name" class="form-control" placeholder="Tên nhân viên" required></div>
    </div>
    <div class="form-group"><label>Email <span style="color:red;">*</span></label><input type="email" name="email" class="form-control" placeholder="Email nhân viên" required></div>
    <div class="form-group"><label>Điện thoại <span style="color:red;">*</span></label><input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required></div>
    <div class="form-group">
        <label>Vai trò phân quyền</label>
        <select name="role" class="form-control">
            <option value="Nhân viên bán hàng">Nhân viên bán hàng</option>
            <option value="Nhân viên kho">Nhân viên kho</option>
            <option value="Quản lý chi nhánh">Quản lý chi nhánh</option>
        </select>
    </div>
    <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 20px; background: #fafbfc; padding: 10px; border: 1px solid #dfe3e8; border-radius: 4px;">
        <input type="checkbox" checked id="send_mail" style="width: 16px; height: 16px;">
        <label for="send_mail" style="margin:0;">Gửi thông báo mời truy cập cửa hàng qua email</label>
    </div>
    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">📨 Gửi lời mời</button>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
