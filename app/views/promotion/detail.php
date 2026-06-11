<?php

/** @var array $promo */
/** @var array $applied_orders */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    /* CSS CHUẨN MINIMALISM V2 */
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
        line-height: 1;
        margin-top: -4px;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        color: #212b36;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: 0.2s;
    }

    .btn-outline:hover {
        background: #f4f6f8;
    }

    .btn-primary {
        background: #0088ff;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-primary:hover {
        background: #0070cc;
    }

    .btn-danger {
        background: #fff;
        border: 1px solid #ffccc7;
        color: #d82c0d;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-danger:hover {
        background: #fff1f0;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .v3-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

    .data-row {
        margin-bottom: 12px;
        font-size: 14px;
    }

    .data-label {
        color: #637381;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .data-value {
        color: #212b36;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-control:disabled {
        background: #f4f6f8;
        color: #637381;
        cursor: not-allowed;
    }

    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .status-dot::before {
        content: "";
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-active {
        background: #eafff0;
        color: #008a00;
    }

    .status-active::before {
        background: #008a00;
    }

    .status-inactive {
        background: #fff7e6;
        color: #fa8c16;
    }

    .status-inactive::before {
        background: #fa8c16;
    }

    .status-stopped {
        background: #f4f6f8;
        color: #637381;
    }

    .status-stopped::before {
        background: #c4cdd5;
    }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=promo_list">←</a>
        Khuyến mại: <?php echo htmlspecialchars($promo['promo_name']); ?>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="index.php?action=copy_promo&id=<?php echo $promo['id']; ?>" class="btn-outline">Sao chép</a>

        <?php if ($promo['status'] == 'Đang áp dụng'): ?>
            <form action="index.php?action=bulk_action_promo" method="POST" style="margin:0;">
                <input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>">
                <button type="submit" name="action" value="Ngừng" class="btn-outline" style="color:#d82c0d; border-color:#ffccc7;">Ngừng khuyến mại</button>
            </form>
        <?php endif; ?>

        <button type="submit" form="editForm" class="btn-primary">Lưu thay đổi</button>
    </div>
</div>

<?php if (isset($_GET['success_edit'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size: 14px;">✅ Đã cập nhật thông tin khuyến mại thành công!</div><?php endif; ?>

<div style="display: flex; gap: 20px; align-items: flex-start;">
    <div style="flex: 0 0 320px;">
        <div class="v3-card">
            <div class="v3-card-title">Tổng quan</div>

            <div class="data-row">
                <?php
                if ($promo['status'] == 'Đang áp dụng') echo '<span class="status-dot status-active">Đang áp dụng</span>';
                elseif ($promo['status'] == 'Chưa áp dụng') echo '<span class="status-dot status-inactive">Chưa áp dụng</span>';
                else echo '<span class="status-dot status-stopped">Ngừng áp dụng</span>';
                ?>
            </div>

            <div class="data-row" style="margin-top: 20px;">
                <div class="data-label">Loại khuyến mại</div>
                <div class="data-value">
                    <?php
                    if ($promo['promo_type'] == 'discount_order') echo '📉 Giảm giá đơn hàng';
                    elseif ($promo['promo_type'] == 'discount_product') echo '📱 Giảm giá sản phẩm';
                    elseif ($promo['promo_type'] == 'gift_by_order' || $promo['promo_type'] == 'gift_by_product') echo '🎁 Mua X tặng Y';
                    elseif ($promo['promo_type'] == 'free_shipping') echo '🚚 Miễn phí vận chuyển';
                    ?>
                </div>
            </div>

            <div class="data-row">
                <div class="data-label">Hình thức</div>
                <div class="data-value">
                    <?php if (!empty($promo['promo_code'])): ?>
                        <span style="background:#f4f6f8; padding:4px 8px; border:1px dashed #c4cdd5; border-radius:4px; font-family:monospace; color: #0088ff; font-weight: bold;">
                            <?php echo htmlspecialchars($promo['promo_code']); ?>
                        </span>
                    <?php else: ?>
                        Chương trình tự động
                    <?php endif; ?>
                </div>
            </div>

            <div class="data-row">
                <div class="data-label">Đã dùng / Giới hạn</div>
                <div class="data-value" style="font-size: 16px;">
                    <strong style="color: #0088ff;"><?php echo $promo['used_count'] ?? 0; ?></strong> /
                    <?php echo empty($promo['usage_limit']) ? '&infin;' : $promo['usage_limit']; ?>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 20px 0;">

            <?php if (!empty($promo['promo_code'])): ?>
                <button type="button" onclick="sharePromoCode('<?php echo htmlspecialchars($promo['promo_code']); ?>')" style="width: 100%; background: #f4f6f8; border: 1px solid #c4cdd5; padding: 10px; border-radius: 4px; font-weight: 500; cursor: pointer; margin-bottom: 10px; color: #212b36;">
                    🔗 Chia sẻ mã khuyến mại
                </button>
            <?php endif; ?>

            <button type="button" onclick="document.getElementById('report_section').scrollIntoView({behavior: 'smooth'})" style="width: 100%; background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 10px; border-radius: 4px; font-weight: 500; cursor: pointer;">
                📊 Xem báo cáo doanh thu
            </button>
        </div>
    </div>

    <div style="flex: 1;">

        <form id="editForm" action="index.php?action=edit_promo&id=<?php echo $promo['id']; ?>" method="POST">
            <div class="v3-card">
                <div class="v3-card-title">Cấu hình khuyến mại</div>

                <?php if ($promo['status'] == 'Đang áp dụng'): ?>
                    <div style="background: #e6f7ff; border: 1px solid #91d5ff; padding: 10px 15px; border-radius: 4px; font-size: 13px; color: #0050b3; margin-bottom: 15px;">
                        💡 <b>Chương trình đang áp dụng:</b> Để đảm bảo tính chính xác của dữ liệu, bạn chỉ có thể sửa đổi <b>Số lượng áp dụng</b> và <b>Ngày kết thúc</b>.
                    </div>
                <?php endif; ?>

                <div class="data-row">
                    <label class="data-label">Tên chương trình khuyến mại</label>
                    <input type="text" name="promo_name" class="form-control" value="<?php echo htmlspecialchars($promo['promo_name']); ?>" <?php echo ($promo['status'] == 'Đang áp dụng') ? 'readonly disabled' : 'required'; ?>>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div class="data-row" style="flex: 1;">
                        <label class="data-label">Ngày bắt đầu</label>
                        <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($promo['start_date'])); ?>" disabled readonly>
                    </div>
                    <div class="data-row" style="flex: 1;" id="end_date_box" style="<?php echo ($promo['no_end_date']) ? 'display:none;' : ''; ?>">
                        <label class="data-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($promo['end_date'])); ?>">
                    </div>
                </div>

                <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer; margin-bottom: 15px;">
                    <input type="checkbox" name="no_end_date" id="no_end_date" value="1" onchange="toggleEndDate()" <?php echo ($promo['no_end_date']) ? 'checked' : ''; ?>> Không có ngày kết thúc
                </label>

                <div class="data-row" style="width: 50%;">
                    <label class="data-label">Giới hạn số lượng áp dụng</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="number" name="usage_limit" id="usage_limit" class="form-control" value="<?php echo $promo['usage_limit']; ?>" <?php echo empty($promo['usage_limit']) ? 'disabled' : ''; ?>>
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer; white-space: nowrap;">
                            <input type="checkbox" name="unlimited_usage" id="unlimited_usage" value="1" onchange="toggleLimit()" <?php echo empty($promo['usage_limit']) ? 'checked' : ''; ?>> Không giới hạn
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <div class="v3-card" id="report_section" style="padding: 0;">
            <div class="v3-card-title" style="padding: 20px 20px 10px 20px; border-bottom: none; margin-bottom:0;">
                Báo cáo doanh thu & Lịch sử sử dụng
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #fafbfc; color: #637381; border-top: 1px solid #dfe3e8; border-bottom: 1px solid #dfe3e8; font-size: 13px;">
                            <th style="padding: 12px 20px;">Mã đơn hàng</th>
                            <th style="padding: 12px 20px;">Ngày duyệt</th>
                            <th style="padding: 12px 20px;">Khách hàng</th>
                            <th style="padding: 12px 20px; text-align:right;">Giá trị khuyến mại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applied_orders)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #637381; font-size: 14px;">Chưa có đơn hàng nào áp dụng khuyến mại này.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applied_orders as $o): ?>
                                <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;">
                                    <td style="padding: 15px 20px;"><a href="#" style="color:#0088ff; text-decoration:none; font-weight:600;"><?php echo $o['order_code']; ?></a></td>
                                    <td style="padding: 15px 20px; color: #637381;"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                                    <td style="padding: 15px 20px;"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                                    <td style="padding: 15px 20px; text-align:right; font-weight:600; color:#108043;">- <?php echo number_format($o['discount_amount'], 0, ',', '.'); ?> ₫</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="text-align: right; margin-top: 10px;">
            <form action="index.php?action=bulk_action_promo" method="POST" onsubmit="return confirm('⚠️ Khuyến mại sau khi xóa sẽ không thể khôi phục. Bạn có chắc chắn muốn xóa?');">
                <input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>">
                <button type="submit" name="action" value="delete" class="btn-danger">🗑️ Xóa khuyến mại</button>
            </form>
        </div>

    </div>
</div>

<script>
    // Hàm chia sẻ mã (Copy to clipboard)
    function sharePromoCode(code) {
        let textToShare = "🛒 Mã giảm giá dành cho bạn: " + code + "\nSử dụng ngay tại cửa hàng của chúng tôi để nhận ưu đãi!";
        navigator.clipboard.writeText(textToShare).then(() => {
            alert("✅ Đã sao chép nội dung chia sẻ mã khuyến mại (" + code + ") vào khay nhớ tạm!");
        }).catch(err => {
            alert("Lỗi khi sao chép: " + err);
        });
    }

    // Logic ẩn hiện checkbox Ngày kết thúc và Giới hạn
    function toggleEndDate() {
        let isChecked = document.getElementById('no_end_date').checked;
        document.getElementById('end_date_box').style.display = isChecked ? 'none' : 'block';
    }

    function toggleLimit() {
        let isChecked = document.getElementById('unlimited_usage').checked;
        let limitInput = document.getElementById('usage_limit');
        if (isChecked) {
            limitInput.disabled = true;
            limitInput.value = '';
        } else {
            limitInput.disabled = false;
        }
    }

    window.onload = function() {
        toggleEndDate();
    };
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
