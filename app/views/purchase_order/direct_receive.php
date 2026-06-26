<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $allProducts */ ?>

<style>
    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
    }

    .Há»‡ thá»‘ng-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .Há»‡ thá»‘ng-col-left {
        flex: 0 0 68%;
    }

    .Há»‡ thá»‘ng-col-right {
        flex: 0 0 calc(32% - 20px);
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px;
    }

    .btn-approve {
        background: #0088ff;
        color: #fff;
    }

    .po-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .po-table th,
    .po-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #dfe3e8;
        text-align: left;
        font-size: 14px;
    }

    .po-table th {
        background: #fafbfc;
        font-weight: 500;
        color: #637381;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><a href="index.php?action=purchase_list" style="text-decoration:none; color:#637381;">â†</a> Táº¡o Ä‘Æ¡n Nháº­p hÃ ng trá»±c tiáº¿p</h2>
</div>

<form action="index.php?action=direct_receive" method="POST">
    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">NhÃ  cung cáº¥p</div>
                <input type="text" name="supplier_name" class="form-control" placeholder="TÃ¬m nhÃ  cung cáº¥p hoáº·c thÃªm má»›i...">
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">Sáº£n pháº©m nháº­p hÃ ng</div>
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 10px; color: #637381;">ðŸ”</span>
                    <select id="product-select" class="form-control" style="padding-left: 35px;" onchange="addProductRow()">
                        <option value="">TÃ¬m kiáº¿m sáº£n pháº©m theo tÃªn, SKU, Barcode...</option>
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
                            <th>MÃ£ SKU</th>
                            <th>TÃªn sáº£n pháº©m</th>
                            <th style="width: 100px;">ÄÆ¡n vá»‹</th>
                            <th style="width: 100px;">Sá»‘ lÆ°á»£ng</th>
                            <th style="width: 150px;">ÄÆ¡n giÃ¡</th>
                            <th style="width: 150px;">ThÃ nh tiá»n</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="po-body"></tbody>
                </table>
                <div style="text-align: right; margin-top: 20px; font-size: 16px; color: #212b36;">
                    <strong>Tá»•ng tiá»n: <span id="total-amount" style="color: #0088ff; font-size: 18px;">0</span> â‚«</strong>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ThÃ´ng tin nháº­p kho</div>
                <div class="form-group">
                    <label>Chi nhÃ¡nh nháº­p</label>
                    <select name="branch" class="form-control">
                        <option>Cá»­a hÃ ng chÃ­nh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>NgÃ y ghi nháº­n</label>
                    <input type="date" name="expected_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div style="margin-top: 25px; padding: 15px; background: #eafff0; border: 1px solid #33d067; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" checked disabled style="width: 16px; height: 16px;">
                    <span style="color: #108043; font-weight: 500; font-size: 14px;">Nháº­p kho khi táº¡o Ä‘Æ¡n</span>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid #dfe3e8; padding-top: 20px;">
        <button type="button" class="btn" style="border: 1px solid #c4cdd5; background: #fff;" onclick="window.location.href='index.php?action=purchase_list'">Há»§y</button>
        <button type="submit" class="btn btn-approve">Táº¡o Ä‘Æ¡n & Nháº­p kho</button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const option = select.options[select.selectedIndex];
        if (!option.value) return;

        const id = option.value;
        const name = option.text.split(' - ')[0];
        const sku = option.getAttribute('data-sku');
        const price = option.getAttribute('data-price') || 0;

        if (document.getElementById('row-' + id)) {
            let input = document.getElementById('qty-' + id);
            input.value = parseInt(input.value) + 1;
            updateRowTotal(id);
            select.value = '';
            return;
        }

        const tbody = document.getElementById('po-body');
        const tr = document.createElement('tr');
        tr.id = 'row-' + id;
        tr.innerHTML = `
            <td style="color: #0088ff;">${sku}</td>
            <td style="font-weight: 500;">${name}</td>
            <td><select class="form-control" style="padding: 6px;"><option>CÃ¡i</option></select></td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 6px;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td><input type="number" name="price[]" id="price-${id}" value="${price}" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 6px;"></td>
            <td id="total-${id}" style="font-weight: 500;">${new Intl.NumberFormat('vi-VN').format(price)}</td>
            <td><a href="javascript:void(0)" onclick="removeRow('${id}')" style="color: #ff4d4f; text-decoration: none; font-size: 20px; font-weight: bold;">Ã—</a></td>
        `;
        tbody.appendChild(tr);
        select.value = '';
        updateGrandTotal();
    }

    function updateRowTotal(id) {
        const qty = parseInt(document.getElementById('qty-' + id).value) || 0;
        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        document.getElementById('total-' + id).innerText = new Intl.NumberFormat('vi-VN').format(qty * price);
        updateGrandTotal();
    }

    function removeRow(id) {
        document.getElementById('row-' + id).remove();
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('input[name="price[]"]').forEach(input => {
            let id = input.id.split('-')[1];
            let qty = parseInt(document.getElementById('qty-' + id).value) || 0;
            total += qty * (parseFloat(input.value) || 0);
        });
        document.getElementById('total-amount').innerText = new Intl.NumberFormat('vi-VN').format(total);
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

