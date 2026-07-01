<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    .v3-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #dfe3e8; margin-bottom: 20px; }
    .card-header { padding: 15px 20px; border-bottom: 1px solid #dfe3e8; background: #fafbfc; font-weight: 600; color: #212b36; display: flex; justify-content: space-between; }
    .card-body { padding: 20px; }
    
    .layout-grid { display: flex; gap: 20px; align-items: flex-start; }
    .col-main { flex: 0 0 65%; display: flex; flex-direction: column; gap: 20px; }
    .col-side { flex: 1; display: flex; flex-direction: column; gap: 20px; }
    
    table { width: 100%; border-collapse: collapse; }
    th { background: #f4f6f8; padding: 10px; color: #637381; font-size: 13px; text-align: left; border-bottom: 1px solid #dfe3e8; }
    td { padding: 15px 10px; border-bottom: 1px solid #dfe3e8; vertical-align: middle; }
    
    .status-badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .status-pending { background: #fff8ea; color: #8a6100; border: 1px solid #ffea8a; }
    .status-completed { background: #eafff0; color: #108043; border: 1px solid #8ce09f; }
    
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #212b36; }
    .summary-row.total { font-weight: bold; font-size: 16px; border-top: 1px solid #dfe3e8; padding-top: 15px; margin-top: 5px; }

    .btn-primary { background: #0088ff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
    .btn-outline { background: #fff; color: #212b36; padding: 10px 20px; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: 600; cursor: pointer; }
    .btn-danger { background: #fff; color: #d82c0d; padding: 10px 20px; border: 1px solid #d82c0d; border-radius: 4px; font-weight: 600; cursor: pointer; }
    .btn-success { background: #108043; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=return_order_list" style="text-decoration: none; color: #637381;">←</a> 
        Chi tiết phiếu trả <?php echo htmlspecialchars($return_order['return_code']); ?>
    </div>
    
    <div style="display: flex; gap: 10px;">
        <?php if ($return_order['receive_status'] === 'pending' && $return_order['refund_status'] === 'pending'): ?>
            <button class="btn-danger" onclick="cancelReturn()">❌ Hủy phiếu trả</button>
        <?php endif; ?>
    </div>
</div>

<div class="layout-grid">
    <div class="col-main">
        <div class="v3-card">
            <div class="card-header">
                <span>1. Sản phẩm khách trả lại</span>
                <span class="status-badge <?php echo $return_order['receive_status'] == 'received' ? 'status-completed' : 'status-pending'; ?>">
                    <?php echo $return_order['receive_status'] == 'received' ? 'Đã nhận hàng (Hoàn kho)' : 'Chưa nhận hàng'; ?>
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL trả</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($return_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div style="font-size: 12px; color: #637381;"><?php echo htmlspecialchars($item['sku']); ?></div>
                                </td>
                                <td style="text-align: center;"><?php echo $item['qty_returned']; ?></td>
                                <td style="text-align: right;"><?php echo number_format($item['return_price'], 0, ',', '.'); ?>đ</td>
                                <td style="text-align: right; font-weight: bold;"><?php echo number_format($item['line_total'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($return_order['receive_status'] === 'pending'): ?>
                <div style="padding: 15px; border-top: 1px solid #dfe3e8; text-align: right;">
                    <button class="btn-primary" onclick="receiveItems()">📦 Nhận hàng & Cộng tồn kho</button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($exchange_items) > 0): ?>
        <div class="v3-card">
            <div class="card-header">2. Sản phẩm đổi mới</div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL lấy</th>
                            <th style="text-align: right;">Đơn giá mới</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exchange_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div style="font-size: 12px; color: #637381;"><?php echo htmlspecialchars($item['sku']); ?></div>
                                </td>
                                <td style="text-align: center;"><?php echo $item['qty']; ?></td>
                                <td style="text-align: right;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                <td style="text-align: right; font-weight: bold;"><?php echo number_format($item['line_total'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-side">
        <div class="v3-card">
            <div class="card-header">
                <span>Trạng thái Tài chính</span>
                <span class="status-badge <?php echo $return_order['refund_status'] == 'refunded' ? 'status-completed' : 'status-pending'; ?>">
                    <?php echo $return_order['refund_status'] == 'refunded' ? 'Đã hoàn tất' : 'Chưa xử lý'; ?>
                </span>
            </div>
            <div class="card-body">
                <div class="summary-row">
                    <span>Tổng hàng trả:</span>
                    <span><?php echo number_format($return_order['total_return_value'], 0, ',', '.'); ?>đ</span>
                </div>
                <div class="summary-row">
                    <span>Tổng hàng đổi mới:</span>
                    <span><?php echo number_format($return_order['total_exchange_value'], 0, ',', '.'); ?>đ</span>
                </div>
                
                <div class="summary-row total">
                    <span>
                        <?php echo $return_order['refund_amount'] >= 0 ? "Cần hoàn khách:" : "Khách trả thêm:"; ?>
                    </span>
                    <span style="color: <?php echo $return_order['refund_amount'] >= 0 ? '#d82c0d' : '#108043'; ?>">
                        <?php echo number_format(abs($return_order['refund_amount']), 0, ',', '.'); ?>đ
                    </span>
                </div>

                <?php if ($return_order['refund_status'] === 'pending' && abs($return_order['refund_amount']) > 0): ?>
                    <div style="margin-top: 20px;">
                        <button class="btn-success" style="width: 100%;" onclick="processRefund()">
                            💰 Xác nhận <?php echo $return_order['refund_amount'] > 0 ? "Hoàn Tiền" : "Thu Thêm"; ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">Thông tin tham chiếu</div>
            <div class="card-body" style="font-size: 14px; color: #212b36; line-height: 1.8;">
                <div>Đơn hàng gốc: <a href="index.php?action=view_order&id=<?php echo $return_order['order_id']; ?>" style="color: #0088ff; text-decoration: none; font-weight: bold;"><?php echo htmlspecialchars($return_order['order_code']); ?></a></div>
                <div>Khách hàng: <strong><?php echo htmlspecialchars($return_order['last_name'] . ' ' . $return_order['first_name']); ?></strong></div>
                <div>Ngày tạo: <?php echo date('d/m/Y H:i', strtotime($return_order['created_at'])); ?></div>
                <?php if ($return_order['note']): ?>
                    <div>Ghi chú: <em style="color: #637381;"><?php echo htmlspecialchars($return_order['note']); ?></em></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function receiveItems() {
        if (!confirm("Bạn có chắc chắn đã nhận hàng và muốn hoàn lại số lượng vào kho?")) return;
        
        fetch('index.php?action=receive_return', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ return_id: <?php echo $return_order['id']; ?> })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Đã nhận hàng và cộng lại tồn kho thành công!");
                location.reload();
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }

    function processRefund() {
        let msg = "<?php echo $return_order['refund_amount'] > 0 ? 'Xác nhận tạo Phiếu Chi hoàn tiền cho khách?' : 'Xác nhận tạo Phiếu Thu thêm tiền của khách?'; ?>";
        if (!confirm(msg)) return;
        
        fetch('index.php?action=refund_return', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ return_id: <?php echo $return_order['id']; ?> })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Đã ghi nhận thanh toán thành công!");
                location.reload();
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }

    function cancelReturn() {
        if (!confirm("Bạn chắc chắn muốn hủy phiếu trả hàng này? Thao tác không thể hoàn tác.")) return;
        
        fetch('index.php?action=cancel_return', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ return_id: <?php echo $return_order['id']; ?> })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Hủy phiếu thành công!");
                window.location.href = 'index.php?action=return_order_list';
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
