<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách sản phẩm</h2>
    <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm sản phẩm</a>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #f4f6f8;">
                <th style="padding: 12px; text-align: left; color: #637381;">Mã máy (ID)</th>
                <th style="padding: 12px; text-align: left; color: #637381;">Ảnh</th>
                <th style="padding: 12px; text-align: left; color: #637381;">Tên sản phẩm</th>
                <th style="padding: 12px; text-align: left; color: #637381;">Hãng (Brand)</th>
                <th style="padding: 12px; text-align: left; color: #637381;">Tồn kho (IMEI)</th>
                <th style="padding: 12px; text-align: left; color: #637381;">Giá bán lẻ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 12px;"><strong>MAC-<?php echo $row['id']; ?></strong></td>
                        <td style="padding: 12px;">
                            <div style="width:40px; height:40px; background:#e9ecef; border-radius:4px; text-align:center; line-height:40px; font-size: 20px;">📱</div>
                        </td>
                        <td style="padding: 12px; color: #0088ff; font-weight: 500;"><?php echo $row['product_name']; ?></td>
                        <td style="padding: 12px;"><?php echo $row['brand']; ?></td>
                        <td style="padding: 12px;">
                            <span style="color: <?php echo (isset($row['ton_kho']) && $row['ton_kho'] > 0) ? '#52c41a' : '#ff4d4f'; ?>; font-weight:bold;">
                                <?php echo isset($row['ton_kho']) ? $row['ton_kho'] : '0'; ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo number_format($row['base_price'], 0, ',', '.'); ?> ₫</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #637381;">
                        Chưa có sản phẩm nào. Hãy bấm "Thêm sản phẩm" nhé!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
