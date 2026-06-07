<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $staff */
/** @var array $current_permissions */
?>

<style>
    .sapo-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-cancel {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        color: #212b36;
        text-decoration: none;
        font-weight: 500;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #212b36;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        box-sizing: border-box;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    /* CSS RIÊNG CHO PHÂN QUYỀN */
    .perm-group {
        margin-bottom: 20px;
    }

    .perm-group-title {
        font-weight: bold;
        color: #0050b3;
        background: #e6f7ff;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .perm-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 15px;
    }

    .perm-item {
        flex: 0 0 calc(33.33% - 10px);
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        color: #212b36;
    }

    .perm-item input {
        margin-top: 3px;
        cursor: pointer;
    }
</style>

<form action="index.php?action=edit_staff&id=<?php echo $staff['id']; ?>" method="POST">
    <div class="sapo-header-bar">
        <h2><a href="index.php?action=staff_list" style="text-decoration:none; color:#637381;">←</a> Hồ sơ: <?php echo htmlspecialchars($staff['full_name'] ?? ''); ?></h2>
        <div>
            <a href="index.php?action=staff_list" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight: 500;">✅ Lưu thông tin và phân quyền thành công!</div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 68%;">
            <div class="sapo-card">
                <div class="sapo-card-title">
                    🛡️ Phân quyền chi tiết (Quyền quản trị)
                    <div style="float: right;">
                        <button type="button" onclick="checkAll(true)" style="background:#fff; border:1px solid #c4cdd5; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;">Tích chọn tất cả</button>
                        <button type="button" onclick="checkAll(false)" style="background:#fff; border:1px solid #c4cdd5; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;">Bỏ chọn tất cả</button>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">1. Đơn hàng</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_view" <?php echo in_array('order_view', $current_permissions) ? 'checked' : ''; ?>> Xem danh sách đơn hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_create" <?php echo in_array('order_create', $current_permissions) ? 'checked' : ''; ?>> Tạo đơn hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_edit" <?php echo in_array('order_edit', $current_permissions) ? 'checked' : ''; ?>> Sửa đơn hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_delete" <?php echo in_array('order_delete', $current_permissions) ? 'checked' : ''; ?>> Hủy / Xóa đơn hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_export" <?php echo in_array('order_export', $current_permissions) ? 'checked' : ''; ?>> Xuất file Excel</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_price_edit" <?php echo in_array('order_price_edit', $current_permissions) ? 'checked' : ''; ?>> Chỉnh sửa giá bán thủ công</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_limit_view" <?php echo in_array('order_limit_view', $current_permissions) ? 'checked' : ''; ?>> Chỉ xem đơn do mình tạo</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">2. Sản phẩm & Danh mục</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_view" <?php echo in_array('product_view', $current_permissions) ? 'checked' : ''; ?>> Xem sản phẩm</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_create" <?php echo in_array('product_create', $current_permissions) ? 'checked' : ''; ?>> Thêm mới / Sửa sản phẩm</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_delete" <?php echo in_array('product_delete', $current_permissions) ? 'checked' : ''; ?>> Xóa sản phẩm</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_cost_view" <?php echo in_array('product_cost_view', $current_permissions) ? 'checked' : ''; ?> style="accent-color:#cf1322;"> <b>Xem giá vốn</b></label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="category_manage" <?php echo in_array('category_manage', $current_permissions) ? 'checked' : ''; ?>> Quản lý danh mục</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">3. Quản lý Kho</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="inventory_view" <?php echo in_array('inventory_view', $current_permissions) ? 'checked' : ''; ?>> Xem tồn kho</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="inventory_edit" <?php echo in_array('inventory_edit', $current_permissions) ? 'checked' : ''; ?>> Khởi tạo & Điều chỉnh kho</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="purchase_view" <?php echo in_array('purchase_view', $current_permissions) ? 'checked' : ''; ?>> Xem Đơn đặt hàng nhập</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="purchase_create" <?php echo in_array('purchase_create', $current_permissions) ? 'checked' : ''; ?>> Tạo / Sửa Đơn nhập hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="supplier_manage" <?php echo in_array('supplier_manage', $current_permissions) ? 'checked' : ''; ?>> Quản lý Nhà cung cấp</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">4. Khách hàng & Báo cáo</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="customer_manage" <?php echo in_array('customer_manage', $current_permissions) ? 'checked' : ''; ?>> Quản lý Khách hàng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="report_revenue" <?php echo in_array('report_revenue', $current_permissions) ? 'checked' : ''; ?>> Xem Báo cáo Doanh thu</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="report_profit" <?php echo in_array('report_profit', $current_permissions) ? 'checked' : ''; ?> style="accent-color:#cf1322;"> <b>Xem Báo cáo Lợi nhuận</b></label>
                    </div>
                </div>

                <p style="font-size: 13px; color: #637381; margin-top: 20px;">ℹ️ <b>Mẹo:</b> Để bảo mật thông tin lợi nhuận, bạn không nên cấp quyền "Xem giá vốn" và "Xem báo cáo lợi nhuận" cho nhân viên bán hàng.</p>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin nhân viên</div>
                <div class="row-flex">
                    <div class="form-group"><label>Họ</label><input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($staff['last_name'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Tên <span style="color:red;">*</span></label><input type="text" name="first_name" class="form-control" required value="<?php echo htmlspecialchars($staff['first_name'] ?? ''); ?>"></div>
                </div>
                <div class="form-group">
                    <label>Email (Tài khoản đăng nhập)</label>
                    <input type="email" value="<?php echo htmlspecialchars($staff['email']); ?>" class="form-control" readonly style="background:#f4f6f8; cursor:not-allowed;" title="Không thể đổi Email">
                </div>
                <div class="form-group">
                    <label>Điện thoại <span style="color:red;">*</span></label>
                    <input type="text" name="phone" class="form-control" required value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Nhóm Vai trò mặc định</label>
                    <select name="role" class="form-control" onchange="autoAssignPermissions(this.value)">
                        <option value="Admin" <?php echo ($staff['role'] == 'Admin') ? 'selected' : ''; ?>>Chủ cửa hàng (Admin)</option>
                        <option value="Nhân viên bán hàng" <?php echo ($staff['role'] == 'Nhân viên bán hàng') ? 'selected' : ''; ?>>Nhân viên bán hàng (Thu ngân)</option>
                        <option value="Nhân viên kho" <?php echo ($staff['role'] == 'Nhân viên kho') ? 'selected' : ''; ?>>Nhân viên kho</option>
                        <option value="Tùy chỉnh" <?php echo ($staff['role'] == 'Tùy chỉnh') ? 'selected' : ''; ?>>Tùy chỉnh phân quyền</option>
                    </select>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Trạng thái bảo mật</div>
                <div style="font-size: 14px; margin-bottom: 10px;">
                    Tình trạng tài khoản:
                    <?php if ($staff['status'] == 'Đang kích hoạt'): ?>
                        <span style="color:#108043; font-weight:bold;">● Đã xác nhận mật khẩu</span>
                    <?php else: ?>
                        <span style="color:#cf1322; font-weight:bold;">○ Đang chờ xác nhận</span>
                    <?php endif; ?>
                </div>
                <p style="font-size: 12px; color: #637381; margin: 0;">(Chỉ nhân viên đã kích hoạt mới có thể đăng nhập vào hệ thống Sapo).</p>
            </div>
        </div>
    </div>
</form>

<script>
    // Hàm chọn nhanh tất cả checkbox
    function checkAll(status) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = status);
    }

    // Hàm tự động tích các quyền mẫu theo Vai trò (Chức năng cao cấp Sapo)
    function autoAssignPermissions(role) {
        if (role === 'Tùy chỉnh') return; // Không can thiệp nếu chọn tùy chỉnh

        checkAll(false); // Xóa trắng trước khi gán

        if (role === 'Admin') {
            checkAll(true); // Admin có tất cả quyền
        } else if (role === 'Nhân viên bán hàng') {
            const salePerms = ['order_view', 'order_create', 'product_view', 'customer_manage'];
            salePerms.forEach(p => {
                let cb = document.querySelector(`input[value="${p}"]`);
                if (cb) cb.checked = true;
            });
        } else if (role === 'Nhân viên kho') {
            const inventoryPerms = ['product_view', 'inventory_view', 'inventory_edit', 'purchase_view', 'purchase_create', 'supplier_manage'];
            inventoryPerms.forEach(p => {
                let cb = document.querySelector(`input[value="${p}"]`);
                if (cb) cb.checked = true;
            });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
