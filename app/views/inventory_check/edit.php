<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $check */
/** @var array $details */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><a href="index.php?action=inventory_check_list" style="text-decoration:none; color:#637381;">←</a> Sửa Phiếu Kiểm Kho: <span style="color:#0088ff;">#CHK<?php echo $check['id']; ?></span></h2>
</div>

<div style="background:#fff3cd; color:#856404; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffeeba;">
    ⚠️ <b>Lưu ý kế toán:</b> Phiếu kiểm này đã được Cân bằng kho. Bạn chỉ có thể sửa thông tin ghi chú, không thể sửa số lượng để tránh sai lệch tồn kho.
</div>

<form action="index.php?action=edit_inventory_check&id=<?php echo $check['id']; ?>" method="POST">
    <div style="display: flex; gap: 20px;">
        <div style="flex: 0 0 70%;">
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
                <div style="font-weight: bold; margin-bottom: 15px; color: #212b36;">Chi tiết sản phẩm đã kiểm</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                            <th style="padding: 12px; text-align: left; color: #637381;">Sản phẩm</th>
                            <th style="padding: 12px; text-align: center; color: #637381;">Tồn chi nhánh</th>
                            <th style="padding: 12px; text-align: center; color: #637381;">Tồn thực tế</th>
                            <th style="padding: 12px; text-align: center; color: #637381;">Chênh lệch</th>
                            <th style="padding: 12px; text-align: left; color: #637381;">Lý do</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $item): ?>
                            <tr style="border-bottom: 1px solid #f4f6f8;">
                                <td style="padding: 12px; font-weight: 500; color: #0088ff;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td style="padding: 12px; text-align: center;"><?php echo $item['system_stock']; ?></td>
                                <td style="padding: 12px; text-align: center; font-weight: bold;"><?php echo $item['actual_stock']; ?></td>
                                <td style="padding: 12px; text-align: center;">
                                    <?php if ($item['difference'] > 0): ?>
                                        <span style="color: #108043; font-weight: bold;">+<?php echo $item['difference']; ?></span>
                                    <?php elseif ($item['difference'] < 0): ?>
                                        <span style="color: #cf1322; font-weight: bold;"><?php echo $item['difference']; ?></span>
                                    <?php else: ?>
                                        0
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($item['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="flex: 0 0 calc(30% - 20px);">
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
                <div style="font-weight: bold; margin-bottom: 15px;">Thông tin cập nhật</div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #637381; font-size: 13px;">Nhân viên kiểm</label>
                    <input type="text" name="employee" value="<?php echo htmlspecialchars($check['employee']); ?>" style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #637381; font-size: 13px;">Ghi chú</label>
                    <textarea name="note" rows="4" style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px; margin-top: 5px;"><?php echo htmlspecialchars($check['note']); ?></textarea>
                </div>
                <button type="submit" style="width: 100%; padding: 10px; background: #0088ff; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    💾 Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
