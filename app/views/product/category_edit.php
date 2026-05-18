<?php
require_once __DIR__ . '/../layout/header.php';
$category = $category ?? [];
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
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }

    .sapo-grid {
        display: flex;
        gap: 20px;
    }

    .sapo-col-left {
        flex: 0 0 68%;
    }

    .sapo-col-right {
        flex: 1;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        margin-top: 5px;
        margin-bottom: 15px;
    }

    .radio-box {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .rule-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }
</style>

<form action="" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_category" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> <?php echo htmlspecialchars($category['category_name'] ?? ''); ?></h2>
        <div><a href="index.php?action=product_category" class="btn-cancel">Hủy</a> <button type="submit" class="btn-save">Lưu thay đổi</button></div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Tạo danh mục thành công!</div><?php endif; ?>
    <?php if (isset($_GET['updated'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật danh mục thành công!</div><?php endif; ?>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Thông配置 tin chung</h3>
                <label>Tên danh mục *</label> <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['category_name'] ?? ''); ?>" required>
                <label>Mô tả</label> <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
            </div>

            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Chọn sản phẩm</h3>
                <label class="radio-box">
                    <input type="radio" name="selection_type" value="manual" onchange="toggleAutoRules()" <?php echo (($category['selection_type'] ?? 'manual') == 'manual') ? 'checked' : ''; ?>> <strong>Thêm sản phẩm thủ công</strong>
                </label>
                <label class="radio-box" style="margin-top:15px;">
                    <input type="radio" name="selection_type" value="auto" onchange="toggleAutoRules()" <?php echo (($category['selection_type'] ?? '') == 'auto') ? 'checked' : ''; ?>> <strong>Thêm sản phẩm tự động</strong>
                </label>

                <div id="auto-rules-container" style="display: <?php echo (($category['selection_type'] ?? '') == 'auto') ? 'block' : 'none'; ?>; background: #fafbfc; border: 1px solid #dfe3e8; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                        <span>Thỏa mãn:</span>
                        <label><input type="radio" name="match_type" value="all" <?php echo (($category['match_type'] ?? 'all') == 'all') ? 'checked' : ''; ?>> Tất cả điều kiện</label>
                        <label><input type="radio" name="match_type" value="any" <?php echo (($category['match_type'] ?? '') == 'any') ? 'checked' : ''; ?>> Một trong các điều kiện</label>
                    </div>

                    <div id="rules-list">
                        <?php
                        $rules_json = $category['auto_rules'] ?? '[]';
                        $rules = json_decode($rules_json, true);
                        if (!empty($rules)): foreach ($rules as $r):
                        ?>
                                <div class="rule-row">
                                    <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                        <option value="Tên sản phẩm" <?php echo (($r['field'] ?? '') == 'Tên sản phẩm') ? 'selected' : ''; ?>>Tên sản phẩm</option>
                                        <option value="Loại sản phẩm" <?php echo (($r['field'] ?? '') == 'Loại sản phẩm') ? 'selected' : ''; ?>>Loại sản phẩm</option>
                                        <option value="Nhà sản xuất" <?php echo (($r['field'] ?? '') == 'Nhà sản xuất') ? 'selected' : ''; ?>>Nhà sản xuất</option>
                                        <option value="Giá sản phẩm" <?php echo (($r['field'] ?? '') == 'Giá sản phẩm') ? 'selected' : ''; ?>>Giá sản phẩm</option>
                                        <option value="Tag sản phẩm" <?php echo (($r['field'] ?? '') == 'Tag sản phẩm') ? 'selected' : ''; ?>>Tag sản phẩm</option>
                                    </select>
                                    <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                        <option value="bằng" <?php echo (($r['operator'] ?? '') == 'bằng') ? 'selected' : ''; ?>>bằng</option>
                                        <option value="bắt đầu với" <?php echo (($r['operator'] ?? '') == 'bắt đầu với') ? 'selected' : ''; ?>>bắt đầu với</option>
                                        <option value="kết thúc với" <?php echo (($r['operator'] ?? '') == 'kết thúc với') ? 'selected' : ''; ?>>kết thúc với</option>
                                        <option value="chứa từ" <?php echo (($r['operator'] ?? '') == 'chứa từ') ? 'selected' : ''; ?>>chứa từ</option>
                                        <option value="lớn hơn" <?php echo (($r['operator'] ?? '') == 'lớn hơn') ? 'selected' : ''; ?>>lớn hơn</option>
                                        <option value="nhỏ hơn" <?php echo (($r['operator'] ?? '') == 'nhỏ hơn') ? 'selected' : ''; ?>>nhỏ hơn</option>
                                    </select>
                                    <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" value="<?php echo htmlspecialchars($r['value'] ?? ''); ?>">
                                    <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">🗑️</button>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="rule-row">
                                <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                    <option value="Tên sản phẩm">Tên sản phẩm</option>
                                    <option value="Loại sản phẩm">Loại sản phẩm</option>
                                </select>
                                <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                    <option value="bằng">bằng</option>
                                    <option value="chứa từ">chứa từ</option>
                                </select>
                                <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;">
                                <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">🗑️</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" onclick="addRule()" style="background:#fff; border:1px solid #0088ff; color:#0088ff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-top:10px;">+ Thêm điều kiện</button>
                </div>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Trạng thái</h3>
                <div class="radio-box"><input type="radio" name="status" value="Hiển thị" <?php echo (($category['status'] ?? 'Hiển thị') == 'Hiển thị') ? 'checked' : ''; ?>> Hiển thị</div>
                <div class="radio-box"><input type="radio" name="status" value="Ẩn" <?php echo (($category['status'] ?? '') == 'Ẩn') ? 'checked' : ''; ?>> Ẩn</div>
            </div>
            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Sắp xếp sản phẩm</h3>
                <select name="sort_order" class="form-control">
                    <option value="newest" <?php echo (($category['sort_order'] ?? 'newest') == 'newest') ? 'selected' : ''; ?>>Theo ngày tạo: Từ mới đến cũ</option>
                    <option value="price_asc" <?php echo (($category['sort_order'] ?? '') == 'price_asc') ? 'selected' : ''; ?>>Theo giá: Từ thấp đến cao</option>
                    <option value="name_asc" <?php echo (($category['sort_order'] ?? '') == 'name_asc') ? 'selected' : ''; ?>>Theo tên: A-Z</option>
                </select>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleAutoRules() {
        document.getElementById('auto-rules-container').style.display = (document.querySelector('input[name="selection_type"]:checked').value === 'auto') ? 'block' : 'none';
    }

    function addRule() {
        var div = document.createElement('div');
        div.className = 'rule-row';
        div.innerHTML = `<select name="rule_field[]" class="form-control" style="margin:0; flex:1;"><option value="Tên sản phẩm">Tên sản phẩm</option><option value="Loại sản phẩm">Loại sản phẩm</option><option value="Nhà sản xuất">Nhà sản xuất</option><option value="Giá sản phẩm">Giá sản phẩm</option><option value="Tag sản phẩm">Tag sản phẩm</option></select> <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;"><option value="bằng">bằng</option><option value="bắt đầu với">bắt đầu với</option><option value="kết thúc với">kết thúc với</option><option value="chứa từ">chứa từ</option><option value="lớn hơn">lớn hơn</option><option value="nhỏ hơn">nhỏ hơn</option></select> <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nhập giá trị..."> <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">🗑️</button>`;
        document.getElementById('rules-list').appendChild(div);
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
