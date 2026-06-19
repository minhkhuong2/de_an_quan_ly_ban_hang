<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$expenses = $expenses ?? [];
?>

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

    .btn-primary {
        background: #0088ff;
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
    <div class="v3-title">Danh sách phiếu chi</div>
    <div style="display: flex; gap: 10px;">
        <button id="btn_bulk_delete" class="btn-danger-outline" onclick="processBulkDelete()">🗑️ Xóa phiếu đã chọn</button>
        <a href="index.php?action=create_expense" class="btn-primary">+ Tạo phiếu chi</a>
    </div>
</div>

<div class="v3-card">
    <table class="v3-table">
        <thead>
            <tr>
                <th style="width: 5%;"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                <th>Mã phiếu chi</th>
                <th>Ngày tạo</th>
                <th>Đối tượng nhận</th>
                <th>Lý do chi</th>
                <th style="text-align: right;">Giá trị</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $e): ?>
                <tr>
                    <td><input type="checkbox" class="chk-expense" value="<?php echo $e['id']; ?>" onchange="checkBulkAction()"></td>
                    <td><a href="index.php?action=expense_detail&id=<?php echo $e['id']; ?>" style="color:#0088ff; font-weight:bold; text-decoration:none;"><?php echo $e['expense_code']; ?></a></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($e['created_at'])); ?></td>
                    <td><b><?php echo htmlspecialchars($e['recipient_name']); ?></b></td>
                    <td><?php echo htmlspecialchars($e['expense_reason']); ?></td>
                    <td style="text-align: right; color:#d82c0d; font-weight:bold;"><?php echo number_format($e['amount'], 0, ',', '.'); ?> ₫</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleAll(source) {
        document.querySelectorAll('.chk-expense').forEach(cb => cb.checked = source.checked);
        checkBulkAction();
    }

    function checkBulkAction() {
        let count = document.querySelectorAll('.chk-expense:checked').length;
        document.getElementById('btn_bulk_delete').style.display = count > 0 ? 'block' : 'none';
    }

    function processBulkDelete() {
        let ids = Array.from(document.querySelectorAll('.chk-expense:checked')).map(cb => parseInt(cb.value));
        if (confirm(`⚠️ Bạn đang chọn xóa ${ids.length} phiếu chi.\nThao tác xóa không thể khôi phục và sẽ ảnh hưởng đến sổ cái công nợ!\n\nBạn có chắc chắn muốn xóa?`)) {
            fetch('index.php?action=api_delete_expense', {
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
