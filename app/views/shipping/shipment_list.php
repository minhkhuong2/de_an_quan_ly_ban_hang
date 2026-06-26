<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var string $keyword
 * @var string $status_filter
 * @var string $recon_filter
 * @var array $branches
 * @var array $shipments
 */
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 24px;
        font-weight: bold;
        color: #212b36;
    }

    .v3-filter-bar {
        background: #fff;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dfe3e8;
        border-bottom: none;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    /* Thanh Thao tác hàng loạt (Ẩn mặc định) */
    .bulk-action-bar {
        background: #e5f0ff;
        padding: 12px 15px;
        border: 1px solid #b3d4ff;
        border-bottom: none;
        display: none;
        align-items: center;
        gap: 15px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        background: #fff;
        border: 1px solid #dfe3e8;
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

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-delivering {
        background: #e5f0ff;
        color: #0088ff;
    }

    .badge-delivered {
        background: #eafff0;
        color: #108043;
    }

    .badge-returning {
        background: #fff8ea;
        color: #8a6100;
    }

    /* Modals */
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
        width: 500px;
        padding: 25px;
        border-radius: 8px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">📦 Quản lý danh sách Vận đơn</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="alert('Tính năng Xuất Excel đang cập nhật!')">📥 Xuất file</button>
        <a href="index.php?action=order_list" class="btn-primary">+ Tạo đơn hàng mới</a>
    </div>
</div>

<?php if (isset($_GET['success_status'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #33d067;">✅ Cập nhật trạng thái vận đơn thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_recon'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #33d067;">💰 Đối soát thành công! Hệ thống đã tự động tạo Phiếu Thu tiền COD vào Sổ quỹ.</div>
<?php endif; ?>

<form class="v3-filter-bar" method="GET" action="index.php">
    <input type="hidden" name="action" value="shipment_list">

    <input type="text" name="keyword" class="v3-form-control" placeholder="Tìm Mã Vận đơn, Tên KH..." value="<?php echo htmlspecialchars($keyword); ?>" style="width: 250px;">

    <select name="status" class="v3-form-control">
        <option value="all">Tất cả Trạng thái Giao hàng</option>
        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Chờ lấy hàng</option>
        <option value="delivering" <?php echo $status_filter == 'delivering' ? 'selected' : ''; ?>>Đang giao hàng</option>
        <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>Đã giao thành công</option>
        <option value="returning" <?php echo $status_filter == 'returning' ? 'selected' : ''; ?>>Đang hoàn hàng</option>
    </select>

    <select name="recon_status" class="v3-form-control">
        <option value="all">Trạng thái Đối soát</option>
        <option value="unreconciled" <?php echo $recon_filter == 'unreconciled' ? 'selected' : ''; ?>>🔴 Chưa đối soát (Đang nợ COD)</option>
        <option value="reconciled" <?php echo $recon_filter == 'reconciled' ? 'selected' : ''; ?>>✅ Đã đối soát (Đã nhận tiền)</option>
    </select>

    <button type="submit" class="btn-primary">Lọc Vận đơn</button>
</form>

<div id="bulk_bar" class="bulk-action-bar">
    <span style="font-weight:600; color:#0056b3;">Đã chọn <span id="selected_count">0</span> vận đơn:</span>
    <button class="btn-outline" onclick="openStatusModal()">🔄 Đổi trạng thái</button>
    <button class="btn-primary" style="background:#108043;" onclick="openReconModal()">💰 Thực hiện Đối soát (Thu tiền)</button>
    <button class="btn-outline" onclick="alert('Tính năng In Phiếu bàn giao đang cập nhật')">🖨️ In Phiếu bàn giao</button>
</div>

<table class="v3-table">
    <thead>
        <tr>
            <th style="width:40px; text-align:center;"><input type="checkbox" id="check_all" onclick="toggleCheckAll()"></th>
            <th>Mã Vận Đơn</th>
            <th>Mã Đơn Hệ thống / Khách hàng</th>
            <th>Đối tác</th>
            <th>Trạng thái Giao / Đối soát</th>
            <th style="text-align:right;">Tiền COD</th>
            <th style="text-align:right;">Phí Ship</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($shipments)): ?>
            <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:#637381;">Không tìm thấy vận đơn nào phù hợp.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($shipments as $s): ?>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" class="row-checkbox" value="<?php echo $s['id']; ?>" onclick="updateBulkBar()"></td>
                    <td><b style="color:#0088ff;"><?php echo htmlspecialchars($s['tracking_code']); ?></b></td>
                    <td>
                        <a href="index.php?action=view_order&id=<?php echo $s['order_id']; ?>" style="font-weight:bold; color:#212b36; text-decoration:none;">📦 <?php echo $s['order_code']; ?></a><br>
                        <small style="color:#637381;">👤 <?php echo htmlspecialchars($s['customer_name']); ?></small>
                    </td>
                    <td><b style="text-transform:uppercase; color:#e67e22;"><?php echo $s['partner_code']; ?></b></td>
                    <td>
                        <?php
                        if ($s['status'] == 'delivered') echo '<span class="badge badge-delivered">Đã giao</span>';
                        elseif ($s['status'] == 'delivering') echo '<span class="badge badge-delivering">Đang giao</span>';
                        elseif ($s['status'] == 'returning') echo '<span class="badge badge-returning">Đang hoàn</span>';
                        else echo '<span class="badge" style="background:#f4f6f8; border:1px solid #c4cdd5;">' . $s['status'] . '</span>';
                        ?>
                        <br>
                        <?php echo $s['recon_status'] == 'reconciled' ? '<span style="font-size:12px; color:#108043;">✅ Đã Đ.Soát</span>' : '<span style="font-size:12px; color:#d82c0d;">🔴 Chưa Đ.Soát</span>'; ?>
                    </td>
                    <td style="text-align:right; font-weight:bold; color:#0088ff;"><?php echo number_format($s['cod_amount'], 0, '', '.'); ?> đ</td>
                    <td style="text-align:right; color:#d82c0d;">- <?php echo number_format($s['shipping_fee'], 0, '', '.'); ?> đ</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="status_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">🔄 Cập nhật trạng thái thủ công</h3>
        <p style="font-size:13px; color:#637381;">Áp dụng cho các vận đơn Shipper ngoài hoặc hệ thống chưa kịp đồng bộ.</p>
        <form action="index.php?action=update_shipment_status" method="POST">
            <input type="hidden" name="shipment_ids" id="status_shipment_ids">
            <div class="form-group" style="margin-top:15px;">
                <label>Trạng thái mới</label>
                <select name="new_status" class="v3-form-control">
                    <option value="delivering">🚚 Đang giao hàng</option>
                    <option value="delivered">✅ Đã giao thành công</option>
                    <option value="returning">🔄 Đang hoàn hàng</option>
                    <option value="returned">🔙 Đã hoàn kho</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('status_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu Cập Nhật</button>
            </div>
        </form>
    </div>
</div>

<div id="recon_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color:#108043;">💰 Ghi nhận Đối soát (Thu tiền COD)</h3>
        <p style="font-size:13px; color:#d82c0d; background:#ffe4e4; padding:10px; border-radius:4px;">⚠️ Lưu ý: Chỉ chọn các đơn CÙNG MỘT HÃNG VẬN CHUYỂN và ĐÃ GIAO/HOÀN để tránh lỗi kế toán!</p>
        <form action="index.php?action=reconcile_shipments" method="POST">
            <input type="hidden" name="recon_shipment_ids" id="recon_shipment_ids">

            <div class="form-group" style="margin-top:15px;">
                <label>Chi nhánh ghi nhận Tiền vào sổ quỹ <span>*</span></label>
                <select name="recon_branch_id" class="v3-form-control" required>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ghi chú đối soát</label>
                <input type="text" name="recon_note" class="v3-form-control" placeholder="VD: Đối soát GHN tuần 1 tháng 6">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('recon_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary" style="background:#108043;">Xác nhận Tạo Phiếu Đối Soát</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Logic Checkbox Hàng loạt
    function toggleCheckAll() {
        let isChecked = document.getElementById('check_all').checked;
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = isChecked);
        updateBulkBar();
    }

    function updateBulkBar() {
        let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        let bulkBar = document.getElementById('bulk_bar');
        document.getElementById('selected_count').innerText = checkedCount;

        if (checkedCount > 0) {
            bulkBar.style.display = 'flex';
        } else {
            bulkBar.style.display = 'none';
            document.getElementById('check_all').checked = false;
        }
    }

    // Mở Modal Trạng thái
    function openStatusModal() {
        let ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        document.getElementById('status_shipment_ids').value = ids;
        document.getElementById('status_modal').style.display = 'flex';
    }

    // Mở Modal Đối soát
    function openReconModal() {
        let ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        document.getElementById('recon_shipment_ids').value = ids;
        document.getElementById('recon_modal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
