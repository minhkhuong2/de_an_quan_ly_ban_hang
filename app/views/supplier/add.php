<?php require_once __DIR__ . '/../layout/header.php'; ?>
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

<form action="index.php?action=add_supplier" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=supplier_list" style="color:#637381; text-decoration:none; margin-right: 10px;">←</a> Thêm mới nhà cung cấp</h2>
        <div>
            <a href="index.php?action=supplier_list" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Lưu nhà cung cấp</button>
        </div>
    </div>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin chung</div>
                <div class="form-group">
                    <label>Tên nhà cung cấp <span style="color:red;">*</span></label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="Nhập tên nhà cung cấp..." required>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Mã nhà cung cấp</label><input type="text" name="supplier_code" class="form-control" placeholder="Để trống hệ thống tự tạo"></div>
                    <div class="form-group"><label>Nhóm nhà cung cấp</label><select name="supplier_group" class="form-control">
                            <option value="">Chọn nhóm nhà cung cấp</option>
                            <option value="Nhà sản xuất">Nhà sản xuất</option>
                            <option value="Đại lý">Đại lý bán buôn</option>
                        </select></div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" class="form-control" placeholder="VD: 0987654321"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" placeholder="VD: email@domain.com"></div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin địa chỉ</div>
                <div class="form-group">
                    <label>Địa chỉ cụ thể</label>
                    <input type="text" name="address" class="form-control" placeholder="Số nhà, ngõ ngách, tên đường...">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin bổ sung</div>
                <div class="row-flex">
                    <div class="form-group"><label>Số Fax</label><input type="text" name="fax" class="form-control"></div>
                    <div class="form-group"><label>Mã số thuế</label><input type="text" name="tax_code" class="form-control"></div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Website</label><input type="text" name="website" class="form-control" placeholder="https://"></div>
                    <div class="form-group"><label>Công nợ ban đầu (₫)</label><input type="number" name="debt" class="form-control" value="0"></div>
                </div>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin khác</div>
                <div class="form-group">
                    <label>Nhân viên phụ trách</label>
                    <select name="assignee" class="form-control">
                        <option value="<?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? ''); ?>">
                            <?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'Bùi Văn Khương'); ?>
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Ghi chú thêm về nhà cung cấp này..."></textarea>
                </div>
                <div class="form-group">
                    <label>Thẻ tags</label>
                    <input type="text" name="tags" class="form-control" placeholder="VD: linh-kien, pin, apple...">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Cài đặt nâng cao</div>
                <div class="form-group">
                    <label>Thiết lập thuế</label>
                    <select name="tax_setting" class="form-control">
                        <option value="Mặc định">Thuế mặc định</option>
                        <option value="VAT 8%">VAT 8%</option>
                        <option value="VAT 10%">VAT 10%</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá nhập mặc định</label>
                    <select name="default_import_price" class="form-control">
                        <option value="Giá vốn">Lấy theo Giá vốn</option>
                        <option value="Giá nhập lần cuối">Lấy theo Lần nhập cuối</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
