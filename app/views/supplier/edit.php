<?php
require_once __DIR__ . '/../layout/header.php';
$supplier = $supplier ?? [];
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
        font-weight: 500;
        cursor: pointer;
        margin-left: 10px;
    }

    .sapo-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .sapo-col-left {
        flex: 0 0 68%;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sapo-col-right {
        flex: 0 0 calc(32% - 20px);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
        border-bottom: 1px solid #f4f6f8;
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
        font-size: 14px;
        box-sizing: border-box;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }
</style>

<form action="" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=supplier_list" style="color:#637381; text-decoration:none; margin-right: 10px;">←</a> Chỉnh sửa: <?php echo htmlspecialchars($supplier['supplier_name'] ?? ''); ?></h2>
        <div>
            <a href="index.php?action=supplier_list" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin chung</div>
                <div class="form-group">
                    <label>Tên nhà cung cấp *</label>
                    <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($supplier['supplier_name'] ?? ''); ?>" required>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Mã nhà cung cấp</label><input type="text" name="supplier_code" class="form-control" value="<?php echo htmlspecialchars($supplier['supplier_code'] ?? ''); ?>"></div>
                    <div class="form-group">
                        <label>Nhóm nhà cung cấp</label>
                        <select name="supplier_group" class="form-control">
                            <option value="">Chọn nhóm nhà cung cấp</option>
                            <option value="Nhà sản xuất" <?php echo (($supplier['supplier_group'] ?? '') == 'Nhà sản xuất') ? 'selected' : ''; ?>>Nhà sản xuất</option>
                            <option value="Đại lý" <?php echo (($supplier['supplier_group'] ?? '') == 'Đại lý') ? 'selected' : ''; ?>>Đại lý bán buôn</option>
                        </select>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>"></div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin địa chỉ</div>
                <div class="form-group">
                    <label>Địa chỉ cụ thể</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($supplier['address'] ?? ''); ?>">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin bổ sung</div>
                <div class="row-flex">
                    <div class="form-group"><label>Số Fax</label><input type="text" name="fax" class="form-control" value="<?php echo htmlspecialchars($supplier['fax'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Mã số thuế</label><input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($supplier['tax_code'] ?? ''); ?>"></div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Website</label><input type="text" name="website" class="form-control" value="<?php echo htmlspecialchars($supplier['website'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Công nợ ban đầu (₫)</label><input type="number" name="debt" class="form-control" value="<?php echo htmlspecialchars($supplier['debt'] ?? 0); ?>"></div>
                </div>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin khác</div>
                <div class="form-group">
                    <label>Trạng thái giao dịch</label>
                    <select name="status" class="form-control">
                        <option value="Đang giao dịch" <?php echo (($supplier['status'] ?? 'Đang giao dịch') == 'Đang giao dịch') ? 'selected' : ''; ?>>Đang giao dịch</option>
                        <option value="Ngừng giao dịch" <?php echo (($supplier['status'] ?? '') == 'Ngừng giao dịch') ? 'selected' : ''; ?>>Ngừng giao dịch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhân viên phụ trách</label>
                    <input type="text" name="assignee" class="form-control" value="<?php echo htmlspecialchars($supplier['assignee'] ?? 'Bùi Văn Khương'); ?>" readonly style="background:#f4f6f8;">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($supplier['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Thẻ tags</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($supplier['tags'] ?? ''); ?>">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Cài đặt nâng cao</div>
                <div class="form-group">
                    <label>Thiết lập thuế</label>
                    <select name="tax_setting" class="form-control">
                        <option value="Mặc định" <?php echo (($supplier['tax_setting'] ?? '') == 'Mặc định') ? 'selected' : ''; ?>>Thuế mặc định</option>
                        <option value="VAT 8%" <?php echo (($supplier['tax_setting'] ?? '') == 'VAT 8%') ? 'selected' : ''; ?>>VAT 8%</option>
                        <option value="VAT 10%" <?php echo (($supplier['tax_setting'] ?? '') == 'VAT 10%') ? 'selected' : ''; ?>>VAT 10%</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá nhập mặc định</label>
                    <select name="default_import_price" class="form-control">
                        <option value="Giá vốn" <?php echo (($supplier['default_import_price'] ?? '') == 'Giá vốn') ? 'selected' : ''; ?>>Lấy theo Giá vốn</option>
                        <option value="Giá nhập lần cuối" <?php echo (($supplier['default_import_price'] ?? '') == 'Giá nhập lần cuối') ? 'selected' : ''; ?>>Lấy theo Lần nhập cuối</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
