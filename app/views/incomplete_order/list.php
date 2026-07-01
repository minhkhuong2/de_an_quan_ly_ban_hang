<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    .v3-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 20px; border: 1px solid #dfe3e8; }
    
    .filter-bar { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; }
    .form-control { padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; }
    .btn-primary { background: #0088ff; color: #fff; padding: 8px 15px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
    .btn-outline { background: #fff; color: #212b36; padding: 8px 15px; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: 500; cursor: pointer; }

    .table-list { width: 100%; border-collapse: collapse; }
    .table-list th { background: #f4f6f8; padding: 12px 15px; text-align: left; color: #637381; font-size: 13px; border-bottom: 1px solid #dfe3e8; }
    .table-list td { padding: 15px; border-bottom: 1px solid #dfe3e8; font-size: 14px; color: #212b36; }
    
    .badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
    .badge-unsent { background: #f4f6f8; color: #637381; }
    .badge-sent { background: #eafff0; color: #108043; border: 1px solid #8ce09f; }
    .badge-archived { background: #ffe4e4; color: #d82c0d; }

    .bulk-actions { background: #f4f6f8; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; display: none; align-items: center; gap: 10px; }
</style>

<div class="v3-header">
    <div class="v3-title">Đơn hàng chưa hoàn tất</div>
    <div>
        <button class="btn-outline" onclick="exportExcel()">📤 Xuất file</button>
    </div>
</div>

<div class="v3-card">
    <form method="GET" action="index.php" class="filter-bar" id="filter_form">
        <input type="hidden" name="action" value="incomplete_list">
        
        <input type="text" name="keyword" class="form-control" placeholder="Tìm tên khách, SĐT, mã đơn..." value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>" style="width: 250px;">
        
        <select name="status" class="form-control">
            <option value="open" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'open') ? 'selected' : ''; ?>>Mở (Đang hoạt động)</option>
            <option value="archived" <?php echo (isset($_GET['status']) && $_GET['status'] == 'archived') ? 'selected' : ''; ?>>Đã lưu trữ</option>
        </select>

        <select name="email_status" class="form-control">
            <option value="all">Trạng thái Email: Tất cả</option>
            <option value="unsent" <?php echo (isset($_GET['email_status']) && $_GET['email_status'] == 'unsent') ? 'selected' : ''; ?>>Chưa gửi</option>
            <option value="sent" <?php echo (isset($_GET['email_status']) && $_GET['email_status'] == 'sent') ? 'selected' : ''; ?>>Đã gửi</option>
        </select>

        <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($_GET['from_date'] ?? ''); ?>" title="Từ ngày">
        
        <button type="submit" class="btn-primary">Lọc đơn hàng</button>
        <button type="button" class="btn-outline" onclick="window.location='index.php?action=incomplete_list'">Xóa bộ lọc</button>
    </form>

    <div class="bulk-actions" id="bulk_actions">
        <span style="font-size: 14px; font-weight: 600;" id="selected_count">Đã chọn 0 đơn hàng</span>
        <span style="flex:1;"></span>
        <?php if (!isset($_GET['status']) || $_GET['status'] != 'archived'): ?>
            <button class="btn-outline" onclick="processBulk('archive')">🗃️ Lưu trữ</button>
        <?php else: ?>
            <button class="btn-outline" onclick="processBulk('unarchive')">📤 Hủy lưu trữ</button>
        <?php endif; ?>
        <button class="btn-outline" style="color: #d82c0d; border-color: #d82c0d;" onclick="processBulk('delete')">🗑️ Xóa</button>
    </div>

    <table class="table-list">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" id="check_all" onclick="toggleAll(this)"></th>
                <th>Mã đơn</th>
                <th>Ngày tạo</th>
                <th>Khách hàng</th>
                <th>Trạng thái Email</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #8c98a4;">Không tìm thấy đơn hàng chưa hoàn tất nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><input type="checkbox" class="row-check" value="<?php echo $o['id']; ?>" onclick="updateBulkUI()"></td>
                        <td>
                            <a href="index.php?action=incomplete_detail&id=<?php echo $o['id']; ?>" style="color: #0088ff; font-weight: bold; text-decoration: none;">
                                <?php echo htmlspecialchars($o['order_code']); ?>
                            </a>
                            <?php if ($o['is_archived']): ?>
                                <span class="badge badge-archived" style="margin-left: 5px;">Đã lưu trữ</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                        <td>
                            <?php echo htmlspecialchars($o['customer_name'] ?: 'Khách vô danh'); ?>
                            <br><small style="color: #637381;"><?php echo htmlspecialchars($o['phone'] ?: 'Không có SĐT'); ?></small>
                        </td>
                        <td>
                            <?php if ($o['email_status'] == 'sent'): ?>
                                <span class="badge badge-sent">✉️ Đã gửi</span>
                            <?php elseif ($o['email_status'] == 'scheduled'): ?>
                                <span class="badge" style="background: #fff8ea; color: #8a6100; border: 1px solid #ffea8a;">⏱️ Hẹn giờ</span>
                            <?php else: ?>
                                <span class="badge badge-unsent">Chưa gửi</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: bold; color: #212b36;">
                            <?php echo number_format($o['grand_total'], 0, ',', '.'); ?>đ
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleAll(source) {
        let checkboxes = document.querySelectorAll('.row-check');
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
        updateBulkUI();
    }

    function updateBulkUI() {
        let checkboxes = document.querySelectorAll('.row-check:checked');
        let bulkDiv = document.getElementById('bulk_actions');
        let countSpan = document.getElementById('selected_count');
        
        if (checkboxes.length > 0) {
            countSpan.innerText = 'Đã chọn ' + checkboxes.length + ' đơn hàng';
            bulkDiv.style.display = 'flex';
        } else {
            bulkDiv.style.display = 'none';
        }
    }

    function processBulk(action) {
        let checkboxes = document.querySelectorAll('.row-check:checked');
        let ids = [];
        checkboxes.forEach(cb => ids.push(cb.value));

        if (ids.length === 0) return;

        let msg = "";
        if (action === 'archive') msg = "Lưu trữ " + ids.length + " đơn hàng?";
        if (action === 'unarchive') msg = "Hủy lưu trữ " + ids.length + " đơn hàng?";
        if (action === 'delete') msg = "Bạn có chắc chắn xóa vĩnh viễn " + ids.length + " đơn hàng này?";

        if (!confirm(msg)) return;

        fetch('index.php?action=incomplete_bulk', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Thao tác thành công!");
                location.reload();
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }

    function exportExcel() {
        let form = document.getElementById('filter_form');
        let formData = new FormData(form);
        let params = new URLSearchParams(formData);
        params.set('action', 'incomplete_export');
        window.location.href = 'index.php?' + params.toString();
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
