<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $orders */ ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách đơn đặt hàng nhập</h2>
    <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight:600;">+ Tạo đơn đặt hàng</a>

</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thao tác đơn hàng thành công!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px; min-height: 400px;">
    <?php if (!empty($orders)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Mã đơn</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Ngày hẹn giao</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Nhà cung cấp</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Trạng thái</th>
                    <th style="padding: 12px; text-align: right; color: #637381; font-weight: 500;">Tổng tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 12px; font-weight: 500;">
                            <a href="index.php?action=view_purchase&id=<?php echo $order['id']; ?>" style="color: #0088ff; text-decoration: none;">
                                #PON<?php echo $order['id']; ?>
                            </a>
                        </td>
                        <td style="padding: 12px;"><?php echo date('d/m/Y', strtotime($order['expected_date'])); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                        <td style="padding: 12px;">
                            <?php if ($order['status'] == 'Chờ nhập'): ?>
                                <span style="background: #e6f7ff; color: #0088ff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Chờ nhập</span>
                            <?php elseif ($order['status'] == 'Đã hủy'): ?>
                                <span style="background: #fff1f0; color: #f5222d; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Đã hủy</span>
                            <?php else: ?>
                                <span style="background: #f4f6f8; color: #637381; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Đơn nháp</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 500; color: #212b36;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>₫</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 20px;">
            <div style="font-size: 50px; margin-bottom: 15px;">📋</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có đơn đặt hàng nhập nào</h3>
            <p style="color: #637381; margin-bottom: 20px;">Tạo đơn đặt hàng nhập để bắt đầu quy trình nhập hàng vào kho.</p>
            <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tạo đơn đặt hàng</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
