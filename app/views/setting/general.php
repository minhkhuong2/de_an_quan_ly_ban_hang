<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $settings */ ?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 700px;
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

    .form-control:focus {
        border-color: #0088ff;
        outline: none;
    }
</style>

<div style="max-width: 700px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=settings" style="text-decoration:none; color:#637381;">←</a> Cấu hình chung</h2>
</div>

<form action="index.php?action=general_settings" method="POST" class="sapo-card">
    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật thông tin cửa hàng thành công!</div><?php endif; ?>

    <h3 style="font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px; color: #0050b3;">Thông tin cửa hàng</h3>

    <div class="form-group">
        <label>Tên cửa hàng <span style="color:red;">*</span></label>
        <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>" required>
    </div>

    <div style="display: flex; gap: 15px;">
        <div class="form-group" style="flex: 1;">
            <label>Số điện thoại liên hệ</label>
            <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>">
        </div>
        <div class="form-group" style="flex: 1;">
            <label>Email liên hệ</label>
            <input type="email" name="store_email" class="form-control" value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-group">
        <label>Địa chỉ cửa hàng</label>
        <textarea name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['store_address'] ?? ''); ?></textarea>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">💾 Lưu cấu hình</button>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
