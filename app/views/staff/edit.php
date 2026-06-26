<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $staff */
/** @var array $current_permissions */
?>

<style>
    .Há»‡ thá»‘ng-header-bar {
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

    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card-title {
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

    /* CSS RIÃŠNG CHO PHÃ‚N QUYá»€N */
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
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2><a href="index.php?action=staff_list" style="text-decoration:none; color:#637381;">â†</a> Há»“ sÆ¡: <?php echo htmlspecialchars($staff['full_name'] ?? ''); ?></h2>
        <div>
            <a href="index.php?action=staff_list" class="btn-cancel">Há»§y</a>
            <button type="submit" class="btn-save">LÆ°u thay Ä‘á»•i</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight: 500;">âœ… LÆ°u thÃ´ng tin vÃ  phÃ¢n quyá»n thÃ nh cÃ´ng!</div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 68%;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">
                    ðŸ›¡ï¸ PhÃ¢n quyá»n chi tiáº¿t (Quyá»n quáº£n trá»‹)
                    <div style="float: right;">
                        <button type="button" onclick="checkAll(true)" style="background:#fff; border:1px solid #c4cdd5; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;">TÃ­ch chá»n táº¥t cáº£</button>
                        <button type="button" onclick="checkAll(false)" style="background:#fff; border:1px solid #c4cdd5; padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;">Bá» chá»n táº¥t cáº£</button>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">1. ÄÆ¡n hÃ ng</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_view" <?php echo in_array('order_view', $current_permissions) ? 'checked' : ''; ?>> Xem danh sÃ¡ch Ä‘Æ¡n hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_create" <?php echo in_array('order_create', $current_permissions) ? 'checked' : ''; ?>> Táº¡o Ä‘Æ¡n hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_edit" <?php echo in_array('order_edit', $current_permissions) ? 'checked' : ''; ?>> Sá»­a Ä‘Æ¡n hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_delete" <?php echo in_array('order_delete', $current_permissions) ? 'checked' : ''; ?>> Há»§y / XÃ³a Ä‘Æ¡n hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_export" <?php echo in_array('order_export', $current_permissions) ? 'checked' : ''; ?>> Xuáº¥t file Excel</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_price_edit" <?php echo in_array('order_price_edit', $current_permissions) ? 'checked' : ''; ?>> Chá»‰nh sá»­a giÃ¡ bÃ¡n thá»§ cÃ´ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="order_limit_view" <?php echo in_array('order_limit_view', $current_permissions) ? 'checked' : ''; ?>> Chá»‰ xem Ä‘Æ¡n do mÃ¬nh táº¡o</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">2. Sáº£n pháº©m & Danh má»¥c</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_view" <?php echo in_array('product_view', $current_permissions) ? 'checked' : ''; ?>> Xem sáº£n pháº©m</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_create" <?php echo in_array('product_create', $current_permissions) ? 'checked' : ''; ?>> ThÃªm má»›i / Sá»­a sáº£n pháº©m</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_delete" <?php echo in_array('product_delete', $current_permissions) ? 'checked' : ''; ?>> XÃ³a sáº£n pháº©m</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="product_cost_view" <?php echo in_array('product_cost_view', $current_permissions) ? 'checked' : ''; ?> style="accent-color:#cf1322;"> <b>Xem giÃ¡ vá»‘n</b></label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="category_manage" <?php echo in_array('category_manage', $current_permissions) ? 'checked' : ''; ?>> Quáº£n lÃ½ danh má»¥c</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">3. Quáº£n lÃ½ Kho</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="inventory_view" <?php echo in_array('inventory_view', $current_permissions) ? 'checked' : ''; ?>> Xem tá»“n kho</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="inventory_edit" <?php echo in_array('inventory_edit', $current_permissions) ? 'checked' : ''; ?>> Khá»Ÿi táº¡o & Äiá»u chá»‰nh kho</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="purchase_view" <?php echo in_array('purchase_view', $current_permissions) ? 'checked' : ''; ?>> Xem ÄÆ¡n Ä‘áº·t hÃ ng nháº­p</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="purchase_create" <?php echo in_array('purchase_create', $current_permissions) ? 'checked' : ''; ?>> Táº¡o / Sá»­a ÄÆ¡n nháº­p hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="supplier_manage" <?php echo in_array('supplier_manage', $current_permissions) ? 'checked' : ''; ?>> Quáº£n lÃ½ NhÃ  cung cáº¥p</label>
                    </div>
                </div>

                <div class="perm-group">
                    <div class="perm-group-title">4. KhÃ¡ch hÃ ng & BÃ¡o cÃ¡o</div>
                    <div class="perm-list">
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="customer_manage" <?php echo in_array('customer_manage', $current_permissions) ? 'checked' : ''; ?>> Quáº£n lÃ½ KhÃ¡ch hÃ ng</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="report_revenue" <?php echo in_array('report_revenue', $current_permissions) ? 'checked' : ''; ?>> Xem BÃ¡o cÃ¡o Doanh thu</label>
                        <label class="perm-item"><input type="checkbox" name="permissions[]" value="report_profit" <?php echo in_array('report_profit', $current_permissions) ? 'checked' : ''; ?> style="accent-color:#cf1322;"> <b>Xem BÃ¡o cÃ¡o Lá»£i nhuáº­n</b></label>
                    </div>
                </div>

                <p style="font-size: 13px; color: #637381; margin-top: 20px;">â„¹ï¸ <b>Máº¹o:</b> Äá»ƒ báº£o máº­t thÃ´ng tin lá»£i nhuáº­n, báº¡n khÃ´ng nÃªn cáº¥p quyá»n "Xem giÃ¡ vá»‘n" vÃ  "Xem bÃ¡o cÃ¡o lá»£i nhuáº­n" cho nhÃ¢n viÃªn bÃ¡n hÃ ng.</p>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ThÃ´ng tin nhÃ¢n viÃªn</div>
                <div class="row-flex">
                    <div class="form-group"><label>Há»</label><input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($staff['last_name'] ?? ''); ?>"></div>
                    <div class="form-group"><label>TÃªn <span style="color:red;">*</span></label><input type="text" name="first_name" class="form-control" required value="<?php echo htmlspecialchars($staff['first_name'] ?? ''); ?>"></div>
                </div>
                <div class="form-group">
                    <label>Email (TÃ i khoáº£n Ä‘Äƒng nháº­p)</label>
                    <input type="email" value="<?php echo htmlspecialchars($staff['email']); ?>" class="form-control" readonly style="background:#f4f6f8; cursor:not-allowed;" title="KhÃ´ng thá»ƒ Ä‘á»•i Email">
                </div>
                <div class="form-group">
                    <label>Äiá»‡n thoáº¡i <span style="color:red;">*</span></label>
                    <input type="text" name="phone" class="form-control" required value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>NhÃ³m Vai trÃ² máº·c Ä‘á»‹nh</label>
                    <select name="role" class="form-control" onchange="autoAssignPermissions(this.value)">
                        <option value="Admin" <?php echo ($staff['role'] == 'Admin') ? 'selected' : ''; ?>>Chá»§ cá»­a hÃ ng (Admin)</option>
                        <option value="NhÃ¢n viÃªn bÃ¡n hÃ ng" <?php echo ($staff['role'] == 'NhÃ¢n viÃªn bÃ¡n hÃ ng') ? 'selected' : ''; ?>>NhÃ¢n viÃªn bÃ¡n hÃ ng (Thu ngÃ¢n)</option>
                        <option value="NhÃ¢n viÃªn kho" <?php echo ($staff['role'] == 'NhÃ¢n viÃªn kho') ? 'selected' : ''; ?>>NhÃ¢n viÃªn kho</option>
                        <option value="TÃ¹y chá»‰nh" <?php echo ($staff['role'] == 'TÃ¹y chá»‰nh') ? 'selected' : ''; ?>>TÃ¹y chá»‰nh phÃ¢n quyá»n</option>
                    </select>
                </div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">Tráº¡ng thÃ¡i báº£o máº­t</div>
                <div style="font-size: 14px; margin-bottom: 10px;">
                    TÃ¬nh tráº¡ng tÃ i khoáº£n:
                    <?php if ($staff['status'] == 'Äang kÃ­ch hoáº¡t'): ?>
                        <span style="color:#108043; font-weight:bold;">â— ÄÃ£ xÃ¡c nháº­n máº­t kháº©u</span>
                    <?php else: ?>
                        <span style="color:#cf1322; font-weight:bold;">â—‹ Äang chá» xÃ¡c nháº­n</span>
                    <?php endif; ?>
                </div>
                <p style="font-size: 12px; color: #637381; margin: 0;">(Chá»‰ nhÃ¢n viÃªn Ä‘Ã£ kÃ­ch hoáº¡t má»›i cÃ³ thá»ƒ Ä‘Äƒng nháº­p vÃ o há»‡ thá»‘ng Há»‡ thá»‘ng).</p>
            </div>
        </div>
    </div>
</form>

<script>
    // HÃ m chá»n nhanh táº¥t cáº£ checkbox
    function checkAll(status) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = status);
    }

    // HÃ m tá»± Ä‘á»™ng tÃ­ch cÃ¡c quyá»n máº«u theo Vai trÃ² (Chá»©c nÄƒng cao cáº¥p Há»‡ thá»‘ng)
    function autoAssignPermissions(role) {
        if (role === 'TÃ¹y chá»‰nh') return; // KhÃ´ng can thiá»‡p náº¿u chá»n tÃ¹y chá»‰nh

        checkAll(false); // XÃ³a tráº¯ng trÆ°á»›c khi gÃ¡n

        if (role === 'Admin') {
            checkAll(true); // Admin cÃ³ táº¥t cáº£ quyá»n
        } else if (role === 'NhÃ¢n viÃªn bÃ¡n hÃ ng') {
            const salePerms = ['order_view', 'order_create', 'product_view', 'customer_manage'];
            salePerms.forEach(p => {
                let cb = document.querySelector(`input[value="${p}"]`);
                if (cb) cb.checked = true;
            });
        } else if (role === 'NhÃ¢n viÃªn kho') {
            const inventoryPerms = ['product_view', 'inventory_view', 'inventory_edit', 'purchase_view', 'purchase_create', 'supplier_manage'];
            inventoryPerms.forEach(p => {
                let cb = document.querySelector(`input[value="${p}"]`);
                if (cb) cb.checked = true;
            });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

