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

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
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
</style>

<div class="v3-header">
    <div class="v3-title">⚙️ Tổng quan thiết lập cửa hàng</div>
    <button type="button" class="btn-primary" onclick="document.getElementById('frm_store_settings').submit()">💾 Lưu cấu hình</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật thông tin cửa hàng thành công! Thông tin này sẽ được đồng bộ lên Hóa đơn và Phiếu in.</div>
<?php endif; ?>

<form id="frm_store_settings" action="index.php?action=update_store_info" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">Thông tin cơ bản cửa hàng</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Tên cửa hàng / Thương hiệu <span>*</span></label>
                    <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($store['store_name'] ?? ''); ?>" required>
                    <p style="font-size: 12px; color: #637381; margin-top: 5px;">Tên này sẽ hiển thị trên Website bán hàng và trên biên lai hóa đơn POS.</p>
                </div>
                <div class="form-group">
                    <label>Số điện thoại liên hệ (Hotline) <span>*</span></label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($store['phone'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Địa chỉ Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($store['email'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">Thông tin pháp lý & Liên hệ</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Địa chỉ cửa hàng chính <span>*</span></label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($store['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Mã số thuế doanh nghiệp / HKD</label>
                    <input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($store['tax_code'] ?? ''); ?>" placeholder="Cung cấp để xuất hóa đơn VAT...">
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <div style="background: #fff8ea; border: 1px solid #ffea8a; padding: 15px; border-radius: 6px;">
                        <span style="font-weight:bold; color:#8a6100;">💡 Ghi chú hệ thống:</span>
                        <p style="margin:5px 0 0 0; font-size:13px; color:#8a6100;">Toàn bộ thông tin cập nhật tại đây là dữ liệu nguồn (Master Data). Các chứng từ Sổ Quỹ Kế Toán, Đơn hàng, Phiếu in nhập kho sẽ tự động lấy dữ liệu từ khu vực này.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
