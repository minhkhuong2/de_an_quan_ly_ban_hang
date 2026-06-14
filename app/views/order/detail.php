<?php

/** @var array $order */
/** @var array $items */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    /* CSS CHUẨN SAPO OMNIAI V3 */
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 22px;
        font-weight: bold;
        color: #212b36;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .v3-title a {
        color: #637381;
        text-decoration: none;
        font-size: 24px;
        margin-top: -4px;
    }

    .pos-layout {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .pos-left {
        flex: 0 0 68%;
    }

    .pos-right {
        flex: 1;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dfe3e8;
    }

    .v3-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-table th {
        text-align: left;
        padding: 10px;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #dfe3e8;
    }

    .cart-table td {
        padding: 15px 10px;
        border-bottom: 1px solid #dfe3e8;
        vertical-align: middle;
        font-size: 14px;
        color: #212b36;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #212b36;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #dfe3e8;
        font-size: 18px;
        font-weight: 700;
        color: #d82c0d;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .badge-paid {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .badge-pending {
        background: #fff8ea;
        color: #8a6100;
        border: 1px solid #ffea8a;
    }

    .badge-completed {
        background: #f4f6f8;
        color: #637381;
        border: 1px solid #c4cdd5;
    }

    .customer-info {
        font-size: 14px;
        line-height: 1.6;
        color: #212b36;
    }

    .customer-info span {
        color: #0088ff;
        font-weight: 500;
    }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=order_list">←</a>
        <?php echo htmlspecialchars($order['order_code']); ?>
        <span style="font-size: 14px; font-weight: normal; color: #637381; margin-left: 10px;">
            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
        </span>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" style="padding: 8px 15px; border: 1px solid #c4cdd5; background: #fff; border-radius: 4px; cursor: pointer;" onclick="window.open('index.php?action=print_order&id=<?php echo $order['id']; ?>', '_blank')">🖨️ In đơn hàng</button>
    </div>
</div>

<div class="pos-layout">
    <div class="pos-left">
        <div class="v3-card" style="padding: 15px 20px;">
            <div class="v3-card" style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 15px;">
                        <?php if ($order['order_status'] == 'cancelled'): ?>
                            <span class="badge" style="background:#ffe4e4; color:#d82c0d; border:1px solid #ffb8b8;">❌ Đã hủy</span>
                        <?php elseif ($order['shipping_status'] == 'delivered'): ?>
                            <span class="badge badge-paid">🚚 Đã giao hàng</span>
                        <?php else: ?>
                            <span class="badge badge-pending">⏳ Chưa giao hàng</span>
                        <?php endif; ?>

                        <?php if ($order['payment_status'] == 'paid'): ?>
                            <span class="badge badge-paid">💰 Đã thanh toán</span>
                        <?php else: ?>
                            <span class="badge badge-pending">🛑 Chưa thanh toán</span>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <?php if ($order['shipping_status'] !== 'delivered'): ?>
                            <button type="button" class="btn-primary" id="btn_ship_action" style="width:auto; padding:8px 15px; background:#108043;" onclick="processShipping(<?php echo $order['id']; ?>)">🚀 Xác nhận xuất kho & Giao hàng</button>
                        <?php endif; ?>

                        <?php if ($order['payment_status'] !== 'paid'): ?>
                            <button type="button" class="btn-primary" id="btn_pay_action" style="width:auto; padding:8px 15px; background:#e67e22;" onclick="processPayment(<?php echo $order['id']; ?>)">💵 Xác nhận thu tiền khách nợ</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="v3-card-title">Chi tiết sản phẩm</div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Sản phẩm</th>
                        <th style="width: 15%; text-align: center;">Số lượng</th>
                        <th style="width: 15%; text-align: right;">Đơn giá</th>
                        <th style="width: 20%; text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div style="font-weight:600; color:#0088ff;">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                    <?php if ($item['is_gift'] == 1) echo '<span style="background:#ffea8a; color:#8a6100; font-size:11px; padding:2px 6px; border-radius:4px; margin-left:8px;">🎁 Quà tặng</span>'; ?>
                                </div>
                                <div style="font-size:12px; color:#637381;">Mã: <?php echo $item['sku'] ?? '---'; ?></div>
                            </td>
                            <td style="text-align:center;"><?php echo $item['qty']; ?></td>
                            <td style="text-align:right;">
                                <?php if ($item['final_price'] < $item['original_price']): ?>
                                    <div style="text-decoration:line-through; color:#8c98a4; font-size:12px;"><?php echo number_format($item['original_price'], 0, '', '.'); ?> ₫</div>
                                <?php endif; ?>
                                <div style="font-weight:500;"><?php echo number_format($item['final_price'], 0, '', '.'); ?> ₫</div>
                            </td>
                            <td style="text-align:right; font-weight:600;"><?php echo number_format($item['line_total'], 0, '', '.'); ?> ₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pos-right">

        <div class="v3-card">
            <div class="v3-card-title">Khách hàng</div>
            <div class="customer-info">
                <?php if (!empty($order['customer_name'])): ?>
                    <div style="margin-bottom: 8px;">👤 <span><?php echo htmlspecialchars($order['customer_name']); ?></span></div>
                    <div style="margin-bottom: 8px;">📞 <?php echo htmlspecialchars($order['phone'] ?? 'Chưa cập nhật'); ?></div>
                    <div>📍 <?php echo htmlspecialchars($order['address'] ?? 'Chưa cập nhật địa chỉ'); ?></div>
                <?php else: ?>
                    <div style="color: #8c98a4; font-style: italic;">Khách lẻ (Không lưu thông tin)</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="v3-card" style="background: #f4f6f8; border: none;">
            <div class="v3-card-title">Thanh toán</div>

            <div class="summary-line">
                <span>Tổng tiền hàng</span>
                <span><?php echo number_format($order['subtotal'], 0, '', '.'); ?> ₫</span>
            </div>

            <?php
            $total_discount = $order['total_product_discount'] + $order['total_order_discount'];
            if ($total_discount > 0):
            ?>
                <div class="summary-line" style="color: #108043;">
                    <span>Chiết khấu</span>
                    <span>-<?php echo number_format($total_discount, 0, '', '.'); ?> ₫</span>
                </div>
            <?php endif; ?>

            <div class="summary-line">
                <span>Phí giao hàng</span>
                <span><?php echo number_format($order['original_shipping_fee'] - $order['total_shipping_discount'], 0, '', '.'); ?> ₫</span>
            </div>

            <div class="summary-total">
                <span>Khách phải trả</span>
                <span><?php echo number_format($order['grand_total'], 0, '', '.'); ?> ₫</span>
            </div>

            <div class="summary-line" style="margin-top: 15px; border-top: 1px dashed #c4cdd5; padding-top: 15px;">
                <span style="font-weight: 500;">Đã thanh toán</span>
                <span style="font-weight: 500; color: #108043;"><?php echo number_format($order['amount_paid'] ?? 0, 0, '', '.'); ?> ₫</span>
            </div>
        </div>

    </div>
</div>
<script>
    // Lệnh AJAX xử lý xuất kho giao hàng
    function processShipping(orderId) {
        if (!confirm('Xác nhận xuất kho và chuyển trạng thái đơn hàng thành ĐÃ GIAO HÀNG? Hệ thống sẽ tự động trừ số lượng tồn kho sản phẩm!')) return;

        document.getElementById('btn_ship_action').disabled = true;

        fetch('index.php?action=update_order_ship', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: 'delivered'
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }

    // Lệnh AJAX xác nhận thu tiền khách
    function processPayment(orderId) {
        if (!confirm('Xác nhận đã thu đủ số tiền khách nợ cho đơn hàng này?')) return;

        document.getElementById('btn_pay_action').disabled = true;

        fetch('index.php?action=collect_order_pay', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }
    // Lệnh AJAX xử lý Hủy đơn hàng
    function processCancel(orderId) {
        if (!confirm('⚠️ BẠN CÓ CHẮC CHẮN MUỐN HỦY ĐƠN HÀNG NÀY KHÔNG?\n\nNếu đơn đã xuất kho, hệ thống sẽ tự động hoàn trả số lượng lại vào kho.')) return;

        fetch('index.php?action=cancel_order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
