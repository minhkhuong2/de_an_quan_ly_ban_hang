<!-- Đường dẫn: app/views/purchase_order/receive.php -->
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $order */
/** @var array $details */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>
        <a href="index.php?action=view_purchase&id=<?php echo $order['id']; ?>" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Nhập hàng cho đơn: <span style="color: #0088ff;">#PON<?php echo $order['id']; ?></span>
    </h2>
</div>

<form action="index.php?action=process_receive" method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <!-- CỘT TRÁI: ĐIỀU CHỈNH SỐ LƯỢNG NHẬP -->
        <div style="flex: 0 0 70%; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
            <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px; color: #212b36;">Sản phẩm nhập hàng</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                        <th style="padding: 12px; text-align: left; color: #637381;">Sản phẩm</th>
                        <th style="padding: 12px; text-align: center; color: #637381;">SL Đặt</th>
                        <th style="padding: 12px; text-align: center; color: #212b36; font-weight: bold;">SL Nhận thực tế</th>
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
                            <td style="padding: 12px; text-align: center; color: #637381; font-weight: bold;">
                                <?php echo $item['quantity']; ?>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <!-- Mặc định điền sẵn số lượng đặt, nhưng cho phép sửa -->
                                <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
                                <input type="number" name="receive_qty[]" value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['quantity']; ?>" style="width: 80px; padding: 6px; text-align: center; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: bold; color: #108043;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- CỘT PHẢI: XÁC NHẬN -->
        <div style="flex: 0 0 calc(30% - 20px); display: flex; flex-direction: column; gap: 20px;">
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
                <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">Xác nhận Nhập hàng</h3>
                <p style="font-size: 13px; color: #637381; margin-bottom: 20px;">
                    Hệ thống sẽ tự động trừ số lượng <b>Đang về kho</b> và cộng vào <b>Tồn kho thực tế</b> dựa trên số lượng bạn xác nhận bên trái.
                </p>
                <button type="submit" style="width: 100%; padding: 12px; background: #0088ff; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                    Nhập hàng vào kho
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
