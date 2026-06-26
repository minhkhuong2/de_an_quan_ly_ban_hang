<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $order */
/** @var array $details */
/** @var array $allProducts */
?>

<style>
    /* NÃ¢ng cáº¥p CSS giao diá»‡n cho Ä‘áº¹p vÃ  sáº¯c nÃ©t hÆ¡n */
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

    .Há»‡ thá»‘ng-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    .Há»‡ thá»‘ng-col-left {
        flex: 1 1 65%;
        min-width: 600px;
    }

    .Há»‡ thá»‘ng-col-right {
        flex: 1 1 30%;
        min-width: 300px;
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
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #0088ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 136, 255, 0.2);
    }

    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        border: 1px solid transparent;
    }

    .btn-cancel {
        background: #fff;
        border-color: #c4cdd5;
        color: #212b36;
    }

    .btn-cancel:hover {
        background: #f4f6f8;
    }

    .btn-approve {
        background: #0088ff;
        color: #fff;
        box-shadow: 0 2px 4px rgba(0, 136, 255, 0.2);
    }

    .btn-approve:hover {
        background: #0070d2;
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

    .po-table th {
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 22px; font-weight: bold; color: #212b36;">
        <a href="index.php?action=purchase_list" style="text-decoration:none; color:#637381; margin-right: 10px;">â†</a>
        Chá»‰nh sá»­a Ä‘Æ¡n Ä‘áº·t hÃ ng: <span style="color: #0088ff;">#PON<?php echo $order['id']; ?></span>
    </h2>
</div>

<form action="index.php?action=edit_purchase&id=<?php echo $order['id']; ?>" method="POST">
    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ‘¤ ThÃ´ng tin NhÃ  cung cáº¥p</div>
                <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($order['supplier_name']); ?>" placeholder="TÃ¬m nhÃ  cung cáº¥p...">
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“¦ Sáº£n pháº©m Ä‘áº·t mua</div>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 11px; color: #637381;">ðŸ”</span>
                    <select id="product-select" class="form-control" style="padding-left: 40px; font-weight: 500;" onchange="addProductRow()">
                        <option value="">ThÃªm sáº£n pháº©m má»›i vÃ o Ä‘Æ¡n hÃ ng nÃ y...</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-sku="<?php echo htmlspecialchars($p['sku'] ?? ''); ?>" data-price="<?php echo htmlspecialchars($p['cost_price'] ?? 0); ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> - SKU: <?php echo htmlspecialchars($p['sku'] ?? '---'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="po-table" id="product-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">MÃ£ SKU</th>
                            <th style="width: 30%;">TÃªn sáº£n pháº©m</th>
                            <th style="width: 12%;">ÄÆ¡n vá»‹</th>
                            <th style="width: 12%;">Sá»‘ lÆ°á»£ng</th>
                            <th style="width: 15%;">ÄÆ¡n giÃ¡</th>
                            <th style="width: 15%;">ThÃ nh tiá»n</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                        <?php foreach ($details as $item): ?>
                            <tr id="row-<?php echo $item['product_id']; ?>">
                                <td style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($item['sku'] ?? '---'); ?></td>
                                <td style="font-weight: 500; color: #212b36;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><select class="form-control" style="padding: 8px;">
                                        <option>CÃ¡i</option>
                                    </select></td>
                                <td>
                                    <input type="number" name="quantity[]" id="qty-<?php echo $item['product_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" oninput="updateRowTotal('<?php echo $item['product_id']; ?>')" style="padding: 8px; text-align: center; font-weight: bold;">
                                    <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
                                </td>
                                <td>
                                    <input type="number" name="price[]" id="price-<?php echo $item['product_id']; ?>" value="<?php echo $item['unit_price']; ?>" class="form-control" oninput="updateRowTotal('<?php echo $item['product_id']; ?>')" style="padding: 8px;">
                                </td>
                                <td id="total-<?php echo $item['product_id']; ?>" style="font-weight: bold; color: #212b36;"><?php echo number_format($item['quantity'] * $item['unit_price'], 0, ',', '.'); ?> â‚«</td>
                                <td style="text-align: center;"><a href="javascript:void(0)" onclick="removeRow('<?php echo $item['product_id']; ?>')" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">Ã—</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 30px; font-size: 16px; color: #212b36; border-top: 1px dashed #dfe3e8; padding-top: 20px;">
                    Tá»•ng tiá»n Ä‘Æ¡n Ä‘áº·t hÃ ng: <strong id="total-amount" style="color: #0088ff; font-size: 22px; margin-left: 10px;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> â‚«</strong>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“ ThÃ´ng tin phiáº¿u</div>
                <div class="form-group">
                    <label>Chi nhÃ¡nh nháº­p</label>
                    <select name="branch" class="form-control">
                        <option <?php echo ($order['branch'] == 'Cá»­a hÃ ng chÃ­nh') ? 'selected' : ''; ?>>Cá»­a hÃ ng chÃ­nh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>NhÃ¢n viÃªn phá»¥ trÃ¡ch</label>
                    <input type="text" name="employee" class="form-control" value="<?php echo htmlspecialchars($order['employee']); ?>">
                </div>
                <div class="form-group">
                    <label>NgÃ y nháº­p dá»± kiáº¿n</label>
                    <input type="date" name="expected_date" class="form-control" value="<?php echo $order['expected_date']; ?>">
                </div>
                <div class="form-group">
                    <label>MÃ£ tham chiáº¿u</label>
                    <input type="text" name="reference" class="form-control" value="<?php echo htmlspecialchars($order['reference']); ?>" placeholder="Äiá»n mÃ£ tham chiáº¿u...">
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" class="btn btn-cancel" onclick="window.location.href='index.php?action=purchase_list'">Há»§y bá»</button>
        <button type="submit" class="btn btn-approve">ðŸ’¾ LÆ°u thay Ä‘á»•i phiáº¿u</button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) return;

        const id = selectedOption.value;
        const name = selectedOption.text.split(' - ')[0];
        const sku = selectedOption.getAttribute('data-sku');
        const price = selectedOption.getAttribute('data-price') || 0;

        if (document.getElementById('row-' + id)) {
            let qtyInput = document.getElementById('qty-' + id);
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateRowTotal(id);
            select.value = '';
            return;
        }

        const tbody = document.getElementById('po-body');
        const tr = document.createElement('tr');
        tr.id = 'row-' + id;
        tr.innerHTML = `
            <td style="color: #0088ff; font-weight: 500;">${sku}</td>
            <td style="font-weight: 500; color: #212b36;">${name}</td>
            <td><select class="form-control" style="padding: 8px;"><option>CÃ¡i</option></select></td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 8px; text-align: center; font-weight: bold;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td><input type="number" name="price[]" id="price-${id}" value="${price}" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 8px;"></td>
            <td id="total-${id}" style="font-weight: bold; color: #212b36;">${new Intl.NumberFormat('vi-VN').format(price)} â‚«</td>
            <td style="text-align: center;"><a href="javascript:void(0)" onclick="removeRow('${id}')" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">Ã—</a></td>
        `;
        tbody.appendChild(tr);
        select.value = '';
        updateGrandTotal();
    }

    function updateRowTotal(id) {
        const qty = parseInt(document.getElementById('qty-' + id).value) || 0;
        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        const total = qty * price;
        document.getElementById('total-' + id).innerText = new Intl.NumberFormat('vi-VN').format(total) + ' â‚«';
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
            let price = parseFloat(priceInput.value) || 0;
            grandTotal += (qty * price);
        });
        document.getElementById('total-amount').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' â‚«';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

