<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
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
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 13px;
        color: #637381;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .btn-success {
        background: #108043;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-danger-outline {
        background: #fff;
        color: #d82c0d;
        border: 1px solid #fca5a5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        display: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách phiếu thu</div>
    <div style="display: flex; gap: 10px;">
        <button id="btn_bulk_delete" class="btn-danger-outline" onclick="processBulkDelete()">🗑️ Xóa phiếu đã chọn</button>
        <a href="index.php?action=create_receipt" class="btn-success">+ Tạo phiếu thu</a>
    </div>
</div>

<div class="v3-card">
    <table class="v3-table">
        <thead>
            <tr>
                <th style="width: 5%;"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                <th>Mã phiếu thu</th>
                <th>Ngày tạo</th>
                <th>Đối tượng nộp</th>
                <th>Lý do thu</th>
                <th style="text-align: right;">Giá trị thu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($receipts)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:30px; color:#637381;">Sổ quỹ trống, chưa ghi nhận khoản tiền thu nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($receipts as $r): ?>
                    <tr>
                        <td><input type="checkbox" class="chk-receipt" value="<?php echo $r['id']; ?>" onchange="checkBulkAction()"></td>
                        <td><a href="index.php?action=receipt_detail&id=<?php echo $r['id']; ?>" style="color:#108043; font-weight:bold; text-decoration:none;"><?php echo $r['receipt_code']; ?></a></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></td>
                        <td><b><?php echo htmlspecialchars($r['payer_name']); ?></b> <small style="color:#637381;">(<?php echo $r['payer_group']; ?>)</small></td>
                        <td><?php echo htmlspecialchars($r['receipt_reason']); ?></td>
                        <td style="text-align: right; color:#108043; font-weight:bold;"><?php echo number_format($r['amount'], 0, ',', '.'); ?> ₫</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleAll(source) {
        document.querySelectorAll('.chk-receipt').forEach(cb => cb.checked = source.checked);
        checkBulkAction();
    }

    function checkBulkAction() {
        let count = document.querySelectorAll('.chk-receipt:checked').length;
        document.getElementById('btn_bulk_delete').style.display = count > 0 ? 'block' : 'none';
    }

    function processBulkDelete() {
        let ids = Array.from(document.querySelectorAll('.chk-receipt:checked')).map(cb => parseInt(cb.value));
        if (confirm(`⚠️ Bạn có chắc chắn muốn xóa vĩnh viễn ${ids.length} phiếu thu đã chọn?\nHành động này sẽ tính toán nợ lại đối với các tài khoản khách hàng liên quan.`)) {
            fetch('index.php?action=api_delete_receipt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ids: ids
                })
            }).then(res => res.json()).then(res => {
                alert(res.msg);
                window.location.reload();
            });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
