<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $settings */ ?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 800px;
        margin: 0 auto;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
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
        font-size: 14px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #0088ff;
        outline: none;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    /* CSS cho khối Upload Logo */
    .logo-upload-box {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }

    .logo-preview {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        border: 1px dashed #c4cdd5;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fafbfc;
        overflow: hidden;
        position: relative;
    }

    .logo-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .logo-actions button {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
    }

    .logo-actions button:hover {
        background: #f4f6f8;
    }
</style>

<div style="max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=settings" style="text-decoration:none; color:#637381;">←</a> Cấu hình thông tin cửa hàng</h2>
</div>

<form action="index.php?action=general_settings" method="POST" enctype="multipart/form-data" class="sapo-card">
    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật thông tin cửa hàng thành công!</div><?php endif; ?>

    <div class="sapo-card-title">Biểu tượng thương hiệu (Logo)</div>
    <div class="logo-upload-box">
        <div class="logo-preview" id="logo_display">
            <?php if (!empty($settings['store_logo'])): ?>
                <img src="<?php echo htmlspecialchars($settings['store_logo']); ?>" id="preview_img">
            <?php else: ?>
                <span style="color:#919eab; font-size:12px;">Chưa có Logo</span>
            <?php endif; ?>
        </div>
        <div class="logo-actions">
            <p style="font-size: 12px; color: #637381; margin: 0 0 10px 0;">Logo hiển thị trên các mẫu in hóa đơn, email.<br>Khuyên dùng ảnh hình vuông, định dạng .png hoặc .jpg</p>
            <input type="file" id="store_logo" name="store_logo" style="display:none;" accept="image/png, image/jpeg" onchange="previewLogo(event)">
            <input type="hidden" name="remove_logo" id="remove_logo" value="0">
            <button type="button" onclick="document.getElementById('store_logo').click()">📸 Đổi hình ảnh</button>
            <button type="button" onclick="deleteLogo()" style="color: #cf1322;">🗑️ Xóa</button>
        </div>
    </div>

    <div class="sapo-card-title">Thông tin cơ bản</div>
    <div class="row-flex">
        <div class="form-group">
            <label>Tên cửa hàng <span style="color:red;">*</span></label>
            <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>" required placeholder="VD: Sapo Store">
        </div>
        <div class="form-group">
            <label>Tên kinh doanh (Pháp nhân)</label>
            <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($settings['business_name'] ?? ''); ?>" placeholder="Công ty TNHH...">
        </div>
    </div>

    <div class="row-flex">
        <div class="form-group">
            <label>Điện thoại liên hệ</label>
            <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>" placeholder="Hotline cửa hàng">
        </div>
        <div class="form-group">
            <label>Quốc gia</label>
            <select name="store_country" class="form-control">
                <option value="Vietnam" <?php echo (($settings['store_country'] ?? '') == 'Vietnam') ? 'selected' : ''; ?>>Việt Nam</option>
                <option value="Other" <?php echo (($settings['store_country'] ?? '') == 'Other') ? 'selected' : ''; ?>>Khác</option>
            </select>
        </div>
    </div>

    <div class="row-flex">
        <div class="form-group">
            <label>Tỉnh/Thành phố</label>
            <select name="store_province" class="form-control">
                <option value="">-- Chọn Tỉnh/Thành phố --</option>
                <option value="Hà Nội" <?php echo (($settings['store_province'] ?? '') == 'Hà Nội') ? 'selected' : ''; ?>>Hà Nội</option>
                <option value="Hồ Chí Minh" <?php echo (($settings['store_province'] ?? '') == 'Hồ Chí Minh') ? 'selected' : ''; ?>>TP. Hồ Chí Minh</option>
                <option value="Đà Nẵng" <?php echo (($settings['store_province'] ?? '') == 'Đà Nẵng') ? 'selected' : ''; ?>>Đà Nẵng</option>
                <option value="Hải Phòng" <?php echo (($settings['store_province'] ?? '') == 'Hải Phòng') ? 'selected' : ''; ?>>Hải Phòng</option>
                <option value="Cần Thơ" <?php echo (($settings['store_province'] ?? '') == 'Cần Thơ') ? 'selected' : ''; ?>>Cần Thơ</option>
            </select>
        </div>
        <div class="form-group">
            <label>Địa chỉ chi tiết</label>
            <input type="text" name="store_address" class="form-control" value="<?php echo htmlspecialchars($settings['store_address'] ?? ''); ?>" placeholder="Số nhà, tên đường, phường xã...">
        </div>
    </div>

    <div class="sapo-card-title" style="margin-top: 20px;">Thông tin liên lạc (Email)</div>
    <div class="row-flex">
        <div class="form-group">
            <label>Email quản trị</label>
            <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>" placeholder="Nhận thông báo từ hệ thống">
            <span style="font-size: 12px; color: #637381;">Dùng để nhận các thông báo bảo mật, hết hạn gói.</span>
        </div>
        <div class="form-group">
            <label>Email gửi thông báo</label>
            <input type="email" name="notification_email" class="form-control" value="<?php echo htmlspecialchars($settings['notification_email'] ?? ''); ?>" placeholder="Gửi cho khách hàng">
            <span style="font-size: 12px; color: #637381;">Hiển thị ở trường "Người gửi" khi gửi hóa đơn cho khách.</span>
        </div>
    </div>

    <div class="sapo-card-title" style="margin-top: 30px;">Cấu hình quản lý kho</div>

    <div class="form-group" style="margin-bottom: 25px;">
        <label style="font-weight: bold; color: #212b36; font-size: 15px; margin-bottom: 10px;">Lựa chọn quản lý kho</label>
        <div style="border: 1px solid #dfe3e8; border-radius: 4px; padding: 15px;">
            <label style="display: flex; gap: 10px; cursor: pointer; margin-bottom: 15px;">
                <input type="radio" name="inventory_type" value="simple" <?php echo (($settings['inventory_type'] ?? '') == 'simple') ? 'checked' : ''; ?> style="margin-top: 3px; accent-color: #0088ff; width: 16px; height: 16px;">
                <div>
                    <strong style="color: #212b36; font-size: 14px;">Quản lý kho đơn giản</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Cập nhật kho trực tiếp, không sử dụng các nghiệp vụ phát sinh chứng từ quản lý kho như: Nhập hàng, Chuyển kho, Trả hàng nhập.</p>
                </div>
            </label>
            <div style="height: 1px; background: #dfe3e8; margin-bottom: 15px;"></div>
            <label style="display: flex; gap: 10px; cursor: pointer;">
                <input type="radio" name="inventory_type" value="full" <?php echo (($settings['inventory_type'] ?? 'full') == 'full') ? 'checked' : ''; ?> style="margin-top: 3px; accent-color: #0088ff; width: 16px; height: 16px;">
                <div>
                    <strong style="color: #212b36; font-size: 14px;">Quản lý kho đầy đủ</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Quản lý đầy đủ quy trình đặt hàng, nhập hàng, và chuyển kho giữa các chi nhánh.</p>
                </div>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label style="font-weight: bold; color: #212b36; font-size: 15px; margin-bottom: 10px;">Phương pháp tính giá vốn</label>
        <div style="border: 1px solid #dfe3e8; border-radius: 4px; padding: 15px;">
            <label style="display: flex; gap: 10px; cursor: pointer; margin-bottom: 15px;">
                <input type="radio" name="costing_method" value="fixed" <?php echo (($settings['costing_method'] ?? '') == 'fixed') ? 'checked' : ''; ?> style="margin-top: 3px; accent-color: #0088ff; width: 16px; height: 16px;">
                <div>
                    <strong style="color: #212b36; font-size: 14px;">Giá vốn cố định</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Không tính lại giá vốn khi có các giao dịch nhập kho. Chỉ thay đổi khi bạn chủ động sửa giá vốn của sản phẩm.</p>
                </div>
            </label>
            <div style="height: 1px; background: #dfe3e8; margin-bottom: 15px;"></div>
            <label style="display: flex; gap: 10px; cursor: pointer;">
                <input type="radio" name="costing_method" value="mac" <?php echo (($settings['costing_method'] ?? 'mac') == 'mac') ? 'checked' : ''; ?> style="margin-top: 3px; accent-color: #0088ff; width: 16px; height: 16px;">
                <div>
                    <strong style="color: #212b36; font-size: 14px;">Giá vốn bình quân gia quyền</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #637381;">Tự động tính lại giá vốn theo phương pháp bình quân gia quyền mỗi khi có giao dịch nhập kho mới.</p>
                </div>
            </label>
        </div>
        <p style="font-size: 12px; color: #0050b3; margin-top: 10px; background: #e6f7ff; padding: 10px; border-radius: 4px; border: 1px solid #91d5ff;">ℹ️ <b>Lưu ý:</b> Lựa chọn này sẽ áp dụng cho các đơn hàng và phiếu nhập phát sinh sau khi bạn cập nhật.</p>
    </div>

    <div style="text-align: right; margin-top: 20px; border-top: 1px solid #dfe3e8; padding-top: 20px;">
        <button type="button" class="logo-actions button" onclick="window.location.href='index.php?action=settings'" style="margin-right: 10px; padding: 10px 20px; border: 1px solid #c4cdd5; background: #fff; border-radius: 4px; cursor: pointer;">Hủy</button>
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 25px; border-radius:4px; font-weight:bold; cursor:pointer;">💾 Lưu cấu hình</button>
    </div>
</form>

<script>
    function previewLogo(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo_display').innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 100%; object-fit: contain;">';
                document.getElementById('remove_logo').value = '0';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteLogo() {
        document.getElementById('logo_display').innerHTML = '<span style="color:#919eab; font-size:12px;">Đã xóa Logo</span>';
        document.getElementById('store_logo').value = '';
        document.getElementById('remove_logo').value = '1';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
