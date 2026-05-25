<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $baseProducts Khai báo ẩn để mách cho VS Code biết biến này tồn tại */
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

    .table-combo {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .table-combo th {
        background: #fafbfc;
        padding: 10px;
        text-align: left;
        font-size: 14px;
        color: #637381;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-combo td {
        padding: 10px;
        border-bottom: 1px solid #f4f6f8;
    }

    .upload-box {
        border: 2px dashed #c4cdd5;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        background: #fafbfc;
    }
</style>

<form action="index.php?action=add_combo" method="POST" enctype="multipart/form-data">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_list" style="color:#637381; text-decoration:none; margin-right: 10px;">←</a> Thêm sản phẩm Combo</h2>
        <div>
            <a href="index.php?action=product_list" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Lưu sản phẩm</button>
        </div>
    </div>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin chung</div>
                <div class="form-group"><label>Tên sản phẩm *</label><input type="text" name="product_name" class="form-control" required></div>
                <div class="row-flex">
                    <div class="form-group"><label>Mã SKU</label><input type="text" name="sku" class="form-control"></div>
                    <div class="form-group"><label>Mã vạch / Barcode</label><input type="text" name="barcode" class="form-control"></div>
                </div>
                <div class="form-group" style="width: 48%;"><label>Đơn vị tính</label><input type="text" name="unit" class="form-control" value="Combo"></div>
                <div class="form-group"><label>Mô tả</label><textarea class="form-control" name="description" rows="3"></textarea></div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thành phần Combo</div>
                <p style="font-size: 14px; color: #637381; margin-bottom: 10px;">Số lượng thành phần không được phép lẻ hoặc nhỏ hơn 1.</p>

                <table class="table-combo" id="comboTable">
                    <thead>
                        <tr>
                            <th>Sản phẩm thành phần</th>
                            <th style="width: 150px;">Số lượng</th>
                            <th style="width: 150px;">Đơn giá</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="comboBody">
                        <tr class="item-row">
                            <td>
                                <select name="component_id[]" class="form-control prod-select" onchange="calcPrice()" required>
                                    <option value="">-- Tìm kiếm sản phẩm --</option>
                                    <?php foreach ($baseProducts as $p): ?>
                                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['base_price']; ?>">
                                            <?php echo htmlspecialchars($p['product_name']); ?> (Tồn: <?php echo $p['stock']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="component_qty[]" class="form-control qty-input" value="1" min="1" required oninput="calcPrice()"></td>
                            <td class="row-price" style="padding-top: 15px;">0 ₫</td>
                            <td style="padding-top: 15px;"><a href="javascript:void(0)" onclick="this.closest('tr').remove(); calcPrice();" style="color: red; text-decoration: none;">✖</a></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" onclick="addRow()" style="background: #e6f7ff; color: #0088ff; border: 1px dashed #0088ff; padding: 8px 15px; border-radius: 4px; margin-top: 15px; cursor: pointer; width: 100%;">+ Thêm sản phẩm vào combo</button>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin giá</div>
                <div class="row-flex">
                    <div class="form-group"><label>Giá bán (₫)</label><input type="number" name="base_price" id="total_combo_price" class="form-control" value="0"></div>
                    <div class="form-group"><label>Giá so sánh (₫)</label><input type="number" name="compare_price" class="form-control" value="0"></div>
                </div>
                <p style="font-size: 13px; color: #637381;">Hệ thống tự động tính giá gợi ý dựa trên tổng giá các sản phẩm thành phần. Bạn có thể tự sửa lại giá bán để làm khuyến mãi.</p>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Ảnh sản phẩm Combo</div>
                <div class="upload-box" onclick="document.getElementById('file-upload').click()">
                    <input type="file" id="file-upload" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                    <div id="upload-placeholder">
                        <div style="font-size: 24px; color: #0088ff;">+</div>
                        <div style="font-size: 14px;">Thêm ảnh từ thiết bị</div>
                    </div>
                    <img id="image-preview" src="" style="display: none; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 6px; object-fit: cover;">
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Phân loại</div>
                <div class="form-group"><label>Danh mục</label><input type="text" name="category" class="form-control"></div>
                <div class="form-group"><label>Nhãn hiệu</label><input type="text" name="brand" class="form-control"></div>
                <div class="form-group"><label>Tag</label><input type="text" name="tags" class="form-control"></div>
            </div>
        </div>
    </div>
</form>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            document.getElementById('image-preview').src = reader.result;
            document.getElementById('image-preview').style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    function calcPrice() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            let selectBox = row.querySelector('.prod-select');
            if (selectBox.selectedIndex > 0) {
                let price = selectBox.options[selectBox.selectedIndex].getAttribute('data-price');
                let qty = row.querySelector('.qty-input').value || 0;
                let subTotal = parseFloat(price) * parseInt(qty);
                row.querySelector('.row-price').innerText = subTotal.toLocaleString() + ' ₫';
                total += subTotal;
            }
        });
        document.getElementById('total_combo_price').value = total;
    }

    function addRow() {
        let tbody = document.getElementById('comboBody');
        let tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = document.querySelector('.item-row').innerHTML;
        tbody.appendChild(tr);
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
