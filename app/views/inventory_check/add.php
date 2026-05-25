<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $products */ ?>
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
        box-sizing: border-box;
    }

    .btn-save {
        background: #0088ff;
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

    .input-readonly {
        background: #f4f6f8;
        color: #637381;
        pointer-events: none;
    }
</style>

<form action="index.php?action=add_inventory_check" method="POST">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 20px; color: #212b36;"><a href="index.php?action=inventory_check_list" style="text-decoration:none; color:#637381;">←</a> Tạo phiếu kiểm kho</h2>
        <button type="submit" class="btn-save">Hoàn tất & Cân bằng kho</button>
    </div>

    <div class="sapo-card">
        <h3 style="font-size: 16px; margin-bottom: 10px;">Sản phẩm kiểm kho</h3>
        <table class="table-import" id="checkTable">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="width: 150px;">Tồn kho HT</th>
                    <th style="width: 150px;">Thực tế <span style="color:red;">*</span></th>
                    <th style="width: 150px;">Chênh lệch</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody id="checkBody">
                <tr class="item-row">
                    <td>
                        <select name="product_id[]" class="form-control" required onchange="selectProduct(this)">
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['id']; ?>" data-stock="<?php echo $p['ton_kho'] ?? 0; ?>">
                                    <?php echo htmlspecialchars($p['product_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="system_stock[]" class="form-control sys-stock input-readonly" value="0" readonly tabindex="-1"></td>
                    <td><input type="number" name="actual_stock[]" class="form-control actual-stock" value="0" min="0" required oninput="calcDiscrepancy(this)"></td>
                    <td>
                        <input type="number" name="discrepancy[]" class="form-control diff-stock input-readonly" value="0" readonly tabindex="-1">
                        <span class="diff-hint" style="font-size: 12px; font-weight: bold; margin-top: 5px; display:block;">Khớp</span>
                    </td>
                    <td style="padding-top: 15px;"><a href="javascript:void(0)" onclick="this.closest('tr').remove();" style="color: red; text-decoration: none;">✖</a></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addRow()" style="background: #e6f7ff; color: #0088ff; border: 1px dashed #0088ff; padding: 8px 15px; border-radius: 4px; margin-top: 15px; cursor: pointer;">+ Thêm dòng sản phẩm</button>
    </div>
</form>

<script>
    // JS Tự động điền Tồn kho Hệ thống khi chọn SP
    function selectProduct(selectObj) {
        let row = selectObj.closest('tr');
        let sysStockInput = row.querySelector('.sys-stock');
        let actualStockInput = row.querySelector('.actual-stock');

        if (selectObj.selectedIndex > 0) {
            let selectedOption = selectObj.options[selectObj.selectedIndex];
            let stock = selectedOption.getAttribute('data-stock');
            sysStockInput.value = stock;
            actualStockInput.value = stock; // Mặc định thực tế = hệ thống
        } else {
            sysStockInput.value = 0;
            actualStockInput.value = 0;
        }
        calcDiscrepancy(actualStockInput);
    }

    // JS Tự động tính độ chênh lệch (Thực tế - Hệ thống)
    function calcDiscrepancy(inputObj) {
        let row = inputObj.closest('tr');
        let sysStock = parseInt(row.querySelector('.sys-stock').value) || 0;
        let actualStock = parseInt(row.querySelector('.actual-stock').value) || 0;
        let diffInput = row.querySelector('.diff-stock');
        let diffHint = row.querySelector('.diff-hint');

        let diff = actualStock - sysStock;
        diffInput.value = diff;

        if (diff > 0) {
            diffHint.innerText = 'Thừa ' + diff;
            diffHint.style.color = '#108043';
        } else if (diff < 0) {
            diffHint.innerText = 'Thiếu ' + Math.abs(diff);
            diffHint.style.color = '#ff4d4f';
        } else {
            diffHint.innerText = 'Khớp';
            diffHint.style.color = '#212b36';
        }
    }

    function addRow() {
        let tbody = document.getElementById('checkBody');
        let tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = document.querySelector('.item-row').innerHTML;
        // Reset values for new row
        tr.querySelector('.sys-stock').value = 0;
        tr.querySelector('.actual-stock').value = 0;
        tr.querySelector('.diff-stock').value = 0;
        tr.querySelector('.diff-hint').innerText = 'Khớp';
        tr.querySelector('.diff-hint').style.color = '#212b36';
        tbody.appendChild(tr);
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
