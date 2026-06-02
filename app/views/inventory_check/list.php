<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $checks */ ?>

<div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
    <h2>Danh sách Phiếu kiểm kho</h2>
    <a href="index.php?action=add_inventory_check" style="background:#0088ff; color:white; padding:8px 16px; border-radius:4px; text-decoration:none;">+ Tạo phiếu kiểm</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px;">
        ✅ Cân bằng kho thành công! Số lượng đã được cập nhật.
    </div>
<?php endif; ?>
<?php if (isset($_GET['success_edit'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px;">
        ✅ Đã cập nhật thông tin phiếu kiểm!
    </div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px;">
        🗑️ Đã xóa phiếu kiểm và hoàn lại tồn kho thành công!
    </div>
<?php endif; ?>

<div class="card" style="background:#fff; padding:20px; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <tr style="border-bottom: 1px solid #dfe3e8; background: #fafbfc;">
            <th style="padding: 12px; color: #637381;">Mã phiếu</th>
            <th style="padding: 12px; color: #637381;">Ngày kiểm</th>
            <th style="padding: 12px; color: #637381;">Nhân viên</th>
            <th style="padding: 12px; color: #637381;">Ghi chú</th>
            <th style="padding: 12px; color: #637381;">Trạng thái</th>
            <th style="padding: 12px; color: #637381; text-align: center;">Thao tác</th>
        </tr>
        <?php foreach ($checks as $c): ?>
            <tr style="border-bottom: 1px solid #f4f6f8;">
                <td style="padding: 12px; color:#0088ff; font-weight: bold;">#CHK<?php echo $c['id']; ?></td>
                <td style="padding: 12px;"><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($c['employee']); ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($c['note']); ?></td>
                <td style="padding: 12px;">
                    <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:4px; font-size:12px; border: 1px solid #8ce09f;">Đã cân bằng</span>
                </td>
                <td style="padding: 12px; text-align: center;">
                    <a href="index.php?action=edit_inventory_check&id=<?php echo $c['id']; ?>" style="color: #0088ff; text-decoration: none; margin-right: 10px;">✏️ Sửa</a>
                    <a href="index.php?action=delete_inventory_check&id=<?php echo $c['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa phiếu kiểm kho này? Tồn kho sẽ được tính toán hoàn lại như cũ.');" style="color: #ff4d4f; text-decoration: none;">🗑️ Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
