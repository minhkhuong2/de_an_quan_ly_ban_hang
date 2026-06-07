<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php 
/** @var array|null $customers */ 
// Đảm bảo $customers luôn là mảng, chống lỗi null
$safe_customers = is_array($customers) ? $customers : [];
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách khách hàng</h2>
    <a href="index.php?action=add_customer" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm khách hàng</a>
</div>

<?php if (isset($_GET['success_delete'])): ?>
    <div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e; font-weight:500;">🗑️ Đã xóa khách hàng thành công!</div>
<?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 400px;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px; padding: 15px; border-bottom: 1px solid #dfe3e8; background: #fafbfc;">
        <input type="hidden" name="action" value="customer_list">
        <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm theo tên, mã KH, SĐT..." style="flex:1; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none;">
        <button type="submit" style="padding: 8px 16px; background: #fff; border: 1px solid #c4cdd5; border-radius: 4px; cursor: pointer;">Lọc</button>
    </form>

    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #fff; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                <th style="padding: 12px 15px;">Mã KH</th>
                <th style="padding: 12px 15px;">Tên khách hàng</th>
                <th style="padding: 12px 15px;">Điện thoại</th>
                <th style="padding: 12px 15px; text-align: right;">Công nợ</th>
                <th style="padding: 12px 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($safe_customers)): ?>
                <?php foreach ($safe_customers as $c): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;"> 
                        <td style="padding: 12px 15px; color:#0088ff; font-weight: bold;"><?php echo htmlspecialchars($c['customer_code'] ?? ''); ?></td>
                        <td style="padding: 12px 15px; font-weight: 500;">
                            <?php echo htmlspecialchars(trim(($c['last_name'] ?? '') . ' ' . ($c['first_name'] ?? ''))); ?>
                        </td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($c['phone'] ?? ''); ?></td>
                        <td style="padding: 12px 15px; text-align: right; font-weight: bold; color: <?php echo ($c['debt'] ?? 0) > 0 ? '#cf1322' : '#108043'; ?>;">
                            <?php echo number_format($c['debt'] ?? 0, 0, ',', '.'); ?> ₫
                        </td>
                        <td style="padding: 12px 15px; text-align: center;">
                            <a href="index.php?action=edit_customer&id=<?php echo $c['id'] ?? 0; ?>" style="text-decoration: none; margin-right: 10px;" title="Sửa">✏️</a>
                            <a href="index.php?action=delete_customer&id=<?php echo $c['id'] ?? 0; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');" style="text-decoration: none;" title="Xóa">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #637381;">Chưa có dữ liệu khách hàng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
