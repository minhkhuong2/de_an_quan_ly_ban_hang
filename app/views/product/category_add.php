<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .akc-header-bar {
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

    .akc-grid {
        display: flex;
        gap: 20px;
    }

    .akc-col-left {
        flex: 0 0 68%;
    }

    .akc-col-right {
        flex: 1;
    }

    .akc-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .akc-card {
        overflow: hidden;
        box-sizing: border-box;
    }

    .form-control {
        box-sizing: border-box;
        width: 100%;
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

<form action="index.php?action=add_category" method="POST">
    <div class="akc-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_category" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Thêm mới danh mục</h2>
        <div><a href="index.php?action=product_category" class="btn-cancel">Hủy</a> <button type="submit" class="btn-save">Lưu</button></div>
    </div>

    <div class="akc-grid">
        <div class="akc-col-left">
            <div class="akc-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Thông tin chung</h3>
                <label>Tên danh mục <span style="color:red;">*</span></label>
                <input type="text" name="category_name" class="form-control" required>
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="akc-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Chọn sản phẩm</h3>
                <label class="radio-box">
                    <input type="radio" name="selection_type" value="manual" checked onchange="toggleAutoRules()">
                    <strong>Thêm sản phẩm thủ công</strong>
                </label>
                <p style="color: #637381; font-size: 14px; margin-left: 25px; margin-bottom: 20px;">Bạn sẽ chọn từng sản phẩm để thêm vào danh mục này.</p>

                <label class="radio-box">
                    <input type="radio" name="selection_type" value="auto" onchange="toggleAutoRules()">
                    <strong>Thêm sản phẩm tự động</strong>
                </label>
                <p style="color: #637381; font-size: 14px; margin-left: 25px;">Sản phẩm tự động được thêm vào danh mục nếu thỏa mãn điều kiện.</p>

                <div id="auto-rules-container" style="display: none; background: #fafbfc; border: 1px solid #dfe3e8; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                        <span>Sản phẩm phải thỏa mãn:</span>
                        <label><input type="radio" name="match_type" value="all" checked> Tất cả điều kiện</label>
                        <label><input type="radio" name="match_type" value="any"> Một trong các điều kiện</label>
                    </div>

                    <div id="rules-list">
                        <div class="rule-row">
                            <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                <option value="Tên sản phẩm">Tên sản phẩm</option>
                                <option value="Loại sản phẩm">Loại sản phẩm</option>
                                <option value="Nhà sản xuất">Nhà sản xuất</option>
                                <option value="Giá sản phẩm">Giá sản phẩm</option>
                                <option value="Tag sản phẩm">Tag sản phẩm</option>
                            </select>
                            <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                <option value="bằng">bằng</option>
                                <option value="bắt đầu với">bắt đầu với</option>
                                <option value="kết thúc với">kết thúc với</option>
                                <option value="chứa từ">chứa từ</option>
                                <option value="lớn hơn">lớn hơn</option>
                                <option value="nhỏ hơn">nhỏ hơn</option>
                            </select>
                            <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nhập giá trị...">
                            <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">🗑️</button>
                        </div>
                    </div>
                    <button type="button" onclick="addRule()" style="background:#fff; border:1px solid #0088ff; color:#0088ff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-top:10px;">+ Thêm điều kiện</button>
                </div>
            </div>
        </div>

        <div class="akc-col-right">
            <div class="akc-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Trạng thái</h3>
                <div class="radio-box"><input type="radio" name="status" value="Hiển thị" checked> Hiển thị</div>
                <div class="radio-box"><input type="radio" name="status" value="Ẩn"> Ẩn</div>
            </div>

            <div class="akc-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Sắp xếp sản phẩm</h3>
                <select name="sort_order" class="form-control">
                    <option value="newest">Theo ngày tạo: Từ mới đến cũ</option>
                    <option value="oldest">Theo ngày tạo: Từ cũ đến mới</option>
                    <option value="price_asc">Theo giá: Từ thấp đến cao</option>
                    <option value="price_desc">Theo giá: Từ cao đến thấp</option>
                    <option value="name_asc">Theo tên: A-Z</option>
                </select>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleAutoRules() {
        var type = document.querySelector('input[name="selection_type"]:checked').value;
        document.getElementById('auto-rules-container').style.display = (type === 'auto') ? 'block' : 'none';
    }

    function addRule() {
        var div = document.createElement('div');
        div.className = 'rule-row';
        div.innerHTML = `
            <select name="rule_field[]" class="form-control" style="margin:0; flex:1;"><option value="Tên sản phẩm">Tên sản phẩm</option><option value="Loại sản phẩm">Loại sản phẩm</option><option value="Nhà sản xuất">Nhà sản xuất</option><option value="Giá sản phẩm">Giá sản phẩm</option><option value="Tag sản phẩm">Tag sản phẩm</option></select>
            <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;"><option value="bằng">bằng</option><option value="bắt đầu với">bắt đầu với</option><option value="kết thúc với">kết thúc với</option><option value="chứa từ">chứa từ</option><option value="lớn hơn">lớn hơn</option><option value="nhỏ hơn">nhỏ hơn</option></select>
            <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nhập giá trị...">
            <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">🗑️</button>
        `;
        document.getElementById('rules-list').appendChild(div);
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
