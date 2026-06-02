<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $orders */ ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách đơn đặt hàng nhập</h2>
    <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500; box-shadow: 0 2px 4px rgba(0,136,255,0.2);">+ Tạo đơn đặt hàng</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thao tác đơn hàng thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">🗑️ Đã xóa vĩnh viễn đơn đặt hàng và hoàn trả lại số lượng kho!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); padding: 20px; min-height: 400px;">
    <?php if (!empty($orders)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px 15px; text-align: left; color: #212b36; font-weight: 600;">Mã đơn</th>
                    <th style="padding: 12px 15px; text-align: left; color: #212b36; font-weight: 600;">Ngày hẹn giao</th>
                    <th style="padding: 12px 15px; text-align: left; color: #212b36; font-weight: 600;">Nhà cung cấp</th>
                    <th style="padding: 12px 15px; text-align: left; color: #212b36; font-weight: 600;">Trạng thái</th>
                    <th style="padding: 12px 15px; text-align: right; color: #212b36; font-weight: 600;">Tổng tiền</th>
                    <th style="padding: 12px 15px; text-align: center; color: #212b36; font-weight: 600;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 15px; font-weight: bold;">
                            <a href="index.php?action=view_purchase&id=<?php echo $order['id']; ?>" style="color: #0088ff; text-decoration: none;">
                                #PON<?php echo $order['id']; ?>
                            </a>
                        </td>
                        <td style="padding: 15px; color: #637381;"><?php echo date('d/m/Y', strtotime($order['expected_date'])); ?></td>
                        <td style="padding: 15px; font-weight: 500; color: #212b36;"><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                        <td style="padding: 15px;">
                            <?php if ($order['status'] == 'Chờ nhập'): ?>
                                <span style="background: #e6f7ff; color: #0088ff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Chờ nhập</span>
                            <?php elseif ($order['status'] == 'Đã hủy'): ?>
                                <span style="background: #fff1f0; color: #cf1322; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Đã hủy</span>
                            <?php elseif ($order['status'] == 'Nhập toàn bộ'): ?>
                                <span style="background: #eafff0; color: #108043; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Nhập toàn bộ</span>
                            <?php else: ?>
                                <span style="background: #f4f6f8; color: #637381; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">Đơn nháp</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; text-align: right; font-weight: bold; color: #212b36;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</td>
                        <td style="padding: 15px; text-align: center;">
                            <?php if (in_array($order['status'], ['Đơn nháp', 'Chờ nhập'])): ?>
                                <a href="index.php?action=edit_purchase&id=<?php echo $order['id']; ?>" style="text-decoration: none; font-size: 16px; margin-right: 12px;" title="Sửa đơn">✏️</a>
                                <a href="index.php?action=delete_purchase&id=<?php echo $order['id']; ?>" onclick="return confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn xóa đơn đặt hàng này không?');" style="text-decoration: none; font-size: 16px;" title="Xóa đơn">🗑️</a>
                            <?php else: ?>
                                <span style="color: #c4cdd5; font-size: 12px;">Không thể xóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 50px; margin-bottom: 15px;">📋</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có đơn đặt hàng nhập nào</h3>
            <p style="color: #637381; margin-bottom: 20px;">Tạo đơn đặt hàng nhập để bắt đầu quy trình nhập hàng vào kho.</p>
            <a href="index.php?action=add_purchase" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tạo đơn đặt hàng</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
