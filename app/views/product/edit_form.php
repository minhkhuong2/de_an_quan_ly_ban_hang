<?php
require_once __DIR__ . '/../layout/header.php';
$product = $product ?? [];
$db = (new Database())->getConnection();
$existing_variants = (new ProductModel($db))->getVariantsByProductId($product['id'] ?? 0);
/** @var array $dynamic_branches */ // Mảng 5 chi nhánh tự động từ Controller
?>

<style>
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
        align-items: flex-start;
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
        margin-top: 2px;
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

    .variant-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
    }

    .variant-table th {
        background: #f4f6f8;
        padding: 10px;
        font-size: 13px;
        color: #212b36;
        text-align: left;
        border-bottom: 1px solid #dfe3e8;
    }

    .variant-table td {
        padding: 10px;
        border-bottom: 1px solid #f4f6f8;
        vertical-align: middle;
        font-size: 13px;
    }

    .variant-input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 13px;
        outline: none;
    }

    .variant-input:focus {
        border-color: #0088ff;
    }

    .bulk-edit-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
        background: #fff;
        padding: 10px;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .bulk-btn {
        padding: 6px 12px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        font-weight: 500;
    }

    .bulk-btn:hover {
        background: #dfe3e8;
    }
</style>

<form action="index.php?action=edit_product&id=<?php echo $product['id'] ?? ''; ?>" method="POST" enctype="multipart/form-data" id="productForm">
    <div class="sapo-header-bar">
        <h2><a href="index.php?action=product_list">←</a> Chỉnh sửa: <?php echo htmlspecialchars($product['product_name'] ?? ''); ?></h2>
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật sản phẩm thành công!</div><?php endif; ?>
    <?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e; font-weight:500;">🗑️ Đã xóa phiên bản sản phẩm thành công!</div><?php endif; ?>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">1. Thông tin chung</div>
                <div class="form-group"><label>Tên sản phẩm <span style="color:red;">*</span> 🌟</label><input type="text" id="main_product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" required></div>
                <div class="row-flex">
                    <div class="form-group"><label>Mã sản phẩm / SKU</label><input type="text" id="main_sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Barcode</label><input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode'] ?? ''); ?>"></div>
                </div>
                <div class="form-group" style="width: 48%;"><label>Đơn vị tính</label><input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>"></div>
                <div class="form-group"><label>Mô tả sản phẩm 🌟</label><textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea></div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">2. Thiết lập giá sản phẩm</div>
                <div class="row-flex">
                    <div class="form-group"><label>Giá bán</label>
                        <div style="display: flex; position: relative;"><input type="text" id="main_price" name="base_price" class="form-control currency-input" value="<?php echo number_format($product['price'] ?? ($product['base_price'] ?? 0), 0, '', '.'); ?>" style="padding-right: 30px; font-weight: bold; color: #212b36;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span></div>
                    </div>
                    <div class="form-group"><label>Giá so sánh ⓘ</label>
                        <div style="display: flex; position: relative;"><input type="text" name="compare_price" class="form-control currency-input" value="<?php echo number_format($product['compare_price'] ?? 0, 0, '', '.'); ?>" style="padding-right: 30px;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span></div>
                    </div>
                </div>
                <div class="form-group" style="width: 48%;"><label>Giá vốn ⓘ</label>
                    <div style="display: flex; position: relative;"><input type="text" id="main_cost" name="cost_price" class="form-control currency-input" value="<?php echo number_format($product['cost_price'] ?? 0, 0, '', '.'); ?>" style="padding-right: 30px; font-weight: bold; color: #cf1322;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">₫</span></div>
                </div>
                <div class="checkbox-group"><input type="checkbox" id="tax" name="apply_tax" value="1" <?php echo (isset($product['apply_tax']) && $product['apply_tax'] == 1) ? 'checked' : ''; ?>><label for="tax" style="margin:0;">Áp dụng thuế</label></div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">4. Thông tin kho hàng (Phân bổ theo chi nhánh)</div>
                <div class="checkbox-group"><input type="checkbox" checked disabled><label style="margin:0;">Quản lý số lượng tồn kho (Theo mã IMEI)</label></div>
                <div class="checkbox-group"><input type="checkbox" name="allow_negative" id="allow_negative"><label for="allow_negative" style="margin:0;">Cho phép bán âm</label></div>

                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #fafbfc; border-top: 1px solid #dfe3e8; border-bottom: 1px solid #dfe3e8;">
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36;">Kho / Chi nhánh</th>
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36; width: 120px;">Tồn kho</th>
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36;">Vị trí lưu kho (Bin Location) ⓘ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($branches_db)): foreach ($branches_db as $b): ?>
                                <tr style="border-bottom: 1px solid #f4f6f8;">
                                    <td style="padding: 10px 12px; font-weight: 500; font-size: 13px; color: #0088ff;">
                                        🏢 <?php echo htmlspecialchars($b['branch_name']); ?>
                                        <?php if ($b['is_default']): ?><span style="font-size:10px; background:#ffea8a; color:#8a6100; padding:2px 4px; border-radius:4px; margin-left:4px;">Mặc định</span><?php endif; ?>
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        <input type="number" name="branch_stock[<?php echo $b['id']; ?>]" class="form-control" value="0" style="padding: 6px;">
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        <input type="text" name="branch_location[<?php echo $b['id']; ?>]" class="form-control" placeholder="VD: A-D10-K456" style="padding: 6px;">
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="3" style="padding: 15px; text-align: center; color: #cf1322;">Vui lòng vào Cấu hình -> Quản lý chi nhánh để tạo kho trước!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">5. Thông tin vận chuyển</div>
                <div class="checkbox-group">
                    <input type="checkbox" checked name="require_shipping" id="require_shipping" onchange="document.getElementById('weight-box').style.display = this.checked ? 'block' : 'none';">
                    <label for="require_shipping" style="margin:0;">Sản phẩm yêu cầu vận chuyển</label>
                </div>

                <div id="weight-box" style="margin-top: 15px; display: block;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px;">Khối lượng</label>
                    <div style="display: flex; width: 50%;">
                        <input type="number" name="weight" class="form-control" value="0" style="border-radius: 4px 0 0 4px; border-right: none;">
                        <select name="weight_unit" class="form-control" style="width: 80px; border-radius: 0 4px 4px 0; background: #fafbfc;">
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                    <p style="font-size: 12px; color: #637381; margin-top: 5px;">Khối lượng dùng để tính phí vận chuyển của bưu tá.</p>
                </div>
            </div>

            <?php if (!empty($existing_variants)): ?>
                <div class="sapo-card">
                    <div class="sapo-card-title" style="color: #0050b3; margin-bottom: 5px;">📦 Các phiên bản hiện tại</div>
                    <table class="variant-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Tên phiên bản</th>
                                <th style="width: 20%;">Mã SKU</th>
                                <th style="width: 20%;">Giá bán (₫)</th>
                                <th style="width: 15%; text-align: center;">Tồn kho</th>
                                <th style="width: 15%; text-align: center;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($existing_variants as $v): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #0088ff;"><?php echo htmlspecialchars($v['product_name']); ?></td>
                                    <td style="color: #637381; font-weight: 500;"><?php echo htmlspecialchars($v['sku']); ?></td>
                                    <td style="font-weight: bold; color: #212b36;"><?php echo number_format($v['price'] ?? $v['base_price'] ?? 0, 0, ',', '.'); ?> ₫</td>
                                    <td style="text-align: center; font-weight: bold; color: #108043;"><?php echo $v['stock']; ?></td>
                                    <td style="text-align: center;">
                                        <a href="index.php?action=edit_product&id=<?php echo $v['id']; ?>" style="color:#ff9900; text-decoration:none; font-weight:bold; background:#fff8ea; padding:4px 8px; border-radius:4px;">✏️ Sửa</a>
                                        <a href="index.php?action=delete_product&id=<?php echo $v['id']; ?>&parent_id=<?php echo $product['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa phiên bản này?');" style="color:#cf1322; text-decoration:none; font-weight:bold; background:#fff1f0; padding:4px 8px; border-radius:4px; margin-left: 5px;">🗑️ Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="sapo-card" id="attribute-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div class="sapo-card-title" style="margin:0;">6. Bổ sung Thuộc tính / Phiên bản mới</div>
                    <a href="javascript:void(0)" class="link-blue" onclick="addAttributeRow()">+ Thêm thuộc tính mới</a>
                </div>
                <p style="font-size: 14px; color: #637381; margin: 0; margin-bottom: 15px;" id="attr-hint">Nhập màu sắc/dung lượng mới để tự động sinh phiên bản.</p>
                <table style="width: 100%; border-collapse: collapse;" id="attributeTable">
                    <tbody id="attributeBody"></tbody>
                </table>
            </div>

            <div class="sapo-card" id="variants-card" style="display: none; border: 1px solid #91d5ff; background: #e6f7ff;">
                <div class="sapo-card-title" style="color: #0050b3; margin-bottom: 5px;">🚀 Danh sách phiên bản mới</div>
                <div class="bulk-edit-toolbar">
                    <strong style="font-size: 13px; color: #212b36;">Sửa nhanh hàng loạt:</strong>
                    <input type="text" id="bulk_price" class="variant-input currency-input" placeholder="Giá bán..." style="width: 100px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_price', 'bulk_price')">Áp dụng Giá</button>
                    <input type="text" id="bulk_cost" class="variant-input currency-input" placeholder="Giá vốn..." style="width: 100px; margin-left: 10px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_cost', 'bulk_cost')">Áp dụng Vốn</button>
                    <input type="number" id="bulk_stock" class="variant-input" placeholder="Tồn kho..." style="width: 90px; margin-left: 10px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_stock', 'bulk_stock')">Áp dụng Kho</button>
                </div>
                <table class="variant-table" id="variantsTable">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Phiên bản mới</th>
                            <th style="width: 18%;">Mã SKU</th>
                            <th style="width: 18%;">Giá bán (₫)</th>
                            <th style="width: 18%;">Giá vốn (₫)</th>
                            <th style="width: 11%; text-align: center;">Tồn kho</th>
                            <th style="width: 10%; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="variantsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">3. Thêm ảnh sản phẩm</div>
                <div class="upload-box" onclick="document.getElementById('file-upload').click()">
                    <input type="file" id="file-upload" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                    <div id="upload-placeholder" style="display: <?php echo !empty($product['image']) ? 'none' : 'block'; ?>;">
                        <div style="font-size: 24px; color: #0088ff; margin-bottom: 10px;">+</div>Kéo thả hoặc tải ảnh từ thiết bị
                    </div>
                    <img id="image-preview" src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : ''; ?>" style="display: <?php echo !empty($product['image']) ? 'block' : 'none'; ?>; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 6px; object-fit: cover;">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">8. Kênh bán hàng</div>
                <div class="checkbox-group" style="align-items: flex-start;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;">Website</label><br><a href="#" class="link-blue" style="font-size: 13px;">Đặt lịch hiển thị</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;">POS</label><br><a href="#" class="link-blue" style="font-size: 13px;">Áp dụng bảng giá POS</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;">Chat OmniAI</label></div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">10. Thông tin bổ sung</div>
                <div class="form-group"><label>10.1 Danh mục ⓘ</label><select class="form-control" name="category">
                        <option value="">Chọn danh mục</option><?php if (!empty($dynamic_categories)): foreach ($dynamic_categories as $catName): ?><option value="<?php echo htmlspecialchars($catName); ?>" <?php echo (strcasecmp(($product['category'] ?? ''), $catName) == 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($catName); ?></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                    endif; ?>
                    </select></div>
                <div class="form-group"><label>10.2 Nhãn hiệu</label><input type="text" name="brand" list="brand_list" class="form-control" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>" placeholder="Gõ hoặc chọn nhãn hiệu..."><datalist id="brand_list"><?php if (!empty($dynamic_brands)): foreach ($dynamic_brands as $brandName): ?><option value="<?php echo htmlspecialchars($brandName); ?>"></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                            endif; ?></datalist></div>
                <div class="form-group"><label>10.3 Loại sản phẩm</label><input type="text" list="type_list" class="form-control" placeholder="Gõ hoặc chọn loại SP..."><datalist id="type_list"><?php if (!empty($dynamic_types)): foreach ($dynamic_types as $typeName): ?><option value="<?php echo htmlspecialchars($typeName); ?>"></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                            endif; ?></datalist></div>
                <div class="form-group">
                    <label>10.4 Nhóm ngành nghề tính thuế ⓘ</label>
                    <select class="form-control" name="tax_category">
                        <option value="">Chọn nhóm ngành nghề</option>
                        <option value="101">101 - Hoạt động bán buôn, bán lẻ hàng hóa</option>
                        <option value="201">201 - Dịch vụ lưu trú, bốc xếp, bưu chính...</option>
                        <option value="301">301 - Sản xuất, gia công, chế biến...</option>
                        <option value="401">401 - Hoạt động kinh doanh khác (thuế 5%)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('productForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
    });

    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    let attributeCount = 0;

    function addAttributeRow() {
        if (attributeCount >= 3) {
            alert("Tối đa 3 thuộc tính cho một sản phẩm.");
            return;
        }
        document.getElementById('attr-hint').style.display = 'none';
        let tbody = document.getElementById('attributeBody');
        let tr = document.createElement('tr');
        tr.className = "attr-row";
        tr.innerHTML = `
            <td style="padding: 10px 0; border-top: 1px solid #f4f6f8;">
                <div class="row-flex" style="align-items: flex-end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;"><label style="font-size: 13px;">Tên thuộc tính</label><input type="text" class="form-control attr-name-input" placeholder="VD: Màu sắc..."></div>
                    <div class="form-group" style="flex: 2; margin-bottom: 0;"><label style="font-size: 13px;">Giá trị (Nhập rồi bấm Enter hoặc phẩy)</label><input type="text" class="form-control attr-val-input" placeholder="VD: Đỏ, Xanh" onkeyup="handleAttrInput(event, this)"></div>
                    <div style="padding-bottom: 8px;"><a href="javascript:void(0)" onclick="removeAttributeRow(this)" style="color: #ff4d4f; font-size: 20px; font-weight: bold; padding: 0 10px; text-decoration: none;">×</a></div>
                </div>
            </td>`;
        tbody.appendChild(tr);
        attributeCount++;
    }

    function handleAttrInput(e, inputElem) {
        if (e.key === 'Enter' || e.key === ',') {
            if (e.key === 'Enter') inputElem.value += ', ';
            generateVariants();
        }
    }

    function removeAttributeRow(btn) {
        btn.closest('tr').remove();
        attributeCount--;
        if (attributeCount === 0) document.getElementById('attr-hint').style.display = 'block';
        generateVariants();
    }

    function cartesianProduct(arr) {
        return arr.reduce((a, b) => a.flatMap(x => b.map(y => [...x, y])), [
            []
        ]);
    }

    function generateVariants() {
        let attrRows = document.querySelectorAll('.attr-row');
        let validAttributes = [];
        attrRows.forEach(row => {
            let vals = row.querySelector('.attr-val-input').value.split(',').map(v => v.trim()).filter(v => v !== '');
            if (vals.length > 0) validAttributes.push(vals);
        });

        let variantsCard = document.getElementById('variants-card');
        let variantsBody = document.getElementById('variantsBody');
        variantsBody.innerHTML = '';
        if (validAttributes.length === 0) {
            variantsCard.style.display = 'none';
            return;
        }

        variantsCard.style.display = 'block';
        let mainPrice = document.getElementById('main_price').value || "0";
        let mainCost = document.getElementById('main_cost').value || "0";
        let mainSku = document.getElementById('main_sku').value || "SKU";

        cartesianProduct(validAttributes).forEach((combo, index) => {
            let variantName = combo.join(' - ');
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="font-weight: bold; color: #0088ff;">${variantName}<input type="hidden" name="var_name[]" value="${variantName}"></td>
                <td><input type="text" name="var_sku[]" class="variant-input" value="${mainSku}-${Math.floor(Math.random() * 1000)}"></td>
                <td><input type="text" name="var_price[]" class="variant-input var-currency var_price" value="${mainPrice}"></td>
                <td><input type="text" name="var_cost[]" class="variant-input var-currency var_cost" value="${mainCost}"></td>
                <td><input type="number" name="var_stock[]" class="variant-input var_stock" value="0" style="text-align: center;"></td>
                <td style="text-align: center;"><a href="javascript:void(0)" onclick="this.closest('tr').remove()" style="color:#cf1322; font-size:16px; text-decoration:none;" title="Xóa bản này">🗑️</a></td>`;
            variantsBody.appendChild(tr);
        });
        attachCurrencyFormat();
    }

    function applyBulk(targetClass, inputId) {
        let val = document.getElementById(inputId).value;
        if (val === "") return;
        document.querySelectorAll('.' + targetClass).forEach(input => {
            input.value = val;
        });
    }

    // CHỈNH SỬA KHO GỐC
    const originalStock = <?php echo (int)($product['stock'] ?? 0); ?>;

    function toggleStockEdit() {
        const box = document.getElementById('stock-edit-box');
        const display = document.getElementById('stock-display');
        if (box.style.display === 'none') {
            box.style.display = 'block';
            display.style.display = 'none';
        } else {
            box.style.display = 'none';
            display.style.display = 'flex';
        }
    }

    function calculateStockDiff() {
        document.getElementById('stock_adjustment').value = (parseInt(document.getElementById('new_stock').value) || 0) - originalStock;
    }

    function calculateNewStock() {
        document.getElementById('new_stock').value = originalStock + (parseInt(document.getElementById('stock_adjustment').value) || 0);
    }

    function attachCurrencyFormat() {
        document.querySelectorAll('.var-currency, .currency-input').forEach(function(input) {
            let newObj = input.cloneNode(true);
            input.parentNode.replaceChild(newObj, input);
            newObj.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value !== '') e.target.value = parseInt(value, 10).toLocaleString('vi-VN').replace(/,/g, '.');
                else e.target.value = '';
            });
        });
    }
    attachCurrencyFormat();
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
