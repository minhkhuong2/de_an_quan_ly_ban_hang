<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="header-container" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Đơn hàng nháp</h2>
    <div style="display:flex; gap:10px;">
        <button class="btn-outline" onclick="exportDrafts()"><i class="fa-solid fa-file-export"></i> Xuất file</button>
        <a href="index.php?action=create_order" class="btn-primary" style="text-decoration:none;"><i class="fa-solid fa-plus"></i> Tạo đơn hàng nháp</a>
    </div>
</div>

<div class="v3-card" style="background:#fff; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    
    <!-- Tab trạng thái -->
    <div style="border-bottom:1px solid #dfe3e8; display:flex; gap:20px; padding:0 20px;">
        <a href="index.php?action=draft_list" style="padding:15px 0; text-decoration:none; color:<?php echo empty($_GET['status']) ? '#202223; font-weight:600; border-bottom:3px solid #008060;' : '#637381;'; ?>">Tất cả đơn nháp (<?php echo $status_counts['all'] ?? 0; ?>)</a>
        <a href="index.php?action=draft_list&status=open" style="padding:15px 0; text-decoration:none; color:<?php echo ($_GET['status']??'')=='open' ? '#202223; font-weight:600; border-bottom:3px solid #008060;' : '#637381;'; ?>">Mở (<?php echo $status_counts['open'] ?? 0; ?>)</a>
        <a href="index.php?action=draft_list&status=completed" style="padding:15px 0; text-decoration:none; color:<?php echo ($_GET['status']??'')=='completed' ? '#202223; font-weight:600; border-bottom:3px solid #008060;' : '#637381;'; ?>">Hoàn thành (<?php echo $status_counts['completed'] ?? 0; ?>)</a>
    </div>

    <!-- Bộ lọc -->
    <div style="padding:15px 20px; border-bottom:1px solid #dfe3e8; display:flex; gap:10px;">
        <form method="GET" action="index.php" style="display:flex; gap:10px; flex:1;">
            <input type="hidden" name="action" value="draft_list">
            <?php if(!empty($_GET['status'])) echo '<input type="hidden" name="status" value="'.htmlspecialchars($_GET['status']).'">'; ?>
            
            <div style="position:relative; flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:10px; color:#8c9196;"></i>
                <input type="text" name="keyword" value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>" placeholder="Tìm theo tên khách, sđt, mã đơn..." class="form-control" style="width:100%; padding-left:35px; padding-top:8px; padding-bottom:8px;">
            </div>
            <button type="submit" class="btn-outline">Lọc</button>
        </form>
    </div>

    <!-- Toolbar Bulk Actions -->
    <div id="bulk_toolbar" style="display:none; padding:10px 20px; background:#f4f6f8; border-bottom:1px solid #dfe3e8; align-items:center; gap:15px;">
        <span id="selected_count" style="font-weight:500;">Đã chọn 0 đơn hàng nháp</span>
        <button class="btn-outline" onclick="addTags()">Thêm tag</button>
        <button class="btn-outline" onclick="removeTags()">Xóa tag</button>
        <button class="btn-outline" style="color:#d82c0d; border-color:#d82c0d;" onclick="deleteSelected()">Xóa đơn hàng nháp</button>
    </div>

    <table class="table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f4f6f8; color:#637381; font-size:13px; text-align:left;">
                <th style="padding:12px 20px; width:40px;"><input type="checkbox" id="check_all" onclick="toggleCheckAll()"></th>
                <th style="padding:12px 0;">Mã đơn</th>
                <th style="padding:12px 0;">Ngày tạo</th>
                <th style="padding:12px 0;">Khách hàng</th>
                <th style="padding:12px 0;">Trạng thái</th>
                <th style="padding:12px 0;">Tổng tiền</th>
                <th style="padding:12px 0;">Tag</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($draft_orders)): ?>
                <tr>
                    <td colspan="7" style="padding:40px; text-align:center; color:#637381;">Chưa có đơn hàng nháp nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($draft_orders as $o): ?>
                <tr style="border-bottom:1px solid #dfe3e8;">
                    <td style="padding:12px 20px;"><input type="checkbox" class="row-check" value="<?php echo $o['id']; ?>" onclick="updateBulkToolbar()"></td>
                    <td style="padding:12px 0;">
                        <a href="index.php?action=edit_order&id=<?php echo $o['id']; ?>" style="color:#008060; font-weight:600; text-decoration:none;"><?php echo htmlspecialchars($o['order_code']); ?></a>
                    </td>
                    <td style="padding:12px 0; color:#637381;"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                    <td style="padding:12px 0;"><?php echo htmlspecialchars($o['customer_name'] ?? 'Khách lẻ'); ?></td>
                    <td style="padding:12px 0;">
                        <?php if($o['draft_status'] == 'open'): ?>
                            <span style="background:#e4f0fa; color:#006fbb; padding:4px 8px; border-radius:12px; font-size:12px;">Mở</span>
                        <?php else: ?>
                            <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:12px; font-size:12px;">Hoàn thành</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px 0; font-weight:500;"><?php echo number_format($o['grand_total']); ?>đ</td>
                    <td style="padding:12px 0; color:#637381;"><?php echo htmlspecialchars($o['tags'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleCheckAll() {
    const isChecked = document.getElementById('check_all').checked;
    const checkboxes = document.querySelectorAll('.row-check');
    checkboxes.forEach(cb => cb.checked = isChecked);
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const checkboxes = document.querySelectorAll('.row-check:checked');
    const toolbar = document.getElementById('bulk_toolbar');
    const countSpan = document.getElementById('selected_count');
    
    if(checkboxes.length > 0) {
        toolbar.style.display = 'flex';
        countSpan.textContent = `Đã chọn ${checkboxes.length} đơn hàng nháp`;
    } else {
        toolbar.style.display = 'none';
        document.getElementById('check_all').checked = false;
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
}

function deleteSelected() {
    const ids = getSelectedIds();
    if(ids.length === 0) return;
    
    if(!confirm('Xóa các đơn hàng nháp đang chọn? LƯU Ý: Các đơn hàng nháp đã "Hoàn thành" sẽ không bị xóa.')) return;
    
    fetch('index.php?action=delete_draft', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ids: ids})
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if(res.status == 'success') window.location.reload();
    });
}

function addTags() {
    const ids = getSelectedIds();
    const tag = prompt("Nhập tag muốn thêm:");
    if(!tag) return;
    
    fetch('index.php?action=update_draft_tags', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ids: ids, tag_action: 'add', tag: tag})
    }).then(r => r.json()).then(res => {
        if(res.status == 'success') window.location.reload();
        else alert(res.msg);
    });
}

function removeTags() {
    const ids = getSelectedIds();
    const tag = prompt("Nhập tag muốn xóa:");
    if(!tag) return;
    
    fetch('index.php?action=update_draft_tags', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ids: ids, tag_action: 'remove', tag: tag})
    }).then(r => r.json()).then(res => {
        if(res.status == 'success') window.location.reload();
        else alert(res.msg);
    });
}

function exportDrafts() {
    alert("Hệ thống sẽ tổng hợp danh sách đơn hàng nháp và gửi link tải qua email của bạn.");
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
