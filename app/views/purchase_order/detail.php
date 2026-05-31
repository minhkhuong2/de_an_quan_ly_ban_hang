<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $order */
/** @var array $details */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>
        <a href="index.php?action=purchase_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Chi tiết Đơn đặt hàng: <span style="color: #0088ff;">#PON<?php echo $order['id']; ?></span>
    </h2>

    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 16px; border-radius: 4px; cursor: pointer;">🖨️ In đơn</button>

        <?php if (in_array($order['status'], ['Đơn nháp', 'Chờ nhập'])): ?>
            <a href="index.php?action=cancel_purchase&id=<?php echo $order['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng Đang về kho sẽ được hoàn lại.');" style="background: #fff; border: 1px solid #ff4d4f; color: #ff4d4f; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">Hủy đơn</a>
            <?php if ($order['status'] == 'Chờ nhập'): ?>
                <a href="index.php?action=receive_purchase&id=<?php echo $order['id']; ?>" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">Nhập hàng vào kho và sửa thẻ</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['success_cancel'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">✅ Đã hủy đơn đặt hàng và hoàn lại tồn kho thành công!</div><?php endif; ?>

<div style="display: flex; gap: 20px; align-items: flex-start;">
    <div style="flex: 0 0 68%; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
        <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px; color: #212b36;">Sản phẩm đặt hàng</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px; text-align: left; color: #637381;">Sản phẩm</th>
                    <th style="padding: 12px; text-align: center; color: #637381;">Số lượng</th>
                    <th style="padding: 12px; text-align: right; color: #637381;">Đơn giá</th>
                    <th style="padding: 12px; text-align: right; color: #637381;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $item): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo $item['image']; ?>" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width:40px; height:40px; background:#f4f6f8; border-radius:4px; text-align:center; line-height:40px;">📦</div>
                                <?php endif; ?>
                                <div>
                                    <div style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div style="color: #637381; font-size: 12px;">SKU: <?php echo htmlspecialchars($item['sku'] ?? '---'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: bold;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo number_format($item['unit_price'], 0, ',', '.'); ?>₫</td>
                        <td style="padding: 12px; text-align: right; font-weight: 500; color: #212b36;"><?php echo number_format($item['quantity'] * $item['unit_price'], 0, ',', '.'); ?>₫</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 20px; font-size: 16px;">
            Tổng tiền: <strong style="color: #0088ff; font-size: 20px;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</strong>
        </div>
    </div>

    <div style="flex: 0 0 calc(32% - 20px); display: flex; flex-direction: column; gap: 20px;">
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
            <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">Thông tin đơn đặt</h3>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: #637381;">Trạng thái:</span>
                <span style="font-weight: bold; color: <?php echo ($order['status'] == 'Đã hủy') ? '#cf1322' : '#0088ff'; ?>;"><?php echo $order['status']; ?></span>
            </div>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: #637381;">Chi nhánh:</span>
                <span style="font-weight: 500;"><?php echo htmlspecialchars($order['branch']); ?></span>
            </div>
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                <span style="color: #637381;">Ngày hẹn:</span>
                <span style="font-weight: 500;"><?php echo date('d/m/Y', strtotime($order['expected_date'])); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #637381;">Nhân viên:</span>
                <span style="font-weight: 500;"><?php echo htmlspecialchars($order['employee']); ?></span>
            </div>
        </div>

        <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
            <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">Nhà cung cấp</h3>
            <div style="color: #0088ff; font-weight: 500; font-size: 15px;">
                👤 <?php echo htmlspecialchars($order['supplier_name']); ?>
            </div>
        </div>
    </div>
</div>
<!-- KHỐI THANH TOÁN (MỚI BỔ SUNG) -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
    <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">Thanh toán công nợ</h3>

    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
        <span style="color: #637381;">Tổng tiền:</span>
        <strong style="color: #212b36;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</strong>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #108043;">
        <span>Đã trả:</span>
        <strong><?php echo number_format($order['paid_amount'] ?? 0, 0, ',', '.'); ?> ₫</strong>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #cf1322;">
        <span>Còn nợ:</span>
        <strong><?php echo number_format($order['total_amount'] - ($order['paid_amount'] ?? 0), 0, ',', '.'); ?> ₫</strong>
    </div>

    <?php if ($order['total_amount'] > ($order['paid_amount'] ?? 0)): ?>
        <hr style="border: none; border-top: 1px solid #dfe3e8; margin-bottom: 15px;">
        <form action="index.php?action=pay_purchase" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 13px; color: #637381; margin-bottom: 5px;">Số tiền thanh toán đợt này</label>
                <input type="number" name="amount" max="<?php echo $order['total_amount'] - ($order['paid_amount'] ?? 0); ?>" value="<?php echo $order['total_amount'] - ($order['paid_amount'] ?? 0); ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; box-sizing: border-box; font-weight: bold; color: #0088ff;">
            </div>
            <button type="submit" style="width: 100%; padding: 10px; background: #0088ff; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                Xác nhận thanh toán
            </button>
        </form>
    <?php else: ?>
        <div style="text-align: center; padding: 10px; background: #eafff0; color: #108043; border-radius: 4px; font-weight: bold; font-size: 14px; border: 1px solid #8ce09f;">
            ✅ Đã thanh toán đủ
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
