<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    .v3-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #dfe3e8; margin-bottom: 20px; }
    .card-header { padding: 15px 20px; border-bottom: 1px solid #dfe3e8; background: #fafbfc; font-weight: 600; color: #212b36; }
    .card-body { padding: 20px; }
    
    .layout-grid { display: flex; gap: 20px; align-items: flex-start; }
    .col-main { flex: 0 0 65%; display: flex; flex-direction: column; gap: 20px; }
    .col-side { flex: 1; display: flex; flex-direction: column; gap: 20px; }
    
    table { width: 100%; border-collapse: collapse; }
    th { background: #f4f6f8; padding: 10px; color: #637381; font-size: 13px; text-align: left; border-bottom: 1px solid #dfe3e8; }
    td { padding: 15px 10px; border-bottom: 1px solid #dfe3e8; vertical-align: middle; }
    
    .btn-primary { background: #0088ff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
    
    .status-badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .status-sent { background: #eafff0; color: #108043; border: 1px solid #8ce09f; }
    .status-unsent { background: #f4f6f8; color: #637381; }

    /* Modal styles */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .modal-box { background: #fff; width: 600px; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .modal-header { font-size: 18px; font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; margin-bottom: 15px; }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=incomplete_list" style="text-decoration: none; color: #637381;">←</a> 
        Chi tiết giỏ hàng bỏ quên: <?php echo htmlspecialchars($order['order_code']); ?>
    </div>
    <div>
        <?php if ($order['email_status'] == 'sent'): ?>
            <span class="status-badge status-sent">Đã gửi email nhắc nhở lúc <?php echo date('d/m/Y H:i', strtotime($order['email_sent_at'])); ?></span>
        <?php else: ?>
            <span class="status-badge status-unsent">Chưa gửi email nhắc nhở</span>
        <?php endif; ?>
    </div>
</div>

<div class="layout-grid">
    <div class="col-main">
        <div class="v3-card">
            <div class="card-header">1. Sản phẩm trong giỏ hàng</div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">Số lượng</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                </td>
                                <td style="text-align: center;"><?php echo $item['qty']; ?></td>
                                <td style="text-align: right;"><?php echo number_format($item['final_price'], 0, ',', '.'); ?>đ</td>
                                <td style="text-align: right; font-weight: bold;"><?php echo number_format($item['line_total'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding: 15px; text-align: right; font-size: 16px;">
                Tổng cộng: <strong style="color: #d82c0d; font-size: 18px;"><?php echo number_format($order['grand_total'], 0, ',', '.'); ?>đ</strong>
            </div>
        </div>
        
        <div class="v3-card">
            <div class="card-header">Gửi email cho khách hàng</div>
            <div class="card-body">
                <p style="color: #637381; margin-bottom: 15px;">Gửi email để nhắc nhở khách hàng về giỏ hàng họ đã bỏ quên, giúp tăng cơ hội chuyển đổi những đơn hàng này.</p>
                <button class="btn-primary" onclick="openEmailModal()">✉️ Gửi email Hoàn tất đơn hàng</button>
            </div>
        </div>
    </div>

    <div class="col-side">
        <div class="v3-card">
            <div class="card-header">Thông tin khách hàng</div>
            <div class="card-body" style="line-height: 1.8;">
                <div>Tên khách: <strong><?php echo htmlspecialchars($order['customer_name'] ?: 'Khách vô danh'); ?></strong></div>
                <div>Số điện thoại: <?php echo htmlspecialchars($order['phone'] ?: 'Không có SĐT'); ?></div>
                <div>Ngày tạo: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Soạn Email -->
<div class="modal-overlay" id="emailModal">
    <div class="modal-box">
        <div class="modal-header">Soạn email nhắc nhở giỏ hàng</div>
        
        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Gửi đến:</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['customer_name']); ?>" disabled>
        
        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Tiêu đề email:</label>
        <input type="text" id="email_subject" class="form-control" value="[AKC Store] Bạn đã bỏ quên sản phẩm trong giỏ hàng!">
        
        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Nội dung tin nhắn:</label>
        <textarea id="email_message" class="form-control" rows="6">Chào <?php echo htmlspecialchars($order['customer_name']); ?>,

Chúng tôi nhận thấy bạn đã để lại một số sản phẩm tuyệt vời trong giỏ hàng. Đừng bỏ lỡ cơ hội sở hữu chúng!
Hãy nhấp vào nút bên dưới để quay lại giỏ hàng và hoàn tất thanh toán.

Cảm ơn bạn đã mua sắm tại AKC Store!</textarea>
        
        <div style="text-align: right; margin-top: 10px;">
            <button class="btn-primary" style="background: #fff; color: #212b36; border: 1px solid #c4cdd5; margin-right: 10px;" onclick="closeEmailModal()">Hủy</button>
            <button class="btn-primary" onclick="sendEmail()">Xác nhận Gửi</button>
        </div>
    </div>
</div>

<script>
    function openEmailModal() {
        document.getElementById('emailModal').style.display = 'flex';
    }

    function closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
    }

    function sendEmail() {
        let message = document.getElementById('email_message').value;
        
        fetch('index.php?action=incomplete_send_email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: <?php echo $order['id']; ?>, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
