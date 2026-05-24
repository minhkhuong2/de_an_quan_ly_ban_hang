<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .sapo-table th,
    .sapo-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
    }

    .sapo-table th {
        background: #fafbfc;
        color: #637381;
        font-weight: 500;
    }

    .badge-status {
        background: #eafff0;
        color: #108043;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách Đơn nhập hàng</h2>
    <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tạo đơn nhập hàng</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Nhập hàng thành công! Tồn kho đã được cập nhật.</div><?php endif; ?>

<?php if (!empty($orders)): ?>
    <table class="sapo-table">
        <thead>
            <tr>
                <th>Mã đơn nhập</th>
                <th>Nhà cung cấp</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Ngày nhập</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $row): ?>
                <tr>
                    <td style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($row['order_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                    <td><span class="badge-status"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td style="font-weight: bold;"><?php echo number_format($row['total_amount']); ?> ₫</td>
                    <td style="color: #637381;"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px;">
        <div style="font-size: 60px; margin-bottom: 15px;">📥</div>
        <h3 style="font-size: 18px; color: #212b36;">Chưa có đơn nhập hàng nào</h3>
        <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Nhập hàng vào kho để bắt đầu theo dõi tồn kho và kinh doanh.</p>
        <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none;">+ Tạo đơn đầu tiên</a>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
