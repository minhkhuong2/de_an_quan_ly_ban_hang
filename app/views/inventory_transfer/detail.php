<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $transfer */
/** @var array $details */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>
        <a href="index.php?action=transfer_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Chi tiết Phiếu Chuyển Kho: <span style="color: #0088ff;">#TRN<?php echo $transfer['id']; ?></span>
    </h2>

    <div style="display: flex; gap: 10px;">
        <?php if ($transfer['status'] == 'Phiếu nháp'): ?>
            <a href="index.php?action=update_transfer&id=<?php echo $transfer['id']; ?>&status=start" class="btn" style="background:#0088ff; color:white; padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:bold;">🚛 Xác nhận Chuyển hàng</a>
        <?php elseif ($transfer['status'] == 'Đang chuyển'): ?>
            <a href="index.php?action=update_transfer&id=<?php echo $transfer['id']; ?>&status=receive" class="btn" style="background:#108043; color:white; padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:bold;">✅ Nhận hàng vào kho</a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px;">✅ Đã tạo phiếu chuyển kho thành công!</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px;">✅ Cập nhật trạng thái phiếu thành công!</div><?php endif; ?>

<div style="display: flex; gap: 20px;">
    <div style="flex: 0 0 68%; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="font-size: 16px; margin-bottom: 15px;">Sản phẩm điều chuyển</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                <th style="padding: 12px; text-align: left;">Sản phẩm</th>
                <th style="padding: 12px; text-align: center;">Số lượng chuyển</th>
            </tr>
            <?php foreach ($details as $item): ?>
                <tr style="border-bottom: 1px solid #f4f6f8;">
                    <td style="padding: 12px; color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td style="padding: 12px; text-align: center; font-weight: bold;"><?php echo $item['quantity']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="flex: 0 0 calc(32% - 20px);">
        <div style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 16px; margin-bottom: 15px;">Thông tin vận chuyển</h3>

            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between;">
                <span style="color: #637381;">Trạng thái:</span>
                <?php if ($transfer['status'] == 'Phiếu nháp'): ?>
                    <span style="background:#f4f6f8; color:#637381; padding:4px 8px; border-radius:4px; font-weight:bold;">Phiếu nháp</span>
                <?php elseif ($transfer['status'] == 'Đang chuyển'): ?>
                    <span style="background:#e6f7ff; color:#0088ff; padding:4px 8px; border-radius:4px; font-weight:bold;">Đang chuyển</span>
                <?php else: ?>
                    <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:4px; font-weight:bold;">Đã nhận hàng</span>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 15px;">
                <span style="color: #637381; display: block; font-size: 12px;">Từ chi nhánh:</span>
                <strong style="color: #212b36; font-size: 15px;">🏠 <?php echo htmlspecialchars($transfer['from_branch']); ?></strong>
            </div>

            <div style="margin-bottom: 15px;">
                <span style="color: #637381; display: block; font-size: 12px;">Đến chi nhánh:</span>
                <strong style="color: #212b36; font-size: 15px;">🏢 <?php echo htmlspecialchars($transfer['to_branch']); ?></strong>
            </div>

            <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 15px 0;">

            <div style="margin-bottom: 10px;">
                <span style="color: #637381; font-size: 13px;">Ghi chú:</span>
                <div style="font-weight: 500; font-size: 14px; margin-top: 5px;"><?php echo htmlspecialchars($transfer['note']); ?></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
