<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $promo */
/** @var array $applied_orders */
?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 10px;
    }

    .data-row {
        display: flex;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .data-label {
        flex: 0 0 30%;
        color: #637381;
    }

    .data-value {
        flex: 1;
        color: #212b36;
        font-weight: 500;
    }

    .btn-action {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid #c4cdd5;
        background: #fff;
        color: #212b36;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-action:hover {
        background: #f4f6f8;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>
        <a href="index.php?action=promo_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Chi tiết Khuyến mại: <span style="color: #0088ff;"><?php echo htmlspecialchars($promo['promo_name']); ?></span>
    </h2>

    <div style="display: flex; gap: 10px;">
        <a href="#" class="btn-action" onclick="alert('File Excel đã được xuất và tải về máy!')">🖨️ Xuất file</a>
        <a href="index.php?action=copy_promo&id=<?php echo $promo['id']; ?>" class="btn-action">📄 Sao chép</a>
        <a href="index.php?action=edit_promo&id=<?php echo $promo['id']; ?>" class="btn-action">✏️ Sửa</a>

        <?php if ($promo['status'] == 'Đang chạy'): ?>
            <form action="index.php?action=bulk_action_promo" method="POST" style="margin:0;">
                <input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>">
                <button type="submit" name="action" value="Tạm dừng" class="btn-action" style="color:#cf1322;">⏸ Tạm dừng</button>
            </form>
        <?php endif; ?>

        <form action="index.php?action=bulk_action_promo" method="POST" style="margin:0;" onsubmit="return confirm('Xóa vĩnh viễn chương trình này?');">
            <input type="hidden" name="promo_ids[]" value="<?php echo $promo['id']; ?>">
            <button type="submit" name="action" value="delete" class="btn-action" style="color:#cf1322; border-color:#ffa39e;">🗑️ Xóa</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['success_edit'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật chương trình thành công!</div><?php endif; ?>

<div style="display: flex; gap: 20px; align-items: flex-start;">
    <div style="flex: 0 0 65%;">
        <div class="sapo-card">
            <div class="sapo-card-title">Thông tin chung</div>
            <div class="data-row">
                <div class="data-label">Tên chương trình:</div>
                <div class="data-value" style="color: #0088ff;"><?php echo htmlspecialchars($promo['promo_name']); ?></div>
            </div>
            <div class="data-row">
                <div class="data-label">Mã khuyến mại:</div>
                <div class="data-value"><?php echo !empty($promo['promo_code']) ? '<span style="background:#f4f6f8; padding:2px 8px; border:1px dashed #c4cdd5; border-radius:4px; font-family:monospace;">🎟️ ' . htmlspecialchars($promo['promo_code']) . '</span>' : '<span style="color:#108043;">Tự động áp dụng</span>'; ?></div>
            </div>
            <div class="data-row">
                <div class="data-label">Phương thức:</div>
                <div class="data-value">
                    <?php
                    if ($promo['promo_type'] == 'discount_order') echo 'Giảm giá đơn hàng';
                    elseif ($promo['promo_type'] == 'discount_product') echo 'Giảm giá sản phẩm';
                    else echo 'Tặng quà';
                    ?>
                </div>
            </div>
            <div class="data-row">
                <div class="data-label">Số lượng áp dụng:</div>
                <div class="data-value"><?php echo empty($promo['usage_limit']) ? 'Không giới hạn' : $promo['usage_limit'] . ' lần'; ?></div>
            </div>
            <div class="data-row">
                <div class="data-label">Trạng thái:</div>
                <div class="data-value">
                    <?php
                    if ($promo['status'] == 'Đang chạy') echo '<span style="color:#108043; font-weight:bold;">● Đang chạy</span>';
                    elseif ($promo['status'] == 'Tạm dừng') echo '<span style="color:#cf1322; font-weight:bold;">⏸ Tạm dừng</span>';
                    else echo '<span style="color:#637381; font-weight:bold;">○ ' . $promo['status'] . '</span>';
                    ?>
                </div>
            </div>
        </div>

        <div class="sapo-card" style="padding: 0;">
            <div class="sapo-card-title" style="padding: 20px 20px 10px 20px; border-bottom: none; margin-bottom:0;">Kết quả chương trình khuyến mại</div>
            <div style="padding: 0 20px 20px 20px; font-size: 13px; color: #637381;">Danh sách các đơn hàng đã áp dụng thành công mã/chương trình này.</div>

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
                            <td colspan="4" style="text-align: center; padding: 40px; color: #637381;">Chưa có đơn hàng nào áp dụng khuyến mại này.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($applied_orders as $o): ?>
                            <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;">
                                <td style="padding: 15px 20px;"><a href="#" style="color:#0088ff; text-decoration:none; font-weight:bold;"><?php echo $o['order_code']; ?></a></td>
                                <td style="padding: 15px 20px;"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                                <td style="padding: 15px 20px;"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                                <td style="padding: 15px 20px; text-align:right; font-weight:bold; color:#108043;">- <?php echo number_format($o['discount_amount'], 0, ',', '.'); ?> ₫</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="flex: 1;">
        <div class="sapo-card">
            <div class="sapo-card-title">Thông tin áp dụng</div>
            <div class="data-row" style="flex-direction: column; margin-bottom: 15px;">
                <div class="data-label" style="margin-bottom: 5px;">Thời gian diễn ra:</div>
                <div class="data-value" style="background:#fafbfc; padding:10px; border-radius:4px; border:1px solid #dfe3e8; font-size:13px;">
                    Từ: <?php echo date('d/m/Y H:i', strtotime($promo['start_date'])); ?><br>
                    Đến: <?php echo ($promo['no_end_date']) ? 'Không giới hạn' : date('d/m/Y H:i', strtotime($promo['end_date'])); ?>
                </div>
            </div>
            <div class="data-row" style="flex-direction: column; margin-bottom: 15px;">
                <div class="data-label" style="margin-bottom: 5px;">Phạm vi áp dụng:</div>
                <div class="data-value">
                    ✓ Chi nhánh: <?php echo ($promo['branch_scope'] == 'all') ? 'Tất cả chi nhánh' : 'Chi nhánh được chỉ định'; ?><br>
                    ✓ Khách hàng: <?php echo ($promo['customer_scope'] == 'all') ? 'Toàn bộ khách hàng' : 'Nhóm khách hàng cụ thể'; ?>
                </div>
            </div>

            <?php if ($promo['status'] == 'Đang chạy'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #fff8ea; border: 1px solid #ffea8a; border-radius: 6px; font-size: 13px; color: #8a6100;">
                    💡 <b>Lưu ý:</b> Chương trình đang chạy. Bạn chỉ có thể sửa Ngày kết thúc và Số lượng áp dụng. Nếu muốn thay đổi thông tin khác, vui lòng Tạm dừng hoặc Tạo chương trình mới.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
