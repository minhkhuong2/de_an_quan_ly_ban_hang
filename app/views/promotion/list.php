<?php
/** @var array $promotions */
require_once __DIR__ . '/../layout/header.php';
$safe_promos = is_array($promotions ?? null) ? $promotions : [];
?>

<style>
    .sapo-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        align-items: center;
        border-radius: 8px 8px 0 0;
    }

    .sapo-filter-bar input.search-input {
        flex: 1;
        padding: 8px 12px 8px 35px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23c4cdd5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>') no-repeat 10px center;
        background-size: 16px;
    }

    .sapo-filter-bar select,
    .filter-btn {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .filter-btn {
        background: #0088ff;
        color: #fff;
        border: none;
        font-weight: bold;
    }

    /* Thanh thao tác hàng loạt nổi lên khi có checkbox được chọn */
    .bulk-action-bar {
        display: none;
        background: #e6f7ff;
        border: 1px solid #91d5ff;
        padding: 12px 20px;
        border-radius: 4px;
        margin-bottom: 15px;
        align-items: center;
        justify-content: space-between;
    }

    .bulk-btn {
        padding: 6px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        margin-left: 8px;
    }

    .bulk-btn:hover {
        background: #f4f6f8;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách Khuyến mại</h2>
    <a href="index.php?action=add_promo" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight:500;">+ Tạo khuyến mại mới</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật chương trình khuyến mại thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_bulk'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">⚡ Đã thực hiện thao tác hàng loạt thành công!</div><?php endif; ?>

<form method="GET" action="index.php" class="sapo-filter-bar">
    <input type="hidden" name="action" value="promo_list">
    <input type="text" name="search" class="search-input" placeholder="Nhập mã hoặc tên chương trình khuyến mại..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

    <select name="status">
        <option value="">-- Trạng thái --</option>
        <option value="Đang chạy" <?php if (($_GET['status'] ?? '') == 'Đang chạy') echo 'selected'; ?>>● Đang chạy</option>
        <option value="Chờ chạy" <?php if (($_GET['status'] ?? '') == 'Chờ chạy') echo 'selected'; ?>>⏱ Chờ chạy (Lưu nháp)</option>
        <option value="Tạm dừng" <?php if (($_GET['status'] ?? '') == 'Tạm dừng') echo 'selected'; ?>>⏸ Tạm dừng</option>
        <option value="Kết thúc" <?php if (($_GET['status'] ?? '') == 'Kết thúc') echo 'selected'; ?>>○ Kết thúc / Hủy</option>
    </select>

    <select name="type">
        <option value="">-- Loại khuyến mại --</option>
        <option value="discount_order" <?php if (($_GET['type'] ?? '') == 'discount_order') echo 'selected'; ?>>Giảm giá đơn hàng</option>
        <option value="discount_product" <?php if (($_GET['type'] ?? '') == 'discount_product') echo 'selected'; ?>>Giảm giá sản phẩm</option>
        <option value="gift_by_order" <?php if (($_GET['type'] ?? '') == 'gift_by_order') echo 'selected'; ?>>Tặng quà theo hóa đơn</option>
        <option value="gift_by_product" <?php if (($_GET['type'] ?? '') == 'gift_by_product') echo 'selected'; ?>>Mua X tặng Y</option>
    </select>

    <button type="submit" class="filter-btn">Lọc</button>
    <a href="index.php?action=promo_list" style="padding: 8px 12px; color: #637381; text-decoration: none; font-size: 14px;">Xóa lọc</a>
</form>

<form method="POST" action="index.php?action=bulk_action_promo">
    <div class="bulk-action-bar" id="bulk_action_bar">
        <div><strong style="color: #0050b3;" id="selected_count">Đã chọn 0 chương trình</strong></div>
        <div>
            <button type="submit" name="action" value="Đang chạy" class="bulk-btn" style="color:#108043;">▶ Kích hoạt</button>
            <button type="submit" name="action" value="Tạm dừng" class="bulk-btn" style="color:#cf1322;">⏸ Tạm dừng</button>
            <button type="submit" name="action" value="Kết thúc" class="bulk-btn" style="color:#637381;">🚫 Kết thúc sớm</button>
            <button type="submit" name="action" value="delete" class="bulk-btn" style="color:#cf1322; border-color:#ffa39e;" onclick="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN các chương trình đã chọn?');">🗑️ Xóa</button>
        </div>
    </div>

    <div class="card" style="background:#fff; border-radius: 0 0 8px 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #fafbfc; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                    <th style="padding: 15px; width: 40px;"><input type="checkbox" id="check_all" style="width:16px; height:16px;"></th>
                    <th style="padding: 15px;">Tên chương trình / Mã</th>
                    <th style="padding: 15px;">Loại khuyến mại</th>
                    <th style="padding: 15px;">Thời gian áp dụng</th>
                    <th style="padding: 15px;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($safe_promos)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #637381;">Không tìm thấy chương trình khuyến mại nào phù hợp.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($safe_promos as $p): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px; <?php echo $p['status'] == 'Kết thúc' ? 'opacity:0.6;' : ''; ?>">
                        <td style="padding: 15px;"><input type="checkbox" name="promo_ids[]" value="<?php echo $p['id']; ?>" class="row-checkbox" style="width:16px; height:16px;"></td>
                        <td style="padding: 15px;">
                            <strong style="color: #0088ff;"><?php echo htmlspecialchars($p['promo_name']); ?></strong>
                            <?php if (!empty($p['promo_code'])): ?>
                                <br><span style="display:inline-block; margin-top:5px; background:#f4f6f8; padding:2px 8px; border:1px dashed #c4cdd5; border-radius:4px; font-family:monospace; font-weight:bold;">🎟️ <?php echo htmlspecialchars($p['promo_code']); ?></span>
                            <?php else: ?>
                                <br><span style="display:inline-block; margin-top:5px; font-size:12px; color:#108043;">⚡ Tự động áp dụng</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php
                            if ($p['promo_type'] == 'discount_order') echo 'Giảm giá đơn hàng';
                            elseif ($p['promo_type'] == 'discount_product') echo 'Giảm giá sản phẩm';
                            elseif ($p['promo_type'] == 'gift_by_order') echo 'Tặng quà theo hóa đơn';
                            else echo 'Mua X tặng Y';
                            ?>
                            <?php if ($p['promo_type'] == 'discount_order' || $p['promo_type'] == 'discount_product'): ?>
                                <br><b style="color: #cf1322;">Giảm <?php echo ($p['discount_type'] == 'percent') ? $p['discount_value'] . '%' : number_format($p['discount_value'], 0, ',', '.') . '₫'; ?></b>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; color: #637381; font-size: 13px;">
                            Từ: <?php echo date('d/m/Y H:i', strtotime($p['start_date'])); ?><br>
                            Đến: <?php echo ($p['no_end_date']) ? 'Không giới hạn' : date('d/m/Y H:i', strtotime($p['end_date'])); ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php
                            if ($p['status'] == 'Đang chạy') echo '<span style="color:#108043; background:#eafff0; padding:4px 8px; border-radius:4px;">● Đang chạy</span>';
                            elseif ($p['status'] == 'Chờ chạy') echo '<span style="color:#fa8c16; background:#fff7e6; padding:4px 8px; border-radius:4px;">⏱ Chờ chạy</span>';
                            elseif ($p['status'] == 'Tạm dừng') echo '<span style="color:#cf1322; background:#fff1f0; padding:4px 8px; border-radius:4px;">⏸ Tạm dừng</span>';
                            else echo '<span style="color:#637381; background:#f4f6f8; padding:4px 8px; border-radius:4px;">○ Kết thúc</span>';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    // Xử lý Checkbox & Thanh thao tác
    const checkAll = document.getElementById('check_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionBar = document.getElementById('bulk_action_bar');
    const selectedCountText = document.getElementById('selected_count');

    function updateBulkAction() {
        let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkActionBar.style.display = 'flex';
            selectedCountText.innerText = 'Đã chọn ' + checkedCount + ' chương trình';
        } else {
            bulkActionBar.style.display = 'none';
        }
        // Đồng bộ trạng thái của Check All
        checkAll.checked = (checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0);
    }

    checkAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkAction();
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkAction);
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
