<?php require_once __DIR__ . '/../layout/header.php'; ?>

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

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
        font-size: 16px;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 22px;
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

    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #c4cdd5;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #0088ff;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .toggle-row {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #dfe3e8;
    }

    .toggle-row:last-child {
        border-bottom: none;
    }

    .toggle-title {
        font-weight: 600;
        color: #212b36;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .toggle-desc {
        font-size: 13px;
        color: #637381;
        line-height: 1.4;
        margin: 0;
    }

    /* Input % */
    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group input {
        border-radius: 4px 0 0 4px;
        border-right: none;
        width: 120px;
        text-align: right;
    }

    .input-group-text {
        padding: 10px 15px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 0 4px 4px 0;
        color: #637381;
        font-weight: 600;
    }

    /* Lớp phủ làm mờ khi tắt quản lý thuế */
    #tax_settings_wrapper {
        transition: 0.3s;
    }

    .disabled-overlay {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Cấu hình Thuế (VAT)</div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn-outline" style="color: #108043; border-color: #8ce09f; background: #eafff0;" onclick="applyPreset('hkd')">🎯 Cấu hình Hộ Kinh Doanh</button>
        <button type="button" class="btn-outline" style="color: #0056b3; border-color: #b3d4ff; background: #e5f0ff;" onclick="applyPreset('dn')">🏢 Cấu hình Doanh Nghiệp</button>
        <button type="button" class="btn-primary" onclick="document.getElementById('frm_tax').submit()">💾 Lưu cấu hình</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật cấu hình thuế thành công!</div>
<?php endif; ?>

<form id="frm_tax" action="index.php?action=update_tax_settings" method="POST">

    <div class="v3-card">
        <div class="card-body">
            <div class="toggle-row" style="border:none; padding:0;">
                <label class="switch" style="margin-top: 2px;">
                    <input type="checkbox" name="is_tax_enabled" id="is_tax_enabled" value="1" <?php echo ($tax['is_tax_enabled'] ?? 0) == 1 ? 'checked' : ''; ?> onchange="toggleMasterTax()">
                    <span class="slider"></span>
                </label>
                <div>
                    <div class="toggle-title" style="font-size: 16px;">Quản lý thông tin Thuế cho cửa hàng</div>
                    <p class="toggle-desc">Khi bật, hệ thống sẽ kích hoạt tính năng thuế. Các đơn hàng, đơn nhập hàng sẽ tự động hạch toán thuế suất dựa theo cấu hình bên dưới.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="tax_settings_wrapper" class="<?php echo ($tax['is_tax_enabled'] ?? 0) == 0 ? 'disabled-overlay' : ''; ?>">

        <div class="v3-card">
            <div class="card-header">1. Cấu hình chung</div>
            <div class="card-body" style="padding-top: 0;">

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="default_tax_sales" id="default_tax_sales" value="1" <?php echo ($tax['default_tax_sales'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Mặc định tính thuế khi bán hàng</div>
                        <p class="toggle-desc">Các giao dịch bán hàng mới (Đơn hàng Web, POS) sẽ tự động được áp dụng mức thuế bán hàng.</p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="default_tax_purchases" id="default_tax_purchases" value="1" <?php echo ($tax['default_tax_purchases'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Mặc định tính thuế khi nhập hàng</div>
                        <p class="toggle-desc">Các giao dịch Đặt hàng nhập, Nhập hàng sẽ được áp dụng thuế. Hữu ích cho doanh nghiệp cần quản lý VAT đầu vào.</p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="price_includes_tax" id="price_includes_tax" value="1" <?php echo ($tax['price_includes_tax'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Giá đã bao gồm thuế</div>
                        <p class="toggle-desc">
                            <b style="color:#0088ff;">Bật:</b> Giá sản phẩm là giá cuối cùng đã có thuế (Hệ thống tự bóc tách thuế).<br>
                            <b style="color:#d82c0d;">Tắt:</b> Giá sản phẩm là giá chưa thuế (Hệ thống sẽ cộng thêm tiền thuế vào tổng bill).
                        </p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="tax_on_shipping" id="tax_on_shipping" value="1" <?php echo ($tax['tax_on_shipping'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Ghi nhận thuế lên phí vận chuyển</div>
                        <p class="toggle-desc">Phí giao hàng thu của khách cũng sẽ bị áp mức thuế vận chuyển tương ứng.</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Mức Thuế suất chung (%)</div>
            <div class="card-body">
                <p style="font-size: 13px; color: #637381; margin-bottom: 20px;">Hệ thống hỗ trợ nhập số thập phân tối đa 2 chữ số (ví dụ: 1.5, 8.00, 10.00) để tính toán chuẩn xác từng đồng lẻ.</p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <div class="form-group">
                            <label class="toggle-title">Thuế nhập hàng chung <span>*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="general_purchase_tax_rate" id="general_purchase_tax_rate" class="form-control" value="<?php echo number_format($tax['general_purchase_tax_rate'] ?? 0, 2, '.', ''); ?>" required>
                                <div class="input-group-text">%</div>
                            </div>
                            <p class="toggle-desc" style="margin-top: 5px;">Áp dụng cho các đơn nhập kho từ Nhà cung cấp.</p>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label class="toggle-title">Thuế bán hàng chung <span>*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="general_sales_tax_rate" id="general_sales_tax_rate" class="form-control" value="<?php echo number_format($tax['general_sales_tax_rate'] ?? 0, 2, '.', ''); ?>" required>
                                <div class="input-group-text">%</div>
                            </div>
                            <p class="toggle-desc" style="margin-top: 5px;">Áp dụng cho đơn xuất bán ra cho Khách hàng.</p>
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 20px 0;">

                <div class="form-group" style="width: 50%;">
                    <label class="toggle-title">Thuế vận chuyển</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="shipping_tax_rate" id="shipping_tax_rate" class="form-control" value="<?php echo number_format($tax['shipping_tax_rate'] ?? 0, 2, '.', ''); ?>">
                        <div class="input-group-text">%</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // Logic mờ/sáng khối cài đặt khi bật tắt Quản lý thuế
    function toggleMasterTax() {
        let isEnabled = document.getElementById('is_tax_enabled').checked;
        let wrapper = document.getElementById('tax_settings_wrapper');
        if (isEnabled) {
            wrapper.classList.remove('disabled-overlay');
        } else {
            wrapper.classList.add('disabled-overlay');
        }
    }

    // Nút thần thánh: Tự động điền cấu hình chuẩn Sapo
    function applyPreset(type) {
        // Đảm bảo đã bật Quản lý thuế
        document.getElementById('is_tax_enabled').checked = true;
        toggleMasterTax();

        if (type === 'hkd') {
            if (!confirm('Áp dụng Cấu hình HỘ KINH DOANH?\n\n- Thuế nhập: 0%\n- Thuế bán: 1.5%\n- Giá đã bao gồm thuế: Bật')) return;

            document.getElementById('default_tax_sales').checked = true;
            document.getElementById('default_tax_purchases').checked = false;
            document.getElementById('price_includes_tax').checked = true;

            document.getElementById('general_purchase_tax_rate').value = '0.00';
            document.getElementById('general_sales_tax_rate').value = '1.50';

        } else if (type === 'dn') {
            if (!confirm('Áp dụng Cấu hình DOANH NGHIỆP (Khấu trừ VAT)?\n\n- Thuế nhập: 10%\n- Thuế bán: 10%\n- Giá đã bao gồm thuế: Tắt (Cộng thêm VAT vào bill)')) return;

            document.getElementById('default_tax_sales').checked = true;
            document.getElementById('default_tax_purchases').checked = true;
            document.getElementById('price_includes_tax').checked = false;

            document.getElementById('general_purchase_tax_rate').value = '10.00';
            document.getElementById('general_sales_tax_rate').value = '10.00';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
