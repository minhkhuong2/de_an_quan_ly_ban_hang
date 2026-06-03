<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $order */
/** @var array $details */
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><a href="index.php?action=view_purchase&id=<?php echo $order['id']; ?>" style="text-decoration:none; color:#637381;">←</a> Hoàn trả hàng cho phiếu nhập: <span style="color:#0088ff;">#PN<?php echo $order['id']; ?></span></h2>
</div>

<?php if (isset($_GET['error'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px;">Vui lòng nhập số lượng > 0 cho ít nhất 1 sản phẩm cần trả.</div><?php endif; ?>

<form action="index.php?action=process_purchase_return" method="POST">
    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
    <input type="hidden" name="supplier_name" value="<?php echo htmlspecialchars($order['supplier_name']); ?>">

    <div style="display: flex; gap: 20px;">
        <div style="flex: 0 0 70%; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px;">Sản phẩm hoàn trả</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 10px;">Sản phẩm</th>
                    <th style="padding: 10px; text-align: center;">SL Đã Nhập</th>
                    <th style="padding: 10px; text-align: center;">Đơn giá</th>
                    <th style="padding: 10px; text-align: center;">SL Hoàn trả</th>
                </tr>
                <?php foreach ($details as $item): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 10px; color:#0088ff; font-weight: 500;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="padding: 10px; text-align: center; font-weight: bold;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 10px; text-align: center;">
                            <?php echo number_format($item['unit_price'], 0, ',', '.'); ?>
                            <input type="hidden" name="price[]" value="<?php echo $item['unit_price']; ?>">
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
                            <input type="number" name="return_qty[]" value="0" min="0" max="<?php echo $item['quantity']; ?>" style="width: 80px; padding: 6px; text-align: center; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: bold; color: #cf1322;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="flex: 0 0 calc(30% - 20px); background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px;">Thông tin bổ sung</h3>
            <div style="margin-bottom: 15px;">
                <label style="color:#637381; font-size:13px; display:block; margin-bottom:5px;">Chi nhánh trả</label>
                <select name="branch" style="width:100%; padding:8px; border:1px solid #c4cdd5; border-radius:4px;">
                    <option>Cửa hàng chính</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="color:#637381; font-size:13px; display:block; margin-bottom:5px;">Lý do hoàn trả</label>
                <textarea name="reason" rows="3" style="width:100%; padding:8px; border:1px solid #c4cdd5; border-radius:4px;" placeholder="Ví dụ: Hàng lỗi, sai mẫu mã..."></textarea>
            </div>

            <button type="submit" style="width: 100%; padding: 10px; background: #ff9900; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px;">
                Tạo phiếu Trả Hàng & Trừ Kho
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
