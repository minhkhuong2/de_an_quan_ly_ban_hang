<?php 
/** 
 * @var array $integrated_methods
 * @var array $manual_methods 
 */
require_once __DIR__ . '/../layout/header.php'; ?>

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

    .method-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px dashed #dfe3e8;
    }

    .method-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .method-icon {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        background: #f4f6f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border: 1px solid #dfe3e8;
    }

    .method-name {
        font-weight: 600;
        color: #212b36;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .method-desc {
        font-size: 13px;
        color: #637381;
        max-width: 400px;
        line-height: 1.4;
    }

    .method-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-setup {
        background: #fff;
        color: #0088ff;
        border: 1px solid #0088ff;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-connect {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    /* Nút Ngừng Kết nối Mới */
    .btn-disconnect {
        background: #fff;
        color: #d82c0d;
        border: 1px solid #fca5a5;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-disconnect:hover {
        background: #fff1f0;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
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
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    input:checked+.slider {
        background-color: #108043;
    }

    input:checked+.slider:before {
        transform: translateX(18px);
    }

    .modal-overlay {
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
        width: 600px;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 90vh;
        overflow-y: auto;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 5px;
        color: #212b36;
    }

    .form-group label span {
        color: red;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        outline: none;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        border-top: 1px solid #dfe3e8;
        padding-top: 15px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Cấu hình Phương thức thanh toán</div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 'config'): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Lưu thông tin tài khoản kết nối thành công!</div>
<?php elseif (isset($_GET['success']) && $_GET['success'] == 'disconnect'): ?>
    <div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">❌ Đã ngừng kết nối và xóa dữ liệu cổng thanh toán!</div>
<?php endif; ?>

<div class="v3-card">
    <div class="v3-card-header">1. Phương thức thanh toán tích hợp</div>
    <div>
        <?php foreach ($integrated_methods as $m): ?>
            <?php $has_config = !empty($m['config_data']); ?>
            <div class="method-row">
                <div class="method-info">
                    <div class="method-icon"><?php echo $m['code'] == 'vietqr' ? '🏦' : ($m['code'] == 'zalopay' ? '📱' : '💳'); ?></div>
                    <div>
                        <div class="method-name"><?php echo htmlspecialchars($m['name']); ?></div>
                        <div class="method-desc">
                            <?php echo $m['code'] == 'vietqr' ? 'Kết nối tài khoản MBBank để tạo mã QR động.' : 'Kết nối cổng ZaloPay để nhận thanh toán tự động.'; ?>
                        </div>
                    </div>
                </div>
                <div class="method-actions">
                    <?php if ($m['code'] == 'zalopay'): ?>
                        <?php if ($has_config): ?>
                            <button class="btn-setup" onclick='openZaloModal(<?php echo $m["id"]; ?>, <?php echo $m["config_data"]; ?>)'>⚙️ Cấu hình</button>
                            <button class="btn-disconnect" onclick="if(confirm('Bạn có chắc chắn muốn ngừng kết nối ZaloPay?')) window.location.href='index.php?action=disconnect_payment_method&id=<?php echo $m['id']; ?>'">Ngừng kết nối</button>
                            <label class="switch"><input type="checkbox" onchange="window.location.href='index.php?action=toggle_payment_method&id=<?php echo $m['id']; ?>&status=' + (this.checked ? 1 : 0)" <?php echo $m['is_active'] ? 'checked' : ''; ?>><span class="slider"></span></label>
                        <?php else: ?>
                            <button class="btn-connect" onclick='openZaloModal(<?php echo $m["id"]; ?>, null)'>🔗 Kết nối</button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($m['code'] == 'vietqr'): ?>
                        <?php if ($has_config): ?>
                            <button class="btn-setup" onclick='openMBModal(<?php echo $m["id"]; ?>, <?php echo $m["config_data"]; ?>)'>⚙️ Cấu hình MBBank</button>
                            <button class="btn-disconnect" onclick="if(confirm('Cảnh báo: Tính năng tạo mã QR sẽ không khả dụng. Ngừng kết nối?')) window.location.href='index.php?action=disconnect_payment_method&id=<?php echo $m['id']; ?>'">Ngừng kết nối</button>
                            <label class="switch"><input type="checkbox" onchange="window.location.href='index.php?action=toggle_payment_method&id=<?php echo $m['id']; ?>&status=' + (this.checked ? 1 : 0)" <?php echo $m['is_active'] ? 'checked' : ''; ?>><span class="slider"></span></label>
                        <?php else: ?>
                            <button class="btn-connect" onclick='openMBModal(<?php echo $m["id"]; ?>, null)'>🔗 Kết nối</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="v3-card">
    <div class="v3-card-header">2. Phương thức thanh toán thủ công</div>
    <div>
        <?php foreach ($manual_methods as $m): ?>
            <div class="method-row">
                <div class="method-info">
                    <div class="method-icon"><?php echo $m['code'] == 'cash' ? '💵' : '💳'; ?></div>
                    <div>
                        <div class="method-name"><?php echo htmlspecialchars($m['name']); ?></div>
                        <div class="method-desc">Người bán tự xác nhận thu tiền khách đưa.</div>
                    </div>
                </div>
                <div class="method-actions">
                    <label class="switch"><input type="checkbox" <?php echo $m['code'] == 'cash' ? 'disabled checked' : ''; ?> onchange="window.location.href='index.php?action=toggle_payment_method&id=<?php echo $m['id']; ?>&status=' + (this.checked ? 1 : 0)" <?php echo $m['is_active'] ? 'checked' : ''; ?>><span class="slider"></span></label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal-overlay" id="zalopay_modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 20px; color: #0088ff; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px;">Kết nối ZaloPay</h3>
        <form id="zalopay_form" action="index.php?action=save_payment_config" method="POST">
            <input type="hidden" name="method_id" id="zalo_method_id">
            <input type="hidden" name="method_code" value="zalopay">

            <h4 style="font-size: 15px; margin-bottom: 10px;">Thông tin doanh nghiệp</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group"><label>Hình thức KD <span>*</span></label><select name="business_type" class="form-control">
                        <option value="Cá nhân">Cá nhân</option>
                        <option value="Doanh nghiệp">Doanh nghiệp</option>
                    </select></div>
                <div class="form-group"><label>Số ĐKKD/CMND <span>*</span></label><input type="text" name="business_id" class="form-control" required></div>
                <div class="form-group" style="grid-column: span 2;"><label>Tên chủ hộ/doanh nghiệp <span>*</span></label><input type="text" name="business_name" class="form-control" required></div>
                <div class="form-group"><label>SĐT Zalo <span>*</span></label><input type="text" name="phone" class="form-control" required></div>
                <div class="form-group"><label>Email <span>*</span></label><input type="email" name="email" class="form-control" required></div>
            </div>

            <h4 style="font-size: 15px; margin-top: 15px; margin-bottom: 10px;">Thông tin kết nối (App Tích hợp)</h4>
            <div class="form-group"><label>App ID <span>*</span></label><input type="text" name="app_id" class="form-control" required></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group"><label>Key 1 <span>*</span></label><input type="text" name="key1" class="form-control" required></div>
                <div class="form-group"><label>Key 2 <span>*</span></label><input type="text" name="key2" class="form-control" required></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-outline" onclick="document.getElementById('zalopay_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-connect">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="mbbank_modal">
    <div class="modal-content" id="mbbank_step_1">
        <h3 style="margin-bottom: 20px; color: #108043; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px;">Cấu hình MBBank VietQR</h3>

        <form id="mbbank_form" action="index.php?action=save_payment_config" method="POST">
            <input type="hidden" name="method_id" id="mb_method_id">
            <input type="hidden" name="method_code" value="vietqr">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group" style="grid-column: span 2;"><label>Họ và tên <span>*</span></label><input type="text" name="fullname" id="mb_fullname" class="form-control" style="text-transform: uppercase;" required></div>
                <div class="form-group"><label>Số CMND/CCCD <span>*</span></label><input type="text" name="id_card" id="mb_idcard" class="form-control" required></div>
                <div class="form-group"><label>Số điện thoại <span>*</span></label><input type="text" name="phone" id="mb_phone" class="form-control" required></div>
                <div class="form-group"><label>Email <span>*</span></label><input type="email" name="email" id="mb_email" class="form-control" required></div>
                <div class="form-group"><label>Số Tài khoản <span>*</span></label><input type="text" name="account_no" id="mb_accno" class="form-control" style="font-weight: bold; color: #0088ff;" required></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-outline" onclick="document.getElementById('mbbank_modal').style.display='none'">Hủy</button>
                <button type="button" class="btn-connect" onclick="showOTPStep()" style="background: #108043;">Tiếp tục (Lưu)</button>
            </div>
        </form>
    </div>

    <div class="modal-content" id="mbbank_step_2" style="display: none; text-align: center; width: 400px;">
        <h3 style="color: #212b36; margin-bottom: 10px;">Xác thực số điện thoại</h3>
        <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Nhập mã OTP gửi về số <b id="display_phone"></b></p>
        <input type="text" class="form-control" placeholder="123456" style="text-align: center; font-size: 24px; letter-spacing: 5px; font-weight: bold; margin-bottom: 20px;" maxlength="6" id="otp_input">
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-outline" style="flex: 1;" onclick="document.getElementById('mbbank_step_2').style.display='none'; document.getElementById('mbbank_step_1').style.display='block';">Quay lại</button>
            <button type="button" class="btn-connect" style="flex: 1; background: #108043;" onclick="document.getElementById('mbbank_form').submit();">Xác nhận</button>
        </div>
    </div>
</div>

<script>
    // Hàm mở form ZaloPay và Đổ dữ liệu cũ vào (nếu có)
    function openZaloModal(id, configData) {
        document.getElementById('zalo_method_id').value = id;

        if (configData) {
            document.querySelector('#zalopay_form [name="business_type"]').value = configData.business_type || '';
            document.querySelector('#zalopay_form [name="business_id"]').value = configData.business_id || '';
            document.querySelector('#zalopay_form [name="business_name"]').value = configData.business_name || '';
            document.querySelector('#zalopay_form [name="phone"]').value = configData.phone || '';
            document.querySelector('#zalopay_form [name="email"]').value = configData.email || '';
            document.querySelector('#zalopay_form [name="app_id"]').value = configData.app_id || '';
            document.querySelector('#zalopay_form [name="key1"]').value = configData.key1 || '';
            document.querySelector('#zalopay_form [name="key2"]').value = configData.key2 || '';
        } else {
            document.getElementById('zalopay_form').reset();
        }
        document.getElementById('zalopay_modal').style.display = 'flex';
    }

    // Hàm mở form MBBank và Đổ dữ liệu cũ vào (nếu có)
    function openMBModal(id, configData) {
        document.getElementById('mb_method_id').value = id;

        if (configData) {
            document.getElementById('mb_fullname').value = configData.fullname || '';
            document.getElementById('mb_idcard').value = configData.id_card || '';
            document.getElementById('mb_phone').value = configData.phone || '';
            document.getElementById('mb_email').value = configData.email || '';
            document.getElementById('mb_accno').value = configData.account_no || '';
        } else {
            document.getElementById('mbbank_form').reset();
        }

        document.getElementById('mbbank_step_1').style.display = 'block';
        document.getElementById('mbbank_step_2').style.display = 'none';
        document.getElementById('mbbank_modal').style.display = 'flex';
    }

    function showOTPStep() {
        if (!document.getElementById('mbbank_form').checkValidity()) {
            document.getElementById('mbbank_form').reportValidity();
            return;
        }
        document.getElementById('display_phone').innerText = document.getElementById('mb_phone').value;
        document.getElementById('mbbank_step_1').style.display = 'none';
        document.getElementById('mbbank_step_2').style.display = 'block';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
