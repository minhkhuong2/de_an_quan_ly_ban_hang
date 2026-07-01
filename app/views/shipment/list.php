<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .v3-title { font-size: 24px; font-weight: bold; color: #212b36; display: flex; align-items: center; gap: 10px; }
    
    .filter-tabs { display: flex; gap: 5px; margin-bottom: 15px; border-bottom: 1px solid #dfe3e8; padding-bottom: 0; overflow-x: auto; }
    .filter-tab { padding: 10px 15px; font-size: 14px; font-weight: 600; color: #637381; cursor: pointer; border-bottom: 2px solid transparent; text-decoration: none; white-space: nowrap; }
    .filter-tab.active { color: #0088ff; border-bottom: 2px solid #0088ff; }
    
    .search-filter-bar { background: #fff; padding: 12px 15px; border-radius: 8px 8px 0 0; border: 1px solid #dfe3e8; border-bottom: none; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-box { display: flex; align-items: center; border: 1px solid #c4cdd5; border-radius: 4px; overflow: hidden; flex-grow: 1; max-width: 350px; }
    .search-box input { border: none; padding: 8px 12px; width: 100%; outline: none; font-size: 14px; }
    
    .filter-dropdown { position: relative; display: inline-block; }
    .filter-btn { background: #fff; border: 1px solid #c4cdd5; padding: 8px 15px; border-radius: 4px; font-size: 13px; font-weight: 500; color: #212b36; cursor: pointer; display: flex; align-items: center; gap: 5px; }
    .filter-menu { position: absolute; top: 100%; left: 0; background: #fff; border: 1px solid #dfe3e8; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 10px; z-index: 100; display: none; min-width: 200px; margin-top: 5px; max-height: 250px; overflow-y: auto; }
    .filter-menu label { display: flex; align-items: center; gap: 8px; font-size: 13px; padding: 5px 0; cursor: pointer; }
    .filter-menu input[type="checkbox"] { margin: 0; cursor: pointer; }
    
    .btn-primary { background: #0088ff; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .btn-outline { background: #fff; color: #212b36; border: 1px solid #c4cdd5; padding: 8px 15px; border-radius: 4px; font-weight: 600; cursor: pointer; }
    
    .v3-table { width: 100%; border-collapse: collapse; text-align: left; background: #fff; border: 1px solid #dfe3e8; border-radius: 0 0 8px 8px; }
    .v3-table th { background: #f4f6f8; padding: 12px 15px; border-bottom: 1px solid #dfe3e8; font-size: 13px; color: #637381; }
    .v3-table td { padding: 14px 15px; border-bottom: 1px solid #dfe3e8; font-size: 14px; color: #212b36; }
    .v3-table tr.clickable:hover { background: #f9fafb; cursor: pointer; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block; }
    
    .bulk-action-bar { background: #e5f0ff; padding: 12px 15px; border: 1px solid #b3d4ff; border-bottom: none; display: none; align-items: center; justify-content: space-between; gap: 15px; }
    
    .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-content { background: #fff; width: 400px; padding: 20px; border-radius: 8px; }
    .modal-footer { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<div class="v3-header">
    <div class="v3-title">🚚 Danh sách Vận đơn</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="exportExcel()"><i class="fa-solid fa-file-export"></i> Xuất file</button>
        <a href="index.php?action=create_order" class="btn-primary">+ Tạo đơn hàng</a>
    </div>
</div>

<div class="filter-tabs" id="custom_filter_tabs">
    <a href="index.php?action=shipment_list&tab=all" class="filter-tab <?php echo ($active_tab == 'all') ? 'active' : ''; ?>">Tất cả vận đơn</a>
    <a href="index.php?action=shipment_list&tab=shipping" class="filter-tab <?php echo ($active_tab == 'shipping') ? 'active' : ''; ?>">Đang giao hàng</a>
    <a href="index.php?action=shipment_list&tab=rescheduling" class="filter-tab <?php echo ($active_tab == 'rescheduling') ? 'active' : ''; ?>">Chờ giao lại</a>
    <a href="index.php?action=shipment_list&tab=returning" class="filter-tab <?php echo ($active_tab == 'returning') ? 'active' : ''; ?>">Đang hoàn hàng</a>
</div>

<form method="GET" action="index.php" id="filter_form">
    <input type="hidden" name="action" value="shipment_list">
    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($active_tab); ?>">
    
    <div class="search-filter-bar">
        <div class="search-box">
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" placeholder="Mã đơn, mã vận đơn, số điện thoại...">
        </div>
        
        <div class="filter-dropdown">
            <button type="button" class="filter-btn" onclick="toggleFilterMenu('status_menu')">Trạng thái ▼</button>
            <div id="status_menu" class="filter-menu">
                <?php $statuses = ['pending'=>'Chờ lấy hàng','shipping'=>'Đang giao hàng','rescheduling'=>'Chờ giao lại','delivered'=>'Đã giao hàng','returning'=>'Đang hoàn hàng','returned'=>'Đã hoàn hàng','cancelled'=>'Hủy giao hàng']; ?>
                <?php foreach($statuses as $k=>$v): ?>
                <label><input type="checkbox" name="status[]" value="<?php echo $k; ?>" <?php echo in_array($k, $status_filter) ? 'checked' : ''; ?>> <?php echo $v; ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="filter-dropdown">
            <button type="button" class="filter-btn" onclick="toggleFilterMenu('branch_menu')">Chi nhánh ▼</button>
            <div id="branch_menu" class="filter-menu">
                <?php foreach($branches as $b): ?>
                <label><input type="checkbox" name="branch[]" value="<?php echo htmlspecialchars($b['branch_name']); ?>" <?php echo in_array($b['branch_name'], $branch_filter) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($b['branch_name']); ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="filter-dropdown">
            <button type="button" class="filter-btn" onclick="toggleFilterMenu('recon_menu')">Trạng thái đối soát ▼</button>
            <div id="recon_menu" class="filter-menu">
                <?php $recons = ['Chưa đối soát', 'Đang đối soát', 'Đã đối soát']; ?>
                <?php foreach($recons as $r): ?>
                <label><input type="checkbox" name="recon[]" value="<?php echo $r; ?>" <?php echo in_array($r, $recon_filter) ? 'checked' : ''; ?>> <?php echo $r; ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn-outline" style="background:#f4f6f8;"><i class="fa-solid fa-magnifying-glass"></i> Lọc</button>
        <button type="button" class="btn-outline" style="color:#0088ff; border-color:#b3d4ff; background:#e5f0ff;" onclick="saveFilter()">
            <i class="fa-solid fa-bookmark"></i> Lưu bộ lọc
        </button>
    </div>
</form>

<div id="bulk_bar" class="bulk-action-bar">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-weight:600; color:#0056b3;">Đã chọn <span id="selected_count">0</span> vận đơn:</span>
        <button class="btn-outline" onclick="openChangeStatusModal()">Chuyển trạng thái</button>
        <button class="btn-primary" style="background:#108043;" onclick="reconcile()">Đối soát vận chuyển</button>
        <button class="btn-outline" onclick="printShipping()">🖨️ In Phiếu giao hàng</button>
        <button class="btn-outline" onclick="printHandover()">🖨️ In Phiếu bàn giao</button>
    </div>
</div>

<table class="v3-table">
    <thead>
        <tr>
            <th style="width:40px; text-align:center;"><input type="checkbox" id="check_all" onclick="toggleCheckAll()"></th>
            <th>Mã vận đơn</th>
            <th>Mã đơn hàng</th>
            <th>Người nhận</th>
            <th>Đơn vị VC</th>
            <th>Tiền thu hộ (COD)</th>
            <th>Trạng thái</th>
            <th>Đối soát</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($shipments)): ?>
            <tr><td colspan="8" style="text-align:center; padding:30px; color:#637381;">Không tìm thấy vận đơn nào.</td></tr>
        <?php else: ?>
            <?php foreach ($shipments as $s): ?>
                <tr class="clickable" onclick="window.location='index.php?action=view_order&id=<?php echo $s['id']; ?>'">
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <input type="checkbox" class="row-checkbox" value="<?php echo $s['id']; ?>" onclick="updateBulkBar()">
                    </td>
                    <td><b style="color:#0088ff;"><?php echo htmlspecialchars($s['waybill_code']); ?></b></td>
                    <td><a href="index.php?action=view_order&id=<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['order_code']); ?></a></td>
                    <td><b><?php echo htmlspecialchars($s['customer_name'] ?: 'Khách lẻ'); ?></b><br><span style="font-size:12px;color:#637381;"><?php echo htmlspecialchars($s['phone']); ?></span></td>
                    <td><?php echo htmlspecialchars($s['shipping_partner_name']); ?></td>
                    <td style="font-weight:600; color:#d82c0d;"><?php echo number_format($s['cod_partner'], 0, '', '.'); ?> ₫</td>
                    <td>
                        <?php 
                        $st = $s['shipping_status'];
                        if($st == 'delivered') echo '<span class="badge" style="background:#eafff0; color:#108043;">Đã giao</span>';
                        elseif($st == 'shipping') echo '<span class="badge" style="background:#e5f0ff; color:#0056b3;">Đang giao</span>';
                        elseif($st == 'rescheduling') echo '<span class="badge" style="background:#fff8ea; color:#8a6100;">Chờ giao lại</span>';
                        elseif($st == 'returned') echo '<span class="badge" style="background:#f4f6f8; color:#637381;">Đã hoàn</span>';
                        elseif($st == 'returning') echo '<span class="badge" style="background:#ffe4e4; color:#d82c0d;">Đang hoàn</span>';
                        elseif($st == 'cancelled') echo '<span class="badge" style="background:#f4f6f8; color:#637381;">Hủy</span>';
                        else echo '<span class="badge" style="background:#f4f6f8; color:#637381;">' . htmlspecialchars($st) . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php 
                        $rs = $s['reconciliation_status'];
                        if($rs == 'Đã đối soát') echo '<span class="badge" style="background:#eafff0; color:#108043;"><i class="fa-solid fa-check"></i> Đã ĐS</span>';
                        elseif($rs == 'Đang đối soát') echo '<span class="badge" style="background:#fff8ea; color:#8a6100;">Đang ĐS</span>';
                        else echo '<span class="badge" style="background:#f4f6f8; color:#637381;">Chưa ĐS</span>';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modals -->
<div id="change_status_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">Đổi trạng thái vận đơn</h3>
        <p style="font-size:13px; color:#637381;">Chọn trạng thái mới cho các vận đơn đã chọn.</p>
        <select id="new_status" class="btn-outline" style="width:100%; margin-top:10px; padding:10px;">
            <option value="pending">Chờ lấy hàng</option>
            <option value="shipping">Đang giao hàng</option>
            <option value="rescheduling">Chờ giao lại</option>
            <option value="delivered">Đã giao hàng</option>
            <option value="returning">Đang hoàn hàng</option>
            <option value="returned">Đã hoàn hàng</option>
        </select>
        <div class="modal-footer">
            <button class="btn-outline" onclick="document.getElementById('change_status_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitChangeStatus()">Cập nhật</button>
        </div>
    </div>
</div>

<script>
    // JS Filter menu
    function toggleFilterMenu(id) {
        let menus = document.querySelectorAll('.filter-menu');
        menus.forEach(m => { if(m.id !== id) m.style.display = 'none'; });
        let menu = document.getElementById(id);
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
    
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.filter-dropdown')) {
            document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
        }
    });

    // Checkbox & Bulk Bar
    function toggleCheckAll() {
        let isChecked = document.getElementById('check_all').checked;
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = isChecked);
        updateBulkBar();
    }
    
    function updateBulkBar() {
        let count = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('selected_count').innerText = count;
        document.getElementById('bulk_bar').style.display = count > 0 ? 'flex' : 'none';
        
        let checkAll = document.getElementById('check_all');
        let totalBoxes = document.querySelectorAll('.row-checkbox').length;
        if(totalBoxes > 0) {
            checkAll.checked = count === totalBoxes;
            checkAll.indeterminate = count > 0 && count < totalBoxes;
        }
    }

    function getSelectedIds() {
        let ids = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => ids.push(cb.value));
        return ids;
    }

    // Bulk Actions
    function openChangeStatusModal() {
        document.getElementById('change_status_modal').style.display = 'flex';
    }

    function submitChangeStatus() {
        let ids = getSelectedIds();
        let status = document.getElementById('new_status').value;
        
        fetch('index.php?action=update_shipment_status', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ids: ids, status: status})
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        });
    }

    function reconcile() {
        let ids = getSelectedIds();
        if(!confirm('Thực hiện đối soát vận chuyển cho các vận đơn đã chọn?')) return;
        
        fetch('index.php?action=reconcile_shipments', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ids: ids})
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        });
    }

    function printShipping() {
        let ids = getSelectedIds();
        if(ids.length > 0) {
            window.open('index.php?action=print_shipping&ids=' + ids.join(','), '_blank');
        }
    }

    function printHandover() {
        let ids = getSelectedIds();
        if(ids.length > 0) {
            window.open('index.php?action=print_handover_slip&ids=' + ids.join(','), '_blank');
        }
    }

    function exportExcel() {
        // Mock export
        alert("Đã xuất danh sách vận đơn ra file Excel!");
    }

    // Custom Filters (Local Storage Demo)
    document.addEventListener("DOMContentLoaded", function() {
        let savedFilters = JSON.parse(localStorage.getItem('shipment_filters') || '[]');
        let tabsContainer = document.getElementById('custom_filter_tabs');
        
        savedFilters.forEach((f, idx) => {
            let a = document.createElement('a');
            a.className = 'filter-tab';
            a.innerText = f.name;
            a.href = "index.php?action=shipment_list&" + f.query;
            tabsContainer.appendChild(a);
        });
    });

    function saveFilter() {
        let name = prompt("Nhập tên cho bộ lọc này (Ví dụ: Đơn Viettel chưa đối soát):");
        if(name) {
            let form = document.getElementById('filter_form');
            let query = new URLSearchParams(new FormData(form)).toString();
            let savedFilters = JSON.parse(localStorage.getItem('shipment_filters') || '[]');
            savedFilters.push({name: name, query: query});
            localStorage.setItem('shipment_filters', JSON.stringify(savedFilters));
            alert("Đã lưu bộ lọc!");
            location.reload();
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
