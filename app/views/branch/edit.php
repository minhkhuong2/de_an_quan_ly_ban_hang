<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array|null $branch */ $b = is_array($branch) ? $branch : []; ?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 800px;
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
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .switch-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fafbfc;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #dfe3e8;
        margin-bottom: 10px;
    }
</style>

<div style="max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=branch_list" style="text-decoration:none; color:#637381;">←</a> Cập nhật chi nhánh</h2>
</div>

<form action="index.php?action=edit_branch&id=<?php echo $b['id'] ?? 0; ?>" method="POST" class="sapo-card">

    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px; margin-bottom: 15px;">
        <h3 style="font-size: 16px; margin:0;">1. Thông tin cơ bản</h3>
        <select name="status" style="padding: 6px 12px; border-radius: 4px; border: 1px solid #c4cdd5; font-weight: bold; background: <?php echo ($b['status'] == 'Hoạt động') ? '#eafff0; color:#108043' : '#fff1f0; color:#cf1322'; ?>;">
            <option value="Hoạt động" <?php echo ($b['status'] == 'Hoạt động') ? 'selected' : ''; ?>>● Đang hoạt động</option>
            <option value="Ngừng hoạt động" <?php echo ($b['status'] == 'Ngừng hoạt động') ? 'selected' : ''; ?>>○ Ngừng hoạt động</option>
        </select>
    </div>

    <div class="form-group"><label>Tên chi nhánh <span style="color:red;">*</span></label><input type="text" name="branch_name" class="form-control" value="<?php echo htmlspecialchars($b['branch_name'] ?? ''); ?>" required></div>
    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:1;"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($b['phone'] ?? ''); ?>"></div>
        <div class="form-group" style="flex:1;"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($b['email'] ?? ''); ?>"></div>
    </div>
    <div class="form-group"><label>Địa chỉ cụ thể</label><input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($b['address'] ?? ''); ?>"></div>

    <h3 style="font-size: 16px; margin-top:30px; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px;">2. Thiết lập khác</h3>

    <div class="switch-box">
        <input type="checkbox" name="is_inventory" value="1" style="width: 18px; height: 18px; margin-top:2px;" <?php echo !empty($b['is_inventory']) ? 'checked' : ''; ?>>
        <div>
            <strong style="color: #212b36;">Thiết lập làm chi nhánh quản lý kho</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Chi nhánh sẽ có kho riêng để quản lý tồn kho, xuất nhập hàng.</p>
        </div>
    </div>

    <div class="switch-box">
        <input type="checkbox" name="is_pickup" value="1" style="width: 18px; height: 18px; margin-top:2px;" <?php echo !empty($b['is_pickup']) ? 'checked' : ''; ?>>
        <div>
            <strong style="color: #212b36;">Là địa chỉ lấy hàng (Nhận đơn Online)</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Cho phép shipper của đối tác vận chuyển đến địa chỉ này lấy hàng.</p>
        </div>
    </div>

    <div class="switch-box" style="background: <?php echo !empty($b['is_default']) ? '#e6f7ff; border-color:#91d5ff;' : '#fff8ea; border-color: #ffc453;'; ?>">
        <input type="checkbox" name="is_default" value="1" style="width: 18px; height: 18px; margin-top:2px;" <?php echo !empty($b['is_default']) ? 'checked' : ''; ?>>
        <div>
            <strong style="color: <?php echo !empty($b['is_default']) ? '#0050b3;' : '#8a6100;'; ?>">Đặt làm chi nhánh mặc định</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Chi nhánh này sẽ được ưu tiên hiển thị đầu tiên trên toàn hệ thống.</p>
        </div>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">💾 Lưu thay đổi</button>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
