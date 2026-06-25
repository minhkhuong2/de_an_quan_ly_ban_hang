<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $orders
 * @var string|int $active_tab_id
 * @var array $saved_filters
 * @var string $search_type
 * @var string $keyword
 * @var string $status
 * @var string $payment_status
 * @var array $branches
 * @var string|int $branch_id
 */
?>
<style>
    /* CSS giữ nguyên từ bản trước */
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .v3-title {
        font-size: 24px;
        font-weight: bold;
        color: #212b36;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 15px;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 0;
        overflow-x: auto;
    }

    .filter-tab {
        padding: 10px 15px;
        font-size: 14px;
        font-weight: 600;
        color: #637381;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        text-decoration: none;
        white-space: nowrap;
    }

    .filter-tab.active {
        color: #0088ff;
        border-bottom: 2px solid #0088ff;
    }

    .search-filter-bar {
        background: #fff;
        padding: 12px 15px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dfe3e8;
        border-bottom: none;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box {
        display: flex;
        align-items: center;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        overflow: hidden;
        flex-grow: 1;
        max-width: 450px;
    }

    .search-box select {
        border: none;
        background: #f4f6f8;
        padding: 8px;
        outline: none;
        border-right: 1px solid #c4cdd5;
        font-size: 13px;
        font-weight: 500;
    }

    .search-box input {
        border: none;
        padding: 8px 12px;
        width: 100%;
        outline: none;
        font-size: 14px;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 13px;
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

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 0 0 8px 8px;
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

    .v3-table tr.clickable:hover {
        background: #f9fafb;
        cursor: pointer;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-paid {
        background: #eafff0;
        color: #108043;
    }

    .badge-pending {
        background: #fff8ea;
        color: #8a6100;
    }

    .badge-completed {
        background: #e5f0ff;
        color: #0088ff;
    }

    /* CSS MỚI CHO BÁO CÁO VAT */
    .badge-vat {
        background: #fff0f6;
        color: #c41d7f;
        border: 1px solid #ffadd2;
        margin-top: 5px;
    }

    .bulk-action-bar {
        background: #e5f0ff;
        padding: 12px 15px;
        border: 1px solid #b3d4ff;
        border-bottom: none;
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .gear-btn {
        background: none;
        border: none;
        font-size: 16px;
        color: #637381;
        cursor: pointer;
        padding: 5px;
    }

    .column-setting-box {
        position: absolute;
        top: 35px;
        left: 0;
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 15px;
        z-index: 100;
        display: none;
        flex-direction: column;
        gap: 10px;
        width: 200px;
    }

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 450px;
        padding: 25px;
        border-radius: 8px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">
        📦 Danh sách đơn hàng
        <div style="position: relative;">
            <button class="gear-btn" onclick="toggleColumnSettings()" title="Điều chỉnh cột">⚙️</button>
            <div id="col_settings" class="column-setting-box">
                <b style="font-size:13px; border-bottom:1px solid #dfe3e8; padding-bottom:8px; margin-bottom:5px;">Hiển thị cột:</b>
                <label><input type="checkbox" checked onchange="toggleCol('col_date')"> Ngày tạo</label>
                <label><input type="checkbox" checked onchange="toggleCol('col_customer')"> Khách hàng</label>
                <label><input type="checkbox" checked onchange="toggleCol('col_branch')"> Chi nhánh</label>
                <label><input type="checkbox" checked onchange="toggleCol('col_pay')"> Thanh toán</label>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="openExportModal()"><i class="fa-solid fa-file-export"></i> Xuất file Excel</button>
        <a href="index.php?action=create_order" class="btn-primary">+ Tạo đơn hàng</a>
    </div>
</div>

<?php if (isset($_GET['success_bulk_action'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #33d067;">✅ Thao tác thành công cho <?php echo $_GET['count']; ?> đơn hàng!</div>
<?php endif; ?>

<div class="filter-tabs">
    <a href="index.php?action=order_list&tab_id=all" class="filter-tab <?php echo ($active_tab_id == '' || $active_tab_id == 'all') ? 'active' : ''; ?>">Tất cả đơn hàng</a>
    <?php foreach ($saved_filters as $sf): ?>
        <a href="index.php?action=order_list&tab_id=<?php echo $sf['id']; ?>" class="filter-tab <?php echo ($active_tab_id == $sf['id']) ? 'active' : ''; ?>">
            <i class="fa-solid fa-filter" style="font-size:11px; margin-right:3px;"></i> <?php echo htmlspecialchars($sf['filter_name']); ?>
        </a>
    <?php endforeach; ?>
</div>

<form method="GET" action="index.php" id="filter_form">
    <input type="hidden" name="action" value="order_list">
    <div class="search-filter-bar">
        <div class="search-box">
            <select name="search_type">
                <option value="all">🔍 Tất cả</option>
                <option value="order_code">Mã đơn hàng</option>
            </select>
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" placeholder="Nhập mã đơn...">
        </div>
        <button type="submit" class="btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Lọc</button>
        <button type="button" class="btn-outline" style="color:#0088ff; border-color:#b3d4ff; background:#e5f0ff;" onclick="document.getElementById('save_filter_modal').style.display='flex'">
            <i class="fa-solid fa-bookmark"></i> Lưu bộ lọc
        </button>
    </div>
</form>

<div id="bulk_bar" class="bulk-action-bar">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-weight:600; color:#0056b3; margin-right: 10px;">Đã chọn <span id="selected_count">0</span> đơn:</span>
        <button class="btn-outline" style="color:#108043; border-color:#8ce09f;" onclick="submitBulkAction('confirm_orders')">✅ Xác nhận đơn</button>
        <button class="btn-primary" style="background:#8e44ad;" onclick="submitBulkAction('packing')">📦 Yêu cầu đóng gói</button>
        <button class="btn-primary" style="background:#e67e22;" onclick="openBulkShipModal()">🚚 Giao hàng</button>

        <div style="position:relative;">
            <button class="btn-outline" onclick="toggleMoreActions()">Thao tác khác ▼</button>
            <div id="more_actions_menu" style="position:absolute; top:35px; left:0; background:#fff; border:1px solid #dfe3e8; border-radius:4px; box-shadow:0 4px 12px rgba(0,0,0,0.1); display:none; flex-direction:column; z-index:100; min-width:220px;">
                <a href="javascript:void(0)" onclick="openEInvoiceModal()" style="padding:10px 15px; text-decoration:none; color:#c41d7f; border-bottom:1px solid #dfe3e8; background:#fff0f6; font-weight:bold;">🧾 Phát hành Hóa đơn VAT</a>
                <a href="javascript:void(0)" onclick="printBulkOrders()" style="padding:10px 15px; text-decoration:none; color:#212b36; border-bottom:1px solid #dfe3e8;">🖨️ In đơn hàng</a>
                <a href="javascript:void(0)" onclick="submitBulkAction('archive')" style="padding:10px 15px; text-decoration:none; color:#212b36; border-bottom:1px solid #dfe3e8;">🗃️ Lưu trữ đơn hàng</a>
                <a href="javascript:void(0)" onclick="openAssignStaffModal()" style="padding:10px 15px; text-decoration:none; color:#212b36; border-bottom:1px solid #dfe3e8;">👤 Phân công nhân viên</a>
                <a href="javascript:void(0)" onclick="openTagsModal()" style="padding:10px 15px; text-decoration:none; color:#212b36;">🏷️ Thêm Tag (Nhãn)</a>
            </div>
        </div>
    </div>
</div>

<form id="frm_bulk_action" action="index.php?action=bulk_order_actions" method="POST" style="display:none;">
    <input type="hidden" name="bulk_action_ids" id="bulk_action_ids">
    <input type="hidden" name="bulk_action_type" id="bulk_action_type">
    <input type="hidden" name="assign_staff_id" id="assign_staff_id">
    <input type="hidden" name="order_tags" id="order_tags">
    <input type="hidden" name="invoice_symbol" id="hidden_invoice_symbol">
</form>

<table class="v3-table">
    <thead>
        <tr>
            <th style="width:40px; text-align:center;"><input type="checkbox" id="check_all" onclick="toggleCheckAll()"></th>
            <th>Mã đơn hàng</th>
            <th class="col_date">Ngày tạo</th>
            <th class="col_customer">Khách hàng</th>
            <th class="col_branch">Chi nhánh</th>
            <th class="col_pay">Thanh toán</th>
            <th>Trạng thái</th>
            <th style="text-align: right;">Tổng tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:30px; color:#637381;">Không tìm thấy đơn hàng.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($orders as $o): ?>
                <tr class="clickable">
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <input type="checkbox" class="row-checkbox" value="<?php echo $o['id']; ?>" data-status="<?php echo $o['order_status'] ?? ''; ?>" onclick="updateBulkBar()">
                    </td>
                    <td onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <b style="color: #0088ff;"><?php echo htmlspecialchars($o['order_code']); ?></b><br>
                        <?php if (($o['has_e_invoice'] ?? 0) == 1): ?>
                            <span class="badge badge-vat">🧾 Đã xuất VAT</span>
                        <?php endif; ?>
                    </td>
                    <td class="col_date" style="color: #637381; font-size:13px;" onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?>
                    </td>
                    <td class="col_customer" onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <b><?php echo !empty($o['customer_name']) ? htmlspecialchars($o['customer_name']) : 'Khách lẻ'; ?></b>
                    </td>
                    <td class="col_branch" style="font-size: 13px;" onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <?php echo htmlspecialchars($o['branch_name'] ?? 'Mặc định'); ?>
                    </td>
                    <td class="col_pay" onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <?php echo (($o['payment_status'] ?? '') == 'paid') ? '<span class="badge badge-paid">Đã thanh toán</span>' : '<span class="badge badge-pending">Chưa thanh toán</span>'; ?>
                    </td>
                    <td onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <?php
                        if (($o['order_status'] ?? '') == 'completed') echo '<span class="badge badge-completed">Hoàn thành</span>';
                        elseif (($o['order_status'] ?? '') == 'processing') echo '<span class="badge" style="background:#e5f0ff; color:#0056b3;">Đang xử lý</span>';
                        else echo '<span class="badge badge-pending">Chờ xử lý</span>';
                        ?>
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #d82c0d;" onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">
                        <?php echo number_format($o['grand_total'] ?? 0, 0, '', '.'); ?> ₫
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="einvoice_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color:#c41d7f;">🧾 Phát hành Hóa đơn Điện tử (VAT)</h3>
        <p style="font-size:13px; color:#637381;">Hệ thống sẽ cấp số Hóa đơn và lấy Mã tra cứu từ Cơ quan Thuế.</p>

        <div class="form-group" style="margin-top:15px;">
            <label>Ký hiệu Hóa đơn (Mẫu số)</label>
            <input type="text" id="input_invoice_symbol" class="v3-form-control" value="1C26TAA">
            <p style="font-size:12px; color:#919eab; margin-top:5px;">VD: 1C26TAA (Hóa đơn GTGT năm 2026)</p>
        </div>

        <div style="background:#fff0f6; padding:10px; border-radius:4px; font-size:13px; color:#c41d7f; margin-bottom:15px; border:1px solid #ffadd2;">
            💡 Khách hàng không cung cấp MST sẽ được ghi nhận là "Người mua không lấy hóa đơn".
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button class="btn-outline" onclick="document.getElementById('einvoice_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" style="background:#c41d7f;" onclick="confirmEInvoices()">Ký số & Phát hành</button>
        </div>
    </div>
</div>
<div id="export_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color:#108043;"><i class="fa-solid fa-file-excel"></i> Xuất dữ liệu Đơn hàng</h3>
        <p style="font-size:13px; color:#637381;">Trích xuất dữ liệu ra file định dạng CSV để xem trên Excel.</p>

        <form action="index.php?action=export_orders" method="POST" id="frm_export">
            <input type="hidden" name="export_ids" id="export_ids">

            <div class="form-group" style="margin-top:15px;">
                <label>Phạm vi xuất dữ liệu <span>*</span></label>
                <select name="export_scope" id="export_scope" class="v3-form-control" style="width:100%;">
                    <option value="all">Tất cả đơn hàng trong hệ thống</option>
                    <option value="selected" id="opt_selected" style="display:none; color:#0088ff; font-weight:bold;">Chỉ xuất các đơn hàng đã được Tích chọn</option>
                </select>
            </div>

            <div class="form-group">
                <label>Loại thông tin hiển thị <span>*</span></label>
                <select name="export_type" class="v3-form-control" style="width:100%;">
                    <option value="summary">File tổng quan (Mỗi đơn hàng 1 dòng)</option>
                    <option value="detailed">File chi tiết (Kèm thông tin từng Sản phẩm bên trong)</option>
                </select>
            </div>

            <div style="background:#fff8ea; padding:10px; border-radius:4px; font-size:12px; color:#8a6100; margin-bottom:15px; border:1px solid #ffea8a;">
                💡 Mẹo: Dữ liệu số điện thoại và mã đơn sẽ được định dạng Text để không bị Excel xóa mất số 0 ở đầu.
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('export_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary" style="background:#108043;" onclick="setTimeout(() => document.getElementById('export_modal').style.display='none', 1000)">Tải File Xuống</button>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleColumnSettings() {
        let box = document.getElementById('col_settings');
        box.style.display = box.style.display === 'flex' ? 'none' : 'flex';
    }

    function toggleCol(colClass) {
        document.querySelectorAll('.' + colClass).forEach(col => col.style.display = col.style.display === 'none' ? 'table-cell' : 'none');
    }

    function toggleCheckAll() {
        let isChecked = document.getElementById('check_all').checked;
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = isChecked);
        updateBulkBar();
    }

    function updateBulkBar() {
        let checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        document.getElementById('selected_count').innerText = checkedBoxes.length;
        document.getElementById('bulk_bar').style.display = checkedBoxes.length > 0 ? 'flex' : 'none';
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
    }

    function toggleMoreActions() {
        let menu = document.getElementById('more_actions_menu');
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }

    // Gửi Hành động (Xác nhận đơn, Đóng gói, Lưu trữ)
    function submitBulkAction(actionType) {
        let ids = getSelectedIds();
        if (actionType === 'confirm_orders' && !confirm('Chuyển trạng thái các đơn chờ thành ĐANG XỬ LÝ?')) return;
        if (actionType === 'archive' && !confirm('Cất gọn các đơn đã chọn vào Kho Lưu trữ?')) return;

        document.getElementById('bulk_action_ids').value = ids;
        document.getElementById('bulk_action_type').value = actionType;
        document.getElementById('frm_bulk_action').submit();
    }

    // Modal Hóa Đơn Điện Tử
    function openEInvoiceModal() {
        document.getElementById('einvoice_modal').style.display = 'flex';
        document.getElementById('more_actions_menu').style.display = 'none';
    }

    function confirmEInvoices() {
        document.getElementById('bulk_action_ids').value = getSelectedIds();
        document.getElementById('bulk_action_type').value = 'issue_e_invoices';
        document.getElementById('hidden_invoice_symbol').value = document.getElementById('input_invoice_symbol').value;
        document.getElementById('frm_bulk_action').submit();
    }
    // Hàm mở Modal Xuất File
    function openExportModal() {
        let ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        let scopeSelect = document.getElementById('export_scope');
        let optSelected = document.getElementById('opt_selected');

        document.getElementById('export_ids').value = ids;

        // Nếu có tick chọn đơn hàng -> Ưu tiên hiện và chọn dòng "Chỉ xuất các đơn đã chọn"
        if (ids !== '') {
            optSelected.style.display = 'block';
            scopeSelect.value = 'selected';
            optSelected.innerText = `Chỉ xuất các đơn hàng đã được Tích chọn (${ids.split(',').length} đơn)`;
        } else {
            optSelected.style.display = 'none';
            scopeSelect.value = 'all';
        }

        document.getElementById('export_modal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
