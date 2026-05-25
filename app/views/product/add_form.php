<?php
require_once __DIR__ . '/../layout/header.php';
/** @var array $product */
/** @var array $dynamic_categories */
/** @var array $dynamic_brands */
/** @var array $dynamic_types */
$product = $product ?? [];
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

    .upload-box a {
        color: #0088ff;
        text-decoration: none;
    }

    .link-blue {
        color: #0088ff;
        text-decoration: none;
        font-size: 14px;
    }
</style>

<form action="" method="POST" enctype="multipart/form-data">

    <div class="sapo-header-bar">
        <h2><a href="index.php?action=product_list">←</a> Chỉnh sửa: <?php echo htmlspecialchars($product['product_name'] ?? ''); ?></h2>
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật sản phẩm thành công!</div><?php endif; ?>
    <?php if (!empty($message)) echo $message; ?>

    <div class="sapo-grid">

        <div class="sapo-col-left">

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin sản phẩm</div>
                <div class="form-group">
                    <label>Tên sản phẩm <span style="color:red;">*</span></label>
                    <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" required>
                </div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Mã SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Mã vạch/ Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Đơn vị tính</label>
                    <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin giá</div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Giá bán</label>
                        <div style="display: flex; position: relative;">
                            <input type="number" name="base_price" class="form-control" value="<?php echo htmlspecialchars($product['base_price'] ?? 0); ?>">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Giá so sánh ⓘ</label>
                        <div style="display: flex; position: relative;">
                            <input type="number" name="compare_price" class="form-control" value="<?php echo htmlspecialchars($product['compare_price'] ?? 0); ?>">
                            <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Giá vốn ⓘ</label>
                    <div style="display: flex; position: relative;">
                        <input type="number" name="cost_price" class="form-control" value="<?php echo htmlspecialchars($product['cost_price'] ?? 0); ?>">
                        <span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="tax" name="apply_tax" value="1" <?php echo (isset($product['apply_tax']) && $product['apply_tax'] == 1) ? 'checked' : ''; ?>>
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
                <div class="checkbox-group"><input type="checkbox" checked disabled><label style="margin:0;">Quản lý số lượng tồn kho (Theo mã IMEI)</label></div>
                <div class="checkbox-group"><input type="checkbox"><label style="margin:0;">Cho phép bán âm</label></div>
                <div style="border-top: 1px solid #f4f6f8; margin: 15px 0;"></div>
                <div class="checkbox-group"><input type="checkbox"><label style="margin:0;">Quản lý sản phẩm theo lô - HSD</label></div>

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
                                <strong>Cửa hàng chính</strong><br><a href="#" class="link-blue">Vị trí lưu kho</a>
                            </td>
                            <td style="padding: 15px 12px;"><input type="number" class="form-control" value="<?php echo htmlspecialchars($product['stock'] ?? 0); ?>" readonly style="background-color: #f4f6f8; color: #212b36;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Vận chuyển</div>
                <div class="checkbox-group"><input type="checkbox" checked><label style="margin:0;">Sản phẩm yêu cầu vận chuyển</label></div>
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

        </div>

        <div class="sapo-col-right">

            <div class="sapo-card">
                <div class="sapo-card-title">Ảnh sản phẩm</div>
                <div class="upload-box" onclick="document.getElementById('file-upload').click()">
                    <input type="file" id="file-upload" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">

                    <div id="upload-placeholder" style="display: <?php echo !empty($product['image']) ? 'none' : 'block'; ?>;">
                        <div style="font-size: 24px; color: #0088ff; margin-bottom: 10px;">+</div>
                        Kéo thả hoặc <a href="javascript:void(0)" class="link-blue">thêm ảnh từ thiết bị</a><br>
                        <span style="font-size: 12px; margin-top: 5px; display: block;">(Dung lượng tối đa 2MB)</span>
                    </div>

                    <img id="image-preview" src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : ''; ?>" style="display: <?php echo !empty($product['image']) ? 'block' : 'none'; ?>; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 6px; object-fit: cover;">
                </div>
            </div>

            <div class="sapo-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div class="sapo-card-title" style="margin:0;">Kênh bán hàng</div>
                    <a href="#" class="link-blue">Bỏ chọn tất cả</a>
                </div>
                <div class="checkbox-group" style="align-items: flex-start;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div><label style="margin:0; font-weight: 500;">Chat OmniAI</label><br><a href="#" class="link-blue" style="font-size: 13px;">Áp dụng bảng giá</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div><label style="margin:0; font-weight: 500;">Website</label><br><a href="#" class="link-blue" style="font-size: 13px;">Đặt lịch hiển thị</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;">
                    <input type="checkbox" checked style="margin-top: 3px;">
                    <div><label style="margin:0; font-weight: 500;">POS</label><br><a href="#" class="link-blue" style="font-size: 13px;">Áp dụng bảng giá POS</a></div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="form-group">
                    <label>Danh mục ⓘ</label>
                    <select class="form-control" name="category">
                        <option value="">Chọn danh mục</option>
                        <?php if (!empty($dynamic_categories)): foreach ($dynamic_categories as $catName): ?>
                                <option value="<?php echo htmlspecialchars($catName); ?>" <?php echo (strcasecmp(($product['category'] ?? ''), $catName) == 0) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($catName); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhãn hiệu</label>
                    <input type="text" name="brand" list="brand_list" class="form-control" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>" placeholder="Gõ hoặc chọn nhãn hiệu...">
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
                    <div style="display: flex; justify-content: space-between;">
                        <label>Tag</label><a href="#" class="link-blue" style="font-size: 13px;">Danh sách tag</a>
                    </div>
                    <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($product['tags'] ?? ''); ?>">
                </div>
            </div>
            <div class="sapo-card">
                <div class="sapo-card-title">Khung giao diện</div><select class="form-control">
                    <option value="product">product</option>
                </select>
            </div>
        </div>
    </div>

    <div class="sapo-header-bar" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dfe3e8; justify-content: flex-end;">
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>
</form>

<script>
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
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
