<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $allProducts */ ?>

<style>
    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212b36;
        padding-bottom: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .po-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .po-table th,
    .po-table td {
        padding: 14px 10px;
        border-bottom: 1px solid #dfe3e8;
        text-align: left;
        font-size: 14px;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 22px; font-weight: bold; color: #212b36;">
        <a href="index.php?action=purchase_return_list" style="text-decoration:none; color:#637381; margin-right: 10px;">â†</a>
        Táº¡o Ä‘Æ¡n tráº£ hÃ ng nháº­p (KhÃ´ng theo Ä‘Æ¡n)
    </h2>
</div>

<form action="index.php?action=add_direct_return" method="POST">
    <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">

        <div style="flex: 1 1 65%; min-width: 600px;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ‘¤ ThÃ´ng tin NhÃ  cung cáº¥p</div>
                <input type="text" name="supplier_name" class="form-control" placeholder="Nháº­p tÃªn nhÃ  cung cáº¥p cáº§n tráº£ hÃ ng..." required>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“¦ Chá»n sáº£n pháº©m xuáº¥t tráº£</div>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 11px; color: #637381;">ðŸ”</span>
                    <select id="product-select" class="form-control" style="padding-left: 40px; font-weight: 500;" onchange="addProductRow()">
                        <option value="">TÃ¬m kiáº¿m sáº£n pháº©m theo tÃªn, mÃ£ SKU...</option>
                        <?php foreach ($allProducts as $p): ?>
                            <?php if ($p['stock'] > 0): ?>
                                <option value="<?php echo $p['id']; ?>" data-sku="<?php echo htmlspecialchars($p['sku'] ?? ''); ?>" data-price="<?php echo htmlspecialchars($p['cost_price'] ?? 0); ?>" data-stock="<?php echo $p['stock']; ?>">
                                    <?php echo htmlspecialchars($p['product_name']); ?> (Tá»“n: <?php echo $p['stock']; ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="po-table" id="product-table">
                    <thead>
                        <tr style="background: #fafbfc; color: #212b36;">
                            <th style="width: 35%;">Sáº£n pháº©m</th>
                            <th style="width: 15%; text-align: center;">Tá»“n kho</th>
                            <th style="width: 15%; text-align: center;">SL Tráº£</th>
                            <th style="width: 20%; text-align: right;">ÄÆ¡n giÃ¡ tráº£</th>
                            <th style="width: 20%; text-align: right;">ThÃ nh tiá»n</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="po-body"></tbody>
                </table>
                <div style="text-align: right; margin-top: 30px; font-size: 16px; color: #212b36; border-top: 1px dashed #dfe3e8; padding-top: 20px;">
                    Tá»•ng giÃ¡ trá»‹ hoÃ n tráº£: <strong id="total-amount" style="color: #ff9900; font-size: 22px; margin-left: 10px;">0 â‚«</strong>
                </div>
            </div>
        </div>

        <div style="flex: 1 1 30%; min-width: 300px;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“ ThÃ´ng tin bá»• sung</div>
                <div class="form-group">
                    <label>Chi nhÃ¡nh xuáº¥t tráº£</label>
                    <select name="branch" class="form-control">
                        <option>Cá»­a hÃ ng chÃ­nh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>NhÃ¢n viÃªn phá»¥ trÃ¡ch</label>
                    <input type="text" name="employee" class="form-control" value="Admin">
                </div>
                <div class="form-group">
                    <label>LÃ½ do tráº£ hÃ ng</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="VÃ­ dá»¥: HÃ ng tá»“n kho quÃ¡ lÃ¢u, hÃ ng lá»—i..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" style="padding: 10px 20px; border-radius: 4px; border: 1px solid #c4cdd5; background: #fff; cursor: pointer;" onclick="window.location.href='index.php?action=purchase_return_list'">Há»§y bá»</button>
        <button type="submit" style="padding: 10px 20px; border-radius: 4px; border: none; background: #ff9900; color: #fff; font-weight: bold; cursor: pointer;">ðŸ“¤ Táº¡o Phiáº¿u Tráº£ HÃ ng</button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) return;

        const id = selectedOption.value;
        const name = selectedOption.text.split(' (')[0];
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;

        if (document.getElementById('row-' + id)) {
            alert('Sáº£n pháº©m Ä‘Ã£ cÃ³ trong danh sÃ¡ch!');
            select.value = '';
            return;
        }

        const tbody = document.getElementById('po-body');
        const tr = document.createElement('tr');
        tr.id = 'row-' + id;
        tr.innerHTML = `
            <td style="color: #0088ff; font-weight: 500;">${name}</td>
            <td style="text-align: center; font-weight: bold; color: #637381;">${stock}</td>
            <td style="text-align: center;">
                <input type="number" name="return_qty[]" id="qty-${id}" value="1" min="1" max="${stock}" class="form-control" oninput="updateRowTotal('${id}', ${stock})" style="padding: 8px; text-align: center; font-weight: bold; color: #cf1322;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td><input type="number" name="price[]" id="price-${id}" value="${price}" class="form-control" oninput="updateRowTotal('${id}', ${stock})" style="padding: 8px; text-align: right;"></td>
            <td id="total-${id}" style="text-align: right; font-weight: bold; color: #212b36;">${new Intl.NumberFormat('vi-VN').format(price)} â‚«</td>
            <td style="text-align: center;"><a href="javascript:void(0)" onclick="removeRow('${id}')" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold;">Ã—</a></td>
        `;
        tbody.appendChild(tr);
        select.value = '';
        updateGrandTotal();
    }

    function updateRowTotal(id, maxStock) {
        let qtyInput = document.getElementById('qty-' + id);
        let qty = parseInt(qtyInput.value) || 0;

        // Cáº£nh bÃ¡o náº¿u nháº­p quÃ¡ tá»“n kho
        if (qty > maxStock) {
            alert("Sá»‘ lÆ°á»£ng tráº£ khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ tá»“n kho hiá»‡n táº¡i (" + maxStock + ")!");
            qtyInput.value = maxStock;
            qty = maxStock;
        }

        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        document.getElementById('total-' + id).innerText = new Intl.NumberFormat('vi-VN').format(qty * price) + ' â‚«';
        updateGrandTotal();
    }

    function removeRow(id) {
        document.getElementById('row-' + id).remove();
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('input[name="price[]"]').forEach((priceInput) => {
            let id = priceInput.id.split('-')[1];
            let qty = parseInt(document.getElementById('qty-' + id).value) || 0;
            grandTotal += (qty * parseFloat(priceInput.value || 0));
        });
        document.getElementById('total-amount').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' â‚«';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

