<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array|null $partner */ $p = is_array($partner) ? $partner : []; ?>

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
        color: #212b36;
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
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=shipping_list" style="text-decoration:none; color:#637381;">←</a> Cấu hình: <?php echo htmlspecialchars($p['partner_name'] ?? ''); ?></h2>
</div>

<form action="index.php?action=edit_shipping&id=<?php echo $p['id'] ?? 0; ?>" method="POST" class="sapo-card">
    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:2;"><label>Tên đối tác <span style="color:red;">*</span></label><input type="text" name="partner_name" class="form-control" value="<?php echo htmlspecialchars($p['partner_name'] ?? ''); ?>" required></div>
        <div class="form-group" style="flex:1;"><label>Mã ĐVVC</label><input type="text" name="partner_code" class="form-control" value="<?php echo htmlspecialchars($p['partner_code'] ?? ''); ?>" required></div>
    </div>

    <div class="form-group">
        <label>Phí vận chuyển mặc định (₫)</label>
        <input type="text" name="base_fee" class="form-control" value="<?php echo number_format($p['base_fee'] ?? 0, 0, '', '.'); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
    </div>

    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:1;">
            <label>Giới hạn số lần giao lại</label>
            <input type="number" name="max_retry" class="form-control" value="<?php echo htmlspecialchars($p['max_retry'] ?? 3); ?>" min="1" max="5">
        </div>
        <div class="form-group" style="flex:1;">
            <label>Trạng thái kết nối</label>
            <select name="status" class="form-control">
                <option value="Đang kết nối" <?php echo (($p['status'] ?? '') == 'Đang kết nối') ? 'selected' : ''; ?>>Đang kết nối</option>
                <option value="Ngừng kết nối" <?php echo (($p['status'] ?? '') == 'Ngừng kết nối') ? 'selected' : ''; ?>>Ngừng kết nối</option>
            </select>
        </div>
    </div>

    <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 10px; background: #fafbfc; padding: 10px; border: 1px solid #dfe3e8; border-radius: 4px;">
        <input type="checkbox" name="allow_cod" value="1" id="allow_cod" style="width: 16px; height: 16px;" <?php echo !empty($p['allow_cod']) ? 'checked' : ''; ?>>
        <label for="allow_cod" style="margin:0; font-weight: bold; color: #108043;">Hỗ trợ thu hộ tiền (COD)</label>
    </div>

    <div class="form-group" style="margin-top: 15px;">
        <label>Chính sách & Ghi chú bồi thường</label>
        <textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($p['notes'] ?? ''); ?></textarea>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">💾 Lưu cấu hình</button>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
