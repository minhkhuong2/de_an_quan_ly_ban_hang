<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $promo */
$products = $products ?? [];
$categories = $categories ?? [];

// Sửa lỗi xung đột Validate: Nếu bị khóa, chỉ in ra 'disabled', gỡ bỏ 'required'
$is_locked = ($promo['status'] == 'Đang áp dụng') ? 'disabled' : 'required';
$is_disabled_only = ($promo['status'] == 'Đang áp dụng') ? 'disabled' : '';

$promo_apply = isset($promo['product_apply_settings']) ? json_decode($promo['product_apply_settings'], true) : [];
$promo_gift = isset($promo['gift_settings']) ? json_decode($promo['gift_settings'], true) : [];
$promo_ship = isset($promo['shipping_settings']) ? json_decode($promo['shipping_settings'], true) : [];
$channels = isset($promo['sales_channels']) ? json_decode($promo['sales_channels'], true) : ['pos', 'web'];
$combinations = isset($promo['allowed_combinations']) ? json_decode($promo['allowed_combinations'], true) : [];
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 20px;
        font-weight: bold;
        color: #212b36;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .v3-title a {
        color: #637381;
        text-decoration: none;
        font-size: 24px;
        margin-top: -4px;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dfe3e8;
    }

    .v3-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 4px;
        font-size: 13px;
        color: #637381;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .form-control:disabled {
        background: #f4f6f8;
        color: #637381;
        cursor: not-allowed;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    .radio-box,
    .check-box {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
        margin-bottom: 10px;
    }

    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .status-active {
        background: #eafff0;
        color: #008a00;
    }

    .status-inactive {
        background: #fff7e6;
        color: #fa8c16;
    }

    .status-stopped {
        background: #f4f6f8;
        color: #637381;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        color: #212b36;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-danger {
        background: #fff;
        border: 1px solid #ffccc7;
        color: #d82c0d;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
    }
</style>

<form id="editForm" action="index.php?action=edit_promo&id=<?php echo $promo['id']; ?>" method="POST">
    <div class="v3-header">
        <div class="v3-title"><a href="index.php?action=promo_list">←</a> Chi tiết chương trình khuyến mại</div>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?action=copy_promo&id=<?php echo $promo['id']; ?>" class="btn-outline">📄 Sao chép</a>
            <?php if ($promo['status'] == 'Đang áp dụng'): ?>
                <button type="button" class="btn-outline" style="color:#d82c0d; border-color:#ffccc7;" onclick="document.getElementById('stopForm').submit();">⏸ Ngừng áp dụng</button>
            <?php endif; ?>

            <button type="submit" class="btn-primary" id="btnSave">💾 Lưu thay đổi</button>
        </div>
    </div>

    <?php if (isset($_GET['success_edit'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size: 14px;">✅ Đã cập nhật toàn bộ cấu hình khuyến mại thành công!</div><?php endif; ?>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 65%;">

            <div class="v3-card">
                <div class="v3-card-title">Thông tin chung</div>
                <div style="margin-bottom: 15px;">
                    <?php
                    if ($promo['status'] == 'Đang áp dụng') echo '<span class="status-dot status-active">● Đang áp dụng</span>';
                    elseif ($promo['status'] == 'Chưa áp dụng') echo '<span class="status-dot status-inactive">⏱ Chưa áp dụng</span>';
                    else echo '<span class="status-dot status-stopped">○ Ngừng áp dụng</span>';
                    ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Tên chương trình khuyến mại *</label>
                    <input type="text" name="promo_name" class="form-control" value="<?php echo htmlspecialchars($promo['promo_name']); ?>" <?php echo $is_locked; ?>>
                </div>
                <?php if (!empty($promo['promo_code'])): ?>
                    <div class="form-group">
                        <label class="form-label">Mã khuyến mại (Cố định)</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($promo['promo_code']); ?>" disabled style="font-family: monospace; font-weight: bold; color: #0088ff;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="v3-card" id="card_standard_discount">
                <div class="v3-card-title">Cấu hình giá trị giảm</div>
                <div class="row-flex">
                    <div class="form-group">
                        <label class="form-label">Hình thức giảm</label>
                        <select name="discount_type" class="form-control" <?php echo $is_disabled_only; ?>>
                            <option value="amount" <?php echo ($promo['discount_type'] == 'amount') ? 'selected' : ''; ?>>Theo số tiền (₫)</option>
                            <option value="percent" <?php echo ($promo['discount_type'] == 'percent') ? 'selected' : ''; ?>>Theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mức giảm</label>
                        <input type="text" name="discount_value" class="form-control" value="<?php echo number_format($promo['discount_value'], 0, '', '.'); ?>" <?php echo $is_locked; ?> oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Điều kiện áp dụng</div>
                <?php
                $cond_type = 'none';
                if ($promo['min_order_value'] > 0) $cond_type = 'min_amount';
                if ($promo['min_product_qty'] > 0) $cond_type = 'min_qty';
                ?>
                <div style="padding-left: 5px;">
                    <label class="radio-box"><input type="radio" name="condition_type" value="none" <?php echo $cond_type == 'none' ? 'checked' : ''; ?> <?php echo $is_disabled_only; ?>> Không có điều kiện</label>
                    <label class="radio-box"><input type="radio" name="condition_type" value="min_amount" <?php echo $cond_type == 'min_amount' ? 'checked' : ''; ?> <?php echo $is_disabled_only; ?>> Đạt mốc giá trị đơn hàng tối thiểu từ:</label>
                    <input type="text" name="min_order_value" class="form-control" style="width: 50%; margin-left: 24px;" value="<?php echo number_format($promo['min_order_value'], 0, '', '.'); ?>" <?php echo $is_disabled_only; ?>>
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="v3-card">
                <div class="v3-card-title">Thời gian hiệu lực</div>
                <div class="form-group">
                    <label class="form-label">Bắt đầu</label>
                    <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($promo['start_date'])); ?>" disabled>
                </div>
                <div class="form-group" id="end_date_box" style="<?php echo $promo['no_end_date'] ? 'display:none;' : ''; ?>">
                    <label class="form-label">Kết thúc (Luôn được sửa) *</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($promo['end_date'])); ?>">
                </div>
                <label class="check-box"><input type="checkbox" name="no_end_date" id="no_end_date" value="1" <?php echo $promo['no_end_date'] ? 'checked' : ''; ?> onchange="document.getElementById('end_date_box').style.display = this.checked ? 'none' : 'block';"> Không giới hạn ngày kết thúc</label>
            </div>

            <div class="v3-card" id="usage_limit_card" style="<?php echo empty($promo['promo_code']) ? 'display:none;' : ''; ?>">
                <div class="v3-card-title">Giới hạn số lần dùng (Luôn được sửa)</div>
                <label class="check-box"><input type="checkbox" name="unlimited_usage" id="unlimited_usage" <?php echo is_null($promo['usage_limit']) ? 'checked' : ''; ?> onchange="document.getElementById('limit_box').style.display = this.checked ? 'none' : 'block';"> Không giới hạn</label>
                <div id="limit_box" style="<?php echo is_null($promo['usage_limit']) ? 'display:none;' : 'display:block;'; ?> margin-top: 10px;">
                    <input type="number" name="usage_limit" class="form-control" value="<?php echo $promo['usage_limit']; ?>" min="1">
                </div>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Kênh bán hàng</div>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="pos" <?php echo in_array('pos', $channels) ? 'checked' : ''; ?> <?php echo $is_disabled_only; ?>> Bán tại quầy (POS)</label>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="web" <?php echo in_array('web', $channels) ? 'checked' : ''; ?> <?php echo $is_disabled_only; ?>> Website</label>
            </div>
        </div>
    </div>
</form>

<form id="stopForm" action="index.php?action=bulk_action_promo" method="POST" style="display:none;"><input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>"><input type="hidden" name="action" value="Ngừng"></form>

<div style="text-align: right; margin-top: 20px;">
    <form action="index.php?action=bulk_action_promo" method="POST" onsubmit="return confirm('⚠️ Thao tác xóa không thể khôi phục. Bạn chắc chắn muốn xóa?');">
        <input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>">
        <button type="submit" name="action" value="delete" class="btn-danger">🗑️ Xóa vĩnh viễn khuyến mại</button>
    </form>
</div>

<script>
    document.getElementById('editForm').addEventListener('submit', function(e) {
        if (this.dataset.submitted) {
            e.preventDefault();
            return false;
        }
        this.dataset.submitted = true;
        let btn = document.getElementById('btnSave');
        if (btn) {
            // Mấu chốt nằm ở setTimeout 50ms này, nó cứu cái form không bị chặn đứng!
            setTimeout(() => {
                btn.disabled = true;
                btn.innerText = 'Đang lưu...';
            }, 50);
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
