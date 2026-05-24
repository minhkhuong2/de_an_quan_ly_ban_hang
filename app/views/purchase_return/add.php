<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
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
    }

    .btn-save {
        background: #ff4d4f;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
    }

    .table-import {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table-import th {
        background: #fafbfc;
        padding: 10px;
        text-align: left;
        font-size: 14px;
        color: #637381;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-import td {
        padding: 10px;
        border-bottom: 1px solid #f4f6f8;
    }
</style>

<form action="index.php?action=add_purchase_return" method="POST">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 20px; color: #212b36;"><a href="index.php?action=purchase_return_list" style="text-decoration:none; color:#637381;">←</a> Tạo đơn trả hàng nhập</h2>
        <button type="submit" class="btn-save">Hoàn tất trả hàng</button>
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 0 0 70%;">
            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Chi tiết sản phẩm hoàn trả</h3>

                <table class="table-import" id="importTable">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="width: 120px;">Số lượng trả</th>
                            <th style="width: 150px;">Đơn giá trả (₫)</th>
                            <th style="width: 150px;">Thành tiền</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="importBody">
                        <tr class="item-row">
                            <td>
                                <select name="product_id[]" class="form-control" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['product_name']); ?> (Tồn: <?php echo $p['stock']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="quantity[]" class="form-control qty-input" value="1" min="1" required oninput="calcTotal()"></td>
                            <td><input type="number" name="return_price[]" class="form-control price-input" value="0" min="0" required oninput="calcTotal()"></td>
                            <td class="row-total" style="font-weight: bold; color: #ff4d4f; padding-top: 15px;">0 ₫</td>
                            <td style="padding-top: 15px;"><a href="javascript:void(0)" onclick="this.closest('tr').remove(); calcTotal();" style="color: red; text-decoration: none;">✖</a></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" onclick="addRow()" style="background: #fff; color: #ff4d4f; border: 1px dashed #ff4d4f; padding: 8px 15px; border-radius: 4px; margin-top: 15px; cursor: pointer; width: 100%;">+ Thêm dòng sản phẩm</button>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 10px;">Nhà cung cấp</h3>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Chọn nhà cung cấp --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['supplier_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sapo-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Giá trị hoàn trả</h3>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #ff4d4f;">
                    <span>Tổng tiền:</span>
                    <span id="grandTotal">0 ₫</span>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function calcTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            let qty = row.querySelector('.qty-input').value || 0;
            let price = row.querySelector('.price-input').value || 0;
            let rowTotal = qty * price;
            row.querySelector('.row-total').innerText = rowTotal.toLocaleString() + ' ₫';
            total += rowTotal;
        });
        document.getElementById('grandTotal').innerText = total.toLocaleString() + ' ₫';
    }

    function addRow() {
        let tbody = document.getElementById('importBody');
        let tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = document.querySelector('.item-row').innerHTML;
        tbody.appendChild(tr);
        calcTotal();
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
