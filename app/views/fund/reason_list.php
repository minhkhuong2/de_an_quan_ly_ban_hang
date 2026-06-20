<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $receipt_reasons
 * @var array $expense_reasons
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

    /* Tabs CSS */
    .tabs {
        display: flex;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        border-radius: 8px 8px 0 0;
    }

    .tab-item {
        padding: 15px 25px;
        font-size: 15px;
        font-weight: 600;
        color: #637381;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }

    .tab-item.active {
        color: #0088ff;
        border-bottom: 2px solid #0088ff;
    }

    .tab-content {
        display: none;
        padding: 20px;
    }

    .tab-content.active {
        display: block;
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

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-sys {
        background: #f4f6f8;
        color: #637381;
        border: 1px solid #c4cdd5;
    }

    .badge-custom {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
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

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        box-sizing: border-box;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh mục: Lý do thu chi</div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Thêm mới lý do thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_upd'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật lý do thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_del'])): ?>
    <div style="background:#fff8ea; color:#8a6100; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #ffea8a; font-weight:500;">🗑️ Đã xóa lý do thành công!</div>
<?php endif; ?>

<div class="v3-card">
    <div class="tabs">
        <div class="tab-item active" onclick="switchTab('receipt')">📥 Lý do thu</div>
        <div class="tab-item" onclick="switchTab('expense')">📤 Lý do chi</div>
    </div>

    <div id="tab-receipt" class="tab-content active">
        <div style="text-align: right; margin-bottom: 15px;">
            <button class="btn-primary" onclick="openModal('receipt')">+ Thêm lý do thu</button>
        </div>
        <table class="v3-table">
            <thead>
                <tr>
                    <th>Nội dung lý do thu</th>
                    <th>Loại lý do</th>
                    <th>Hạch toán báo cáo KQKD</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipt_reasons as $r): ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0)" style="color:#0088ff; font-weight:bold; text-decoration:none;"
                                onclick="openEditModal(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['reason_name'], ENT_QUOTES); ?>', <?php echo $r['is_system']; ?>, <?php echo $r['is_reported']; ?>, '<?php echo $r['type']; ?>', '')">
                                ✏️ <?php echo htmlspecialchars($r['reason_name']); ?>
                            </a>
                        </td>
                        <td><?php echo $r['is_system'] ? '<span class="badge badge-sys">Mặc định hệ thống</span>' : '<span class="badge badge-custom">Tự tạo</span>'; ?></td>
                        <td><?php echo $r['is_reported'] ? '✅ Có ghi nhận' : '❌ Không'; ?></td>
                        <td style="text-align:right;">
                            <?php if ($r['is_system'] == 0): ?>
                                <a href="index.php?action=delete_fund_reason&id=<?php echo $r['id']; ?>&tab=receipt" onclick="return confirm('Bạn chắc chắn muốn xóa lý do này?')" style="color:#ff4d4f; text-decoration:none; font-weight:bold;">Xóa</a>
                            <?php else: ?>
                                <span style="color:#919eab;">Khóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="tab-expense" class="tab-content">
        <div style="text-align: right; margin-bottom: 15px;">
            <button class="btn-primary" onclick="openModal('expense')">+ Thêm lý do chi</button>
        </div>
        <table class="v3-table">
            <thead>
                <tr>
                    <th>Nội dung lý do chi</th>
                    <th>Nhóm chi phí (TT88)</th>
                    <th>Loại lý do</th>
                    <th>Báo cáo KQKD</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expense_reasons as $e): ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0)" style="color:#0088ff; font-weight:bold; text-decoration:none;"
                                onclick="openEditModal(<?php echo $e['id']; ?>, '<?php echo htmlspecialchars($e['reason_name'], ENT_QUOTES); ?>', <?php echo $e['is_system']; ?>, <?php echo $e['is_reported']; ?>, '<?php echo $e['type']; ?>', '<?php echo htmlspecialchars($e['expense_category'] ?? '', ENT_QUOTES); ?>')">
                                ✏️ <?php echo htmlspecialchars($e['reason_name']); ?>
                            </a>
                        </td>
                        <td style="color:#e67e22; font-weight:600;"><?php echo htmlspecialchars($e['expense_category'] ?: '---'); ?></td>
                        <td><?php echo $e['is_system'] ? '<span class="badge badge-sys">Mặc định hệ thống</span>' : '<span class="badge badge-custom">Tự tạo</span>'; ?></td>
                        <td><?php echo $e['is_reported'] ? '✅ Có' : '❌ Không'; ?></td>
                        <td style="text-align:right;">
                            <?php if ($e['is_system'] == 0): ?>
                                <a href="index.php?action=delete_fund_reason&id=<?php echo $e['id']; ?>&tab=expense" onclick="return confirm('Bạn chắc chắn muốn xóa lý do này?')" style="color:#ff4d4f; text-decoration:none; font-weight:bold;">Xóa</a>
                            <?php else: ?>
                                <span style="color:#919eab;">Khóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="reason_modal" class="modal">
    <div class="modal-content">
        <h3 id="modal_title" style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thêm lý do</h3>
        <form action="index.php?action=store_fund_reason" method="POST">
            <input type="hidden" name="type" id="modal_type">

            <div class="form-group">
                <label>Nội dung lý do <span>*</span></label>
                <input type="text" name="reason_name" class="form-control" required placeholder="Nhập lý do...">
            </div>

            <div class="form-group" style="background:#f4f9ff; padding:10px; border-radius:4px;">
                <label style="cursor:pointer; display:flex; align-items:center; gap:8px; margin:0;">
                    <input type="checkbox" name="is_reported" id="chk_reported" value="1" checked onchange="toggleExpenseCategory()" style="width:16px; height:16px;">
                    Ghi nhận vào báo cáo Kết quả kinh doanh
                </label>
            </div>

            <div class="form-group" id="expense_category_block" style="display:none;">
                <label>Chọn Nhóm chi phí (Theo TT88-2021/TT-BTC) <span>*</span></label>
                <select name="expense_category" class="form-control">
                    <option value="Chi phí nhân công (CPNC)">Chi phí nhân công (CPNC)</option>
                    <option value="Chi phí điện (CPD)">Chi phí điện (CPD)</option>
                    <option value="Chi phí nước (CPN)">Chi phí nước (CPN)</option>
                    <option value="Chi phí viễn thông (CPVT)">Chi phí viễn thông (CPVT)</option>
                    <option value="Chi phí thuê mặt bằng (CPMB)">Chi phí thuê mặt bằng (CPMB)</option>
                    <option value="Chi phí quản lý (CPQL)">Chi phí quản lý (CPQL)</option>
                    <option value="Chi phí khác (CPK)">Chi phí khác (CPK)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('reason_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu lý do</button>
            </div>
        </form>
    </div>
</div>

<div id="edit_reason_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#212b36;">⚙️ Chỉnh sửa lý do thu chi</h3>
        <form action="index.php?action=update_fund_reason" method="POST">
            <input type="hidden" name="id" id="edit_modal_id">

            <div class="form-group">
                <label>Nội dung lý do <span>*</span></label>
                <input type="text" name="reason_name" id="edit_modal_name" class="form-control" required>
            </div>

            <div id="edit_reported_block" class="form-group" style="background:#fafbfc; padding:10px; border-radius:4px; border:1px solid #dfe3e8;">
                <label style="cursor:pointer; display:flex; align-items:center; gap:8px; margin:0;">
                    <input type="checkbox" name="is_reported" id="edit_chk_reported" value="1" onchange="toggleEditExpenseCategory()" style="width:16px; height:16px;">
                    Ghi nhận vào báo cáo Kết quả kinh doanh
                </label>
                <p id="system_warning_text" style="margin:5px 0 0 0; font-size:12px; color:#919eab; display:none;">⚠️ Lý do mặc định hệ thống không được phép đổi cấu hình báo cáo.</p>
            </div>

            <div class="form-group" id="edit_expense_category_block" style="display:none;">
                <label>Chọn Nhóm chi phí (Theo TT88-2021/TT-BTC) <span>*</span></label>
                <select name="expense_category" id="edit_expense_category" class="form-control">
                    <option value="Chi phí nhân công (CPNC)">Chi phí nhân công (CPNC)</option>
                    <option value="Chi phí điện (CPD)">Chi phí điện (CPD)</option>
                    <option value="Chi phí nước (CPN)">Chi phí nước (CPN)</option>
                    <option value="Chi phí viễn thông (CPVT)">Chi phí viễn thông (CPVT)</option>
                    <option value="Chi phí thuê mặt bằng (CPMB)">Chi phí thuê mặt bằng (CPMB)</option>
                    <option value="Chi phí quản lý (CPQL)">Chi phí quản lý (CPQL)</option>
                    <option value="Chi phí khác (CPK)">Chi phí khác (CPK)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('edit_reason_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Xử lý chuyển Tab Thu/Chi
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));

        document.getElementById('tab-' + tabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    // Modal Thêm Mới
    function openModal(type) {
        document.getElementById('modal_type').value = type;
        document.getElementById('modal_title').innerText = (type === 'receipt') ? '📥 Thêm lý do thu' : '📤 Thêm lý do chi';
        document.getElementById('reason_modal').style.display = 'flex';
        toggleExpenseCategory();
    }

    function toggleExpenseCategory() {
        let type = document.getElementById('modal_type').value;
        let isChecked = document.getElementById('chk_reported').checked;
        document.getElementById('expense_category_block').style.display = (type === 'expense' && isChecked) ? 'block' : 'none';
    }

    // Modal Cập Nhật
    function openEditModal(id, name, isSystem, isReported, type, category) {
        document.getElementById('edit_modal_id').value = id;
        document.getElementById('edit_modal_name').value = name;

        let chkReported = document.getElementById('edit_chk_reported');
        chkReported.checked = (isReported === 1);

        // Cấu hình vô hiệu hóa nếu là cấu hình hệ thống
        if (isSystem === 1) {
            chkReported.disabled = true;
            document.getElementById('system_warning_text').style.display = 'block';
        } else {
            chkReported.disabled = false;
            document.getElementById('system_warning_text').style.display = 'none';
        }

        // Đổ dữ liệu nhóm chi phí nếu có
        let catSelect = document.getElementById('edit_expense_category');
        if (category) {
            catSelect.value = category;
        }

        // Lưu tạm type vào thuộc tính dataset để tính toán ẩn hiện
        document.getElementById('edit_reason_modal').dataset.type = type;

        document.getElementById('edit_reason_modal').style.display = 'flex';
        toggleEditExpenseCategory();
    }

    function toggleEditExpenseCategory() {
        let modal = document.getElementById('edit_reason_modal');
        let type = modal.dataset.type;
        let isChecked = document.getElementById('edit_chk_reported').checked;

        document.getElementById('edit_expense_category_block').style.display = (type === 'expense' && isChecked) ? 'block' : 'none';
    }

    // Tự động mở đúng tab nếu vừa thêm/sửa/xóa xong
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'expense') {
            document.querySelectorAll('.tab-item')[1].click();
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
