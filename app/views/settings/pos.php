<?php require_once __DIR__ . '/../layout/header.php'; ?>

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
        margin-bottom: 20px;
    }

    .v3-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-weight: 600;
        color: #212b36;
        font-size: 16px;
        background: #fafbfc;
        border-radius: 8px 8px 0 0;
    }

    .v3-card-body {
        padding: 20px;
    }

    /* Thiết kế form hàng ngang */
    .setting-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 15px 0;
        border-bottom: 1px dashed #dfe3e8;
    }

    .setting-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .setting-info {
        flex: 1;
        padding-right: 40px;
    }

    .setting-title {
        font-weight: 600;
        color: #212b36;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .setting-desc {
        color: #637381;
        font-size: 13px;
        line-height: 1.5;
    }

    .setting-action {
        flex: 0 0 auto;
    }

    /* NÚT GẠT (TOGGLE SWITCH) CHUẨN IOS/SAPO */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
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
        transition: .3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    input:checked+.slider {
        background-color: #0088ff;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    /* Form control cơ bản */
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #0088ff;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Cấu hình kênh bán tại quầy (POS)</div>
    <button type="submit" form="posSettingsForm" class="btn-primary">💾 Lưu cấu hình</button>
</div>

<form id="posSettingsForm" action="index.php?action=save_pos_settings" method="POST">

    <div class="v3-card">
        <div class="v3-card-header">1. Cấu hình bán hàng chung</div>
        <div class="v3-card-body">

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Kiểu thanh toán</div>
                    <div class="setting-desc">Chọn thanh toán 1 bước (Nhanh) hoặc 2 bước (Chi tiết nhiều hình thức)</div>
                </div>
                <div class="setting-action" style="width: 200px;">
                    <select name="pos_payment_steps" class="form-control">
                        <option value="1" <?php echo ($settings_db['pos_payment_steps'] == '1') ? 'selected' : ''; ?>>Thanh toán 1 bước</option>
                        <option value="2" <?php echo ($settings_db['pos_payment_steps'] == '2') ? 'selected' : ''; ?>>Thanh toán 2 bước</option>
                    </select>
                </div>
            </div>

            <?php
            // Mảng chứa các Toggles ở Khối 1
            $block1_toggles = [
                'pos_allow_negative_stock' => ['Cho phép bán âm', 'Hệ thống sẽ ghi nhận tồn âm nếu bán hàng khi hết tồn kho.'],
                'pos_suggest_amount' => ['Gợi ý tiền thanh toán', 'Hệ thống gợi ý số tiền khách đưa dựa trên tổng tiền cần trả.'],
                'pos_allow_price_edit' => ['Điều chỉnh giá', 'Cho phép nhân viên thay đổi giá bán hoặc thêm khuyến mại tay.'],
                'pos_auto_promotions' => ['Áp dụng khuyến mại tự động', 'Hệ thống tự động quét và trừ tiền các chương trình KM hợp lệ.'],
                'pos_use_promo_code' => ['Sử dụng mã khuyến mại (Coupon)', 'Hiển thị ô nhập mã giảm giá trên màn hình POS.'],
                'pos_shift_management' => ['Quản lý ca làm việc', 'Theo dõi thông tin, doanh thu của từng ca làm việc.'],
                'pos_sapo_qr' => ['Kết nối hiển thị Sapo QR', 'Hiển thị mã QR VietQR để khách quét thanh toán nhanh.']
            ];

            foreach ($block1_toggles as $key => $info):
                $checked = ($settings_db[$key] == '1') ? 'checked' : '';
            ?>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-title"><?php echo $info[0]; ?></div>
                        <div class="setting-desc"><?php echo $info[1]; ?></div>
                    </div>
                    <div class="setting-action">
                        <label class="switch">
                            <input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo $checked; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div class="v3-card">
        <div class="v3-card-header">2. Máy in & Mẫu in hóa đơn</div>
        <div class="v3-card-body">

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Lựa chọn khổ in mặc định</div>
                    <div class="setting-desc">Tùy chọn khổ giấy phù hợp với máy in tại cửa hàng.</div>
                </div>
                <div class="setting-action" style="width: 200px;">
                    <select name="pos_print_size" class="form-control">
                        <option value="80mm" <?php echo ($settings_db['pos_print_size'] == '80mm') ? 'selected' : ''; ?>>Khổ 80mm (Máy in nhiệt)</option>
                        <option value="58mm" <?php echo ($settings_db['pos_print_size'] == '58mm') ? 'selected' : ''; ?>>Khổ 57/58mm (Máy in mini)</option>
                        <option value="A4" <?php echo ($settings_db['pos_print_size'] == 'A4') ? 'selected' : ''; ?>>Khổ A4 / A5 (Máy in văn phòng)</option>
                    </select>
                </div>
            </div>

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Số bản in (Liên)</div>
                    <div class="setting-desc">Số lượng hóa đơn tự động in ra mỗi khi thanh toán.</div>
                </div>
                <div class="setting-action" style="width: 100px;">
                    <input type="number" name="pos_print_copies" class="form-control" value="<?php echo $settings_db['pos_print_copies'] ?? '1'; ?>" min="1" max="5">
                </div>
            </div>

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Tự động in hóa đơn khi thanh toán</div>
                    <div class="setting-desc">Mặc định hiển thị cửa sổ in ngay khi ấn nút thanh toán thành công.</div>
                </div>
                <div class="setting-action">
                    <label class="switch">
                        <input type="checkbox" name="pos_auto_print" value="1" <?php echo ($settings_db['pos_auto_print'] == '1') ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <div class="v3-card">
        <div class="v3-card-header">3. Bán hàng Offline</div>
        <div class="v3-card-body">
            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Sử dụng chế độ bán hàng Offline</div>
                    <div class="setting-desc">Cho phép bán hàng bình thường ngay cả khi mất kết nối mạng Internet. Dữ liệu sẽ đồng bộ khi có mạng trở lại.</div>
                    <button type="button" class="btn-primary" style="background: #fff; color: #212b36; border: 1px solid #c4cdd5; margin-top: 10px; font-size: 13px; padding: 6px 12px;">🔄 Đồng bộ lại dữ liệu về máy</button>
                </div>
                <div class="setting-action">
                    <label class="switch">
                        <input type="checkbox" name="pos_offline_mode" value="1" <?php echo ($settings_db['pos_offline_mode'] == '1') ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
