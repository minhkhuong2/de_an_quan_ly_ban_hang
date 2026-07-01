<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="header-container" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <a href="index.php?action=handover_list" style="color:#637381; text-decoration:none; font-size:20px;">←</a>
        <h2 style="margin:0;"><?php echo htmlspecialchars($record['record_code']); ?></h2>
        <?php if($record['status'] == 'completed'): ?>
            <span style="background:#eafff0; color:#108043; border:1px solid #8ce09f; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:500;">Đã bàn giao</span>
        <?php else: ?>
            <span style="background:#fff8ea; color:#8a6100; border:1px solid #ffea8a; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:500;">Chờ bàn giao</span>
        <?php endif; ?>
    </div>
    <div style="display:flex; gap:10px;">
        <button class="btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> In biên bản</button>
        <button class="btn-outline" onclick="alert('Đã xuất file Excel!')"><i class="fa-solid fa-file-excel"></i> Xuất Excel</button>
        
        <?php if($record['status'] == 'pending'): ?>
            <button class="btn-primary" style="background:#108043;" onclick="confirmHandover()">Xác nhận bàn giao</button>
            <button class="btn-outline" style="color:#d82c0d; border-color:#d82c0d;" onclick="deleteHandover()">Xóa biên bản</button>
        <?php endif; ?>
    </div>
</div>

<div style="display:flex; gap:20px;">
    <div style="flex:2;">
        <div class="v3-card" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Danh sách kiện hàng (<?php echo count($items); ?>)</h3>
            
            <table class="table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã đơn hàng</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Khách hàng</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã kiện hàng</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã vận đơn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($items)): ?>
                    <tr><td colspan="4" style="text-align:center; padding:20px; color:#637381;">Chưa có kiện hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach($items as $itm): ?>
                        <tr>
                            <td style="padding:10px; border-bottom:1px solid #dfe3e8; font-weight:500; color:#0088ff;"><?php echo htmlspecialchars($itm['order_code'] ?? 'N/A'); ?></td>
                            <td style="padding:10px; border-bottom:1px solid #dfe3e8;"><?php echo htmlspecialchars($itm['customer_name'] ?? 'Khách lẻ'); ?></td>
                            <td style="padding:10px; border-bottom:1px solid #dfe3e8;"><?php echo htmlspecialchars($itm['package_code'] ?? '---'); ?></td>
                            <td style="padding:10px; border-bottom:1px solid #dfe3e8;"><?php echo htmlspecialchars($itm['waybill_code'] ?? '---'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="flex:1;">
        <div class="v3-card" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thông tin chung</h3>
            <div style="margin-bottom:15px;">
                <div style="color:#637381; font-size:13px; margin-bottom:5px;">Chi nhánh bàn giao</div>
                <div style="font-weight:500; color:#212b36;"><?php echo htmlspecialchars($record['branch_name'] ?? '---'); ?></div>
            </div>
            <div style="margin-bottom:15px;">
                <div style="color:#637381; font-size:13px; margin-bottom:5px;">Đối tác vận chuyển</div>
                <div style="font-weight:500; color:#212b36;"><?php echo htmlspecialchars($record['partner_name'] ?? '---'); ?></div>
            </div>
            <div style="margin-bottom:15px;">
                <div style="color:#637381; font-size:13px; margin-bottom:5px;">Ngày ghi nhận</div>
                <div style="color:#212b36;"><?php echo date('d/m/Y H:i', strtotime($record['created_at'])); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmHandover() {
        if(!confirm('Xác nhận bàn giao tất cả kiện hàng cho bưu tá? Sau khi xác nhận sẽ không thể thêm/bớt kiện hàng.')) return;
        
        fetch('index.php?action=confirm_handover', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: <?php echo $record['id']; ?>})
        }).then(res => res.json()).then(res => {
            if(res.status == 'success') {
                alert("Đã bàn giao thành công!");
                window.location.reload();
            } else {
                alert(res.msg);
            }
        });
    }

    function deleteHandover() {
        if(!confirm('Xóa vĩnh viễn biên bản này?')) return;
        
        fetch('index.php?action=delete_handover', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: <?php echo $record['id']; ?>})
        }).then(res => res.json()).then(res => {
            if(res.status == 'success') {
                alert("Đã xóa biên bản!");
                window.location.href = 'index.php?action=handover_list';
            } else {
                alert(res.msg);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
