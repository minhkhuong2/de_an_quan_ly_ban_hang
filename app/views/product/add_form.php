<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    /* CSS CHUẨN FORM SAPO */
    .sapo-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .sapo-header-bar h2 {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sapo-header-bar h2 span {
        cursor: pointer;
        color: #637381;
        font-size: 18px;
    }

    .sapo-btn-group button {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px;
    }

    .btn-cancel {
        background: #fff;
        border-color: #c4cdd5 !important;
        color: #212b36;
        margin-right: 10px;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
    }

    .btn-save:hover {
        background: #0070d2;
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
        transition: all 0.2s;
        font-size: 14px;
        color: #212b36;
    }

    .form-control:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    /* Custom Checkbox Sapo Style */
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
        color: #212b36;
    }

    .checkbox-group input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
    }

    /* Vùng tải ảnh */
    .upload-box {
        border: 2px dashed #c4cdd5;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        color: #637381;
        cursor: pointer;
        background: #fafbfc;
    }

    .upload-box:hover {
        background: #f4f6f8;
        border-color: #0088ff;
    }

    .upload-box a {
        color: #0088ff;
        text-decoration: none;
    }

    .link-blue {
        color: #0088ff;
        text-decoration: none;
        font-size: 14px;
    }

    .link-blue:hover {
        text-decoration: underline;
    }
</style>

<form action="index.php?action=add_product" method="POST">

    <div class="sapo-header-bar">
        <h2>
            <span onclick="window.location.href='index.php?action=product_list'">←</span>
            Thêm sản phẩm
        </h2>
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Thêm sản phẩm</button>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="sapo-grid">

        <div class="sapo-col-left">

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin sản phẩm</div>
                <div class="form-group">
                    <label>Tên sản phẩm <span style="color:red;">*</span></label>
                    <input type="text" name="product_name" class="form-control" placeholder="Nhập tên sản phẩm (tối đa 320 ký tự)" required>
                </div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Mã SKU</label>
                        <input type="text" name="sku" class="form-control" placeholder="Nhập mã SKU (tối đa 50 ký tự)">
                    </div>
                    <div class="form-group">
                        <label>Mã vạch/ Barcode</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Nhập mã vạch/ Barcode (tối đa 50 ký tự)">
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Đơn vị tính</label>
                    <input type="text" name="unit" class="form-control" placeholder="Nhập đơn vị tính">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea class="form-control" name="description" rows="5" placeholder="Nhập mô tả sản phẩm..."></textarea>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin giá</div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Giá bán</label>
                        <div style="display: flex; position: relative;">
                            <input type="number" name="base_price" class="form-control" placeholder="Nhập giá bán sản phẩm">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Giá so sánh ⓘ</label>
                        <div style="display: flex; position: relative;">
                            <input type="number" name="compare_price" class="form-control" placeholder="Nhập giá so sánh sản phẩm">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Giá vốn ⓘ</label>
                    <div style="display: flex; position: relative;">
                        <input type="number" name="cost_price" class="form-control" placeholder="Nhập giá vốn sản phẩm">
                        <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="tax" name="apply_tax">
                    <label for="tax" style="margin:0;">Áp dụng thuế</label>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin kho</div>

                <div class="form-group">
                    <label>Lưu kho tại</label>
                    <select class="form-control" style="background-color: #fff;">
                        <option>Cửa hàng chính</option>
                    </select>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" checked disabled>
                    <label style="margin:0;">Quản lý số lượng tồn kho (Theo mã IMEI)</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox">
                    <label style="margin:0;">Cho phép bán âm</label>
                </div>

                <div style="border-top: 1px solid #f4f6f8; margin: 15px 0;"></div>

                <div class="checkbox-group">
                    <input type="checkbox">
                    <label style="margin:0;">Quản lý sản phẩm theo lô - HSD</label>
                </div>

                <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #fafbfc; border-top: 1px solid #dfe3e8; border-bottom: 1px solid #dfe3e8;">
                            <th style="padding: 12px; text-align: left; font-weight: 500; font-size: 14px; color: #212b36;">Kho lưu trữ</th>
                            <th style="padding: 12px; text-align: left; font-weight: 500; font-size: 14px; color: #212b36; width: 150px;">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 15px 12px; font-size: 14px; color: #212b36;">
                                <strong>Cửa hàng chính</strong><br>
                                <a href="#" class="link-blue">Vị trí lưu kho</a>
                            </td>
                            <td style="padding: 15px 12px;">
                                <input type="number" class="form-control" value="0" readonly style="background-color: #f4f6f8; color: #212b36;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Vận chuyển</div>
                <div class="checkbox-group">
                    <input type="checkbox" checked>
                    <label style="margin:0;">Sản phẩm yêu cầu vận chuyển</label>
                </div>
                <div class="form-group" style="width: 48%; margin-top: 15px;">
                    <label>Khối lượng</label>
                    <div style="display: flex;">
                        <input type="number" class="form-control" value="0" style="border-radius: 4px 0 0 4px; border-right: none;">
                        <select class="form-control" style="width: 70px; border-radius: 0 4px 4px 0; background: #fafbfc;">
                            <option>g</option>
                            <option>kg</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="sapo-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div class="sapo-card-title" style="margin:0;">Thuộc tính</div>
                    <a href="#" class="link-blue">Thêm thuộc tính</a>
                </div>
                <p style="font-size: 14px; color: #212b36; margin: 0;">Sản phẩm có nhiều thuộc tính khác nhau. Ví dụ: kích thước, màu sắc.</p>
            </div>

            <div class="sapo-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div class="sapo-card-title" style="margin:0;">Tối ưu SEO</div>
                    <a href="#" class="link-blue">Tùy chỉnh SEO</a>
                </div>
                <p style="font-size: 14px; color: #212b36; margin: 0;">Xin hãy nhập Tiêu đề và Mô tả để xem trước kết quả tìm kiếm của sản phẩm này.</p>
            </div>

        </div>

        <div class="sapo-col-right">

            <div class="sapo-card">
                <div class="sapo-card-title">Ảnh sản phẩm</div>
                <div class="upload-box">
                    <div style="font-size: 24px; margin-bottom: 10px;">+</div>
                    Kéo thả hoặc <a href="#" class="link-blue">thêm ảnh từ URL</a><br>
                    <span style="font-size: 12px; margin-top: 5px; display: block;">Tải ảnh lên từ thiết bị (Dung lượng ảnh tối đa 2MB)</span>
                </div>
            </div>

            <div class="sapo-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div class="sapo-card-title" style="margin:0;">Kênh bán hàng</div>
                    <a href="#" class="link-blue">Bỏ chọn tất cả</a>
                </div>
                <div class="checkbox-group" style="align-items: flex-start;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div>
                        <label style="margin:0; font-weight: 500;">Chat OmniAI</label><br>
                        <a href="#" class="link-blue" style="font-size: 13px;">Áp dụng bảng giá Chat OmniAI</a>
                    </div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div>
                        <label style="margin:0; font-weight: 500;">Website</label><br>
                        <a href="#" class="link-blue" style="font-size: 13px;">Đặt lịch hiển thị</a>
                    </div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div>
                        <label style="margin:0; font-weight: 500;">POS</label><br>
                        <a href="#" class="link-blue" style="font-size: 13px;">Áp dụng bảng giá POS</a>
                    </div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="form-group">
                    <label>Danh mục ⓘ</label>
                    <select class="form-control" name="category">
                        <option value="">Chọn danh mục</option>
                        <option value="Điện thoại">Điện thoại di động</option>
                        <option value="Máy tính bảng">Máy tính bảng</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhãn hiệu</label>
                    <select class="form-control" name="brand">
                        <option value="">Chọn nhãn hiệu</option>
                        <option value="Apple">Apple</option>
                        <option value="Samsung">Samsung</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Loại sản phẩm</label>
                    <select class="form-control">
                        <option value="">Chọn loại sản phẩm</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhóm ngành nghề tính thuế GTGT, TNCN</label>
                    <select class="form-control">
                        <option value="">Chọn nhóm ngành nghề</option>
                    </select>
                </div>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between;">
                        <label>Tag</label>
                        <a href="#" class="link-blue" style="font-size: 13px;">Danh sách tag</a>
                    </div>
                    <input type="text" name="tags" class="form-control" placeholder="Tìm kiếm hoặc thêm mới">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Khung giao diện</div>
                <select class="form-control">
                    <option value="product">product</option>
                </select>
            </div>

        </div>
    </div>

    <div class="sapo-header-bar" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dfe3e8; justify-content: flex-end;">
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Thêm sản phẩm</button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
