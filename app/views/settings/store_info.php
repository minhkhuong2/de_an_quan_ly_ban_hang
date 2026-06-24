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

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: #212b36;
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

    .form-control:focus {
        border-color: #0088ff;
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

    /* CSS cho Radio chọn cấu hình kho */
    .radio-card {
        border: 1px solid #c4cdd5;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: 0.2s;
    }

    .radio-card:hover {
        border-color: #0088ff;
        background: #f4f9ff;
    }

    .radio-card input {
        margin-right: 10px;
        cursor: pointer;
    }

    .radio-card h4 {
        margin: 0 0 5px 0;
        font-size: 15px;
        color: #212b36;
    }

    .radio-card p {
        margin: 0;
        font-size: 13px;
        color: #637381;
        line-height: 1.5;
    }

    /* CSS Upload Logo */
    .logo-upload-box {
        width: 150px;
        height: 150px;
        border: 2px dashed #c4cdd5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        background: #fafbfc;
    }

    .logo-upload-box:hover {
        border-color: #0088ff;
    }

    .logo-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .upload-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.2s;
        font-size: 13px;
        font-weight: 600;
    }

    .logo-upload-box:hover .upload-overlay {
        opacity: 1;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Cấu hình chung</div>
    <button type="button" class="btn-primary" onclick="document.getElementById('frm_store_settings').submit()">💾 Lưu cấu hình</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật cấu hình cửa hàng thành công!</div>
<?php endif; ?>

<form id="frm_store_settings" action="index.php?action=update_store_info" method="POST" enctype="multipart/form-data">

    <div class="v3-card">
        <div class="card-header">📦 Cấu hình chế độ quản lý kho</div>
        <div class="card-body">
            <div class="grid-2">
                <label class="radio-card">
                    <div style="display: flex; align-items: flex-start;">
                        <input type="radio" name="inventory_mode" value="simple" <?php echo (($store['inventory_mode'] ?? '') == 'simple') ? 'checked' : ''; ?>>
                        <div>
                            <h4>Quản lý kho đơn giản</h4>
                            <p>Cập nhật tồn kho trực tiếp trên sản phẩm. Phù hợp shop nhỏ, không sử dụng các nghiệp vụ phát sinh chứng từ quản lý kho như Nhập hàng, Chuyển kho.</p>
                        </div>
                    </div>
                </label>

                <label class="radio-card" style="<?php echo (($store['inventory_mode'] ?? 'full') == 'full') ? 'border-color:#0088ff; background:#f4f9ff;' : ''; ?>">
                    <div style="display: flex; align-items: flex-start;">
                        <input type="radio" name="inventory_mode" value="full" <?php echo (($store['inventory_mode'] ?? 'full') == 'full') ? 'checked' : ''; ?>>
                        <div>
                            <h4 style="color: #0088ff;">Quản lý kho đầy đủ (ERP)</h4>
                            <p>Quản lý đầy đủ nghiệp vụ: Đặt hàng nhập, Nhập hàng, Trả hàng nhập, Chuyển kho, Quản lý nhà cung cấp. <b>(Khuyên dùng)</b></p>
                        </div>
                    </div>
                </label>
            </div>
            <p style="font-size: 13px; color: #d82c0d; margin: 0; font-style: italic;">⚠️ Lưu ý: Việc chuyển đổi từ chế độ Đầy đủ về Đơn giản có thể làm ẩn các chứng từ nhập kho chưa hoàn thành.</p>
        </div>
    </div>

    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">🏢 Thông tin cửa hàng cơ bản</div>
            <div class="card-body">
                <div class="form-group" style="display: flex; gap: 20px; align-items: flex-start;">
                    <div>
                        <label>Logo thương hiệu</label>
                        <div class="logo-upload-box" onclick="document.getElementById('logo_input').click()">
                            <?php if (!empty($store['logo'])): ?>
                                <img id="logo_preview" src="<?php echo htmlspecialchars($store['logo']); ?>" alt="Logo">
                            <?php else: ?>
                                <img id="logo_preview" src="https://via.placeholder.com/150?text=No+Logo" alt="No Logo">
                            <?php endif; ?>
                            <div class="upload-overlay"><i class="fa-solid fa-camera"></i> Thay đổi Logo</div>
                        </div>
                        <input type="file" name="logo" id="logo_input" accept="image/*" style="display:none;" onchange="previewImage(this)">
                        <div style="font-size: 12px; color: #637381; margin-top: 5px; width: 150px; text-align: center;">Khuyên dùng: .PNG, vuông</div>
                    </div>

                    <div style="flex-grow: 1;">
                        <div class="form-group">
                            <label>Tên cửa hàng <span>*</span></label>
                            <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($store['store_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tên pháp nhân kinh doanh</label>
                            <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($store['business_name'] ?? ''); ?>" placeholder="Công ty TNHH / HKD...">
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Điện thoại (Hotline) <span>*</span></label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($store['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Mã số thuế</label>
                        <input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($store['tax_code'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">📍 Địa chỉ & Liên hệ</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Địa chỉ chi tiết <span>*</span></label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo htmlspecialchars($store['address'] ?? ''); ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Quốc gia</label>
                        <select name="country" class="form-control">
                            <option value="Vietnam" <?php echo (($store['country'] ?? '') == 'Vietnam') ? 'selected' : ''; ?>>Việt Nam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tỉnh / Thành phố</label>
                        <select name="province" class="form-control">
                            <option value="Hà Nội" <?php echo (($store['province'] ?? '') == 'Hà Nội') ? 'selected' : ''; ?>>Hà Nội</option>
                            <option value="TP. Hồ Chí Minh" <?php echo (($store['province'] ?? '') == 'TP. Hồ Chí Minh') ? 'selected' : ''; ?>>TP. Hồ Chí Minh</option>
                            <option value="Đà Nẵng" <?php echo (($store['province'] ?? '') == 'Đà Nẵng') ? 'selected' : ''; ?>>Đà Nẵng</option>
                            <option value="Hải Phòng" <?php echo (($store['province'] ?? '') == 'Hải Phòng') ? 'selected' : ''; ?>>Hải Phòng</option>
                            <option value="Khác" <?php echo (($store['province'] ?? '') == 'Khác') ? 'selected' : ''; ?>>Tỉnh thành khác</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email quản trị (Nhận thông báo)</label>
                        <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($store['admin_email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email CSKH (Gửi cho khách)</label>
                        <input type="email" name="notify_email" class="form-control" value="<?php echo htmlspecialchars($store['notify_email'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // JS Hiển thị ảnh xem trước ngay khi chọn file Logo
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo_preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
