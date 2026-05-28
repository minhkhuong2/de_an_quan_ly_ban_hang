<?php
require_once __DIR__ . '/../layout/header.php';
/** @var array $dynamic_categories */
/** @var array $dynamic_brands */
/** @var array $dynamic_types */
?>

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

    .sapo-header-bar h2 a {
        text-decoration: none;
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
        box-sizing: border-box;
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

    .upload-box {
        border: 2px dashed #c4cdd5;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        color: #637381;
        cursor: pointer;
        background: #fafbfc;
        transition: 0.3s;
    }

    .upload-box:hover {
        background: #f4f6f8;
        border-color: #0088ff;
    }

    .link-blue {
        color: #0088ff;
        text-decoration: none;
        font-size: 14px;
    }
</style>

<form action="index.php?action=add_product" method="POST" enctype="multipart/form-data">

    <div class="sapo-header-bar">
        <h2><a href="index.php?action=product_list">←</a> Thêm mới sản phẩm</h2>
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Lưu sản phẩm</button>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="sapo-grid">

        <div class="sapo-col-left">

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin sản phẩm</div>
                <div class="form-group">
                    <label>Tên sản phẩm <span style="color:red;">*</span></label>
                    <input type="text" name="product_name" class="form-control" placeholder="Nhập tên sản phẩm..." required>
                </div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Mã SKU</label>
                        <input type="text" name="sku" class="form-control" placeholder="Để trống hệ thống tự tạo">
                    </div>
                    <div class="form-group">
                        <label>Mã vạch/ Barcode</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Quét mã vạch...">
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Đơn vị tính</label>
                    <input type="text" name="unit" class="form-control" value="Cái">
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
                            <input type="number" name="base_price" class="form-control" value="0">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Giá so sánh ⓘ</label>
                        <div style="display: flex; position: relative;">
                            <input type="number" name="compare_price" class="form-control" value="0">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Giá vốn ⓘ</label>
                    <div style="display: flex; position: relative;">
                        <input type="number" name="cost_price" class="form-control" value="0">
                        <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="tax" name="apply_tax" value="1">
                    <label for="tax" style="margin:0;">Áp dụng thuế</label>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin kho (Khởi tạo)</div>
                <div class="form-group">
                    <label>Lưu kho tại</label>
                    <select class="form-control" style="background-color: #fff;">
                        <option>Cửa hàng chính</option>
                    </select>
                </div>
                <div class="checkbox-group"><input type="checkbox" checked disabled><label style="margin:0;">Quản lý số lượng tồn kho (Theo mã IMEI)</label></div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Vận chuyển</div>
                <div class="checkbox-group"><input type="checkbox" checked><label style="margin:0;">Sản phẩm yêu cầu vận chuyển</label></div>
            </div>

            <!-- KHỐI THUỘC TÍNH (ĐÃ BỔ SUNG ĐẦY ĐỦ) -->
            <div class="sapo-card" id="attribute-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div class="sapo-card-title" style="margin:0;">Thuộc tính</div>
                    <a href="javascript:void(0)" class="link-blue" onclick="addAttributeRow()">+ Thêm thuộc tính</a>
                </div>
                <p style="font-size: 14px; color: #212b36; margin: 0; margin-bottom: 15px;" id="attr-hint">Sản phẩm có nhiều thuộc tính khác nhau. Ví dụ: kích thước, màu sắc.</p>

                <table style="width: 100%; border-collapse: collapse;" id="attributeTable">
                    <tbody id="attributeBody">
                        <!-- Các dòng thuộc tính sẽ được chèn vào đây bằng Javascript khi bấm nút -->
                    </tbody>
                </table>
            </div>

        </div>

        <div class="sapo-col-right">

            <div class="sapo-card">
                <div class="sapo-card-title">Ảnh sản phẩm</div>
                <div class="upload-box" onclick="document.getElementById('file-upload').click()">
                    <input type="file" id="file-upload" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">

                    <div id="upload-placeholder">
                        <div style="font-size: 24px; color: #0088ff; margin-bottom: 10px;">+</div>
                        Kéo thả hoặc <a href="javascript:void(0)" class="link-blue">thêm ảnh từ thiết bị</a><br>
                        <span style="font-size: 12px; margin-top: 5px; display: block;">(Dung lượng tối đa 2MB)</span>
                    </div>

                    <img id="image-preview" src="" style="display: none; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 6px; object-fit: cover;">
                </div>
            </div>

            <!-- CHỌN PHÂN LOẠI ĐỘNG -->
            <div class="sapo-card">
                <div class="form-group">
                    <label>Danh mục ⓘ</label>
                    <select class="form-control" name="category">
                        <option value="">Chọn danh mục</option>
                        <?php if (!empty($dynamic_categories)): foreach ($dynamic_categories as $catName): ?>
                                <option value="<?php echo htmlspecialchars($catName); ?>">
                                    <?php echo htmlspecialchars($catName); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhãn hiệu</label>
                    <input type="text" name="brand" list="brand_list" class="form-control" placeholder="Gõ hoặc chọn nhãn hiệu...">
                    <datalist id="brand_list">
                        <?php if (!empty($dynamic_brands)): foreach ($dynamic_brands as $brandName): ?>
                                <option value="<?php echo htmlspecialchars($brandName); ?>"></option>
                        <?php endforeach;
                        endif; ?>
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Loại sản phẩm</label>
                    <input type="text" list="type_list" class="form-control" placeholder="Gõ hoặc chọn loại sản phẩm...">
                    <datalist id="type_list">
                        <?php if (!empty($dynamic_types)): foreach ($dynamic_types as $typeName): ?>
                                <option value="<?php echo htmlspecialchars($typeName); ?>"></option>
                        <?php endforeach;
                        endif; ?>
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Tag</label>
                    <input type="text" name="tags" class="form-control" placeholder="VD: Khuyến mãi, Hàng mới...">
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
            <button type="submit" class="btn-save">Lưu sản phẩm</button>
        </div>
    </div>
</form>

<script>
    // JS cho tính năng upload ảnh
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // JS cho tính năng thêm Thuộc tính động
    function addAttributeRow() {
        // Ẩn dòng chữ gợi ý khi bắt đầu thêm
        document.getElementById('attr-hint').style.display = 'none';

        let tbody = document.getElementById('attributeBody');
        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 10px 0; border-top: 1px solid #f4f6f8;">
                <div class="row-flex" style="align-items: flex-end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label style="font-size: 13px; color: #637381;">Tên thuộc tính</label>
                        <input type="text" name="attr_name[]" class="form-control" placeholder="VD: Màu sắc, Dung lượng..." required>
                    </div>
                    <div class="form-group" style="flex: 2; margin-bottom: 0;">
                        <label style="font-size: 13px; color: #637381;">Giá trị</label>
                        <input type="text" name="attr_value[]" class="form-control" placeholder="VD: Đen, Trắng, Vàng (cách nhau bằng phẩy)" required>
                    </div>
                    <div style="padding-bottom: 8px;">
                        <a href="javascript:void(0)" onclick="this.closest('tr').remove()" style="color: #ff4d4f; text-decoration: none; font-size: 20px; font-weight: bold; padding: 0 10px;">×</a>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
