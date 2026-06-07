<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array|null $partners */
$safe_partners = is_array($partners) ? $partners : [];
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Đối tác vận chuyển (Sapo Express)</h2>
    <a href="index.php?action=add_shipping" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm ĐVVC mới</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Lưu cấu hình vận chuyển thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">🗑️ Đã xóa Đơn vị vận chuyển!</div><?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #fafbfc; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                <th style="padding: 15px;">Đơn vị vận chuyển</th>
                <th style="padding: 15px;">Mã ĐVVC</th>
                <th style="padding: 15px;">Phí giao hàng chuẩn</th>
                <th style="padding: 15px; text-align: center;">Hỗ trợ COD</th>
                <th style="padding: 15px;">Trạng thái</th>
                <th style="padding: 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($safe_partners as $p): ?>
                <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;">
                    <td style="padding: 15px; font-weight: bold; color: #0088ff;">📦 <?php echo htmlspecialchars($p['partner_name'] ?? ''); ?></td>
                    <td style="padding: 15px; font-weight: 500;"><?php echo htmlspecialchars($p['partner_code'] ?? ''); ?></td>
                    <td style="padding: 15px; font-weight: bold;"><?php echo number_format($p['base_fee'] ?? 0, 0, ',', '.'); ?> ₫</td>
                    <td style="padding: 15px; text-align: center;">
                        <?php echo (!empty($p['allow_cod'])) ? '<span style="color:#108043;">✅ Có</span>' : '<span style="color:#cf1322;">❌ Không</span>'; ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if (($p['status'] ?? '') == 'Đang kết nối'): ?>
                            <span style="color:#108043; background:#eafff0; padding:4px 8px; border-radius:4px; font-weight:500;">● Đang kết nối</span>
                        <?php else: ?>
                            <span style="color:#cf1322; background:#fff1f0; padding:4px 8px; border-radius:4px; font-weight:500;">○ Ngừng kết nối</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="index.php?action=edit_shipping&id=<?php echo $p['id']; ?>" style="text-decoration:none; margin-right:10px;" title="Cấu hình">⚙️ Cấu hình</a>
                        <a href="index.php?action=delete_shipping&id=<?php echo $p['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn ngắt kết nối và xóa ĐVVC này?');" style="color:#cf1322; text-decoration:none;" title="Xóa">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
