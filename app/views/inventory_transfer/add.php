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
        <a href="index.php?action=transfer_list" style="text-decoration:none; color:#637381; margin-right: 10px;">â†</a>
        Táº¡o phiáº¿u chuyá»ƒn kho má»›i
    </h2>
</div>

<form action="index.php?action=add_transfer" method="POST">
    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“¦ Sáº£n pháº©m chuyá»ƒn kho</div>

                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 11px; color: #637381;">ðŸ”</span>
                    <select id="product-select" class="form-control" style="padding-left: 40px; font-weight: 500;" onchange="addProductRow()">
                        <option value="">TÃ¬m kiáº¿m sáº£n pháº©m Ä‘á»ƒ chuyá»ƒn kho...</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-sku="<?php echo htmlspecialchars($p['sku'] ?? ''); ?>" data-stock="<?php echo $p['stock'] ?? 0; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> (Tá»“n hiá»‡n táº¡i: <?php echo $p['stock'] ?? 0; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="po-table" id="product-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">MÃ£ SKU</th>
                            <th style="width: 45%;">TÃªn sáº£n pháº©m</th>
                            <th style="width: 25%;">Sá»‘ lÆ°á»£ng chuyá»ƒn</th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“ ThÃ´ng tin phiáº¿u chuyá»ƒn</div>

                <div class="form-group">
                    <label>Tá»« chi nhÃ¡nh (Kho xuáº¥t hÃ ng)</label>
                    <select name="from_branch" class="form-control">
                        <option value="Cá»­a hÃ ng chÃ­nh">ðŸ  Cá»­a hÃ ng chÃ­nh</option>
                        <option value="Chi nhÃ¡nh 2">ðŸ¢ Chi nhÃ¡nh 2</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Äáº¿n chi nhÃ¡nh (Kho nháº­n hÃ ng)</label>
                    <select name="to_branch" class="form-control">
                        <option value="Chi nhÃ¡nh 2">ðŸ¢ Chi nhÃ¡nh 2</option>
                        <option value="Cá»­a hÃ ng chÃ­nh">ðŸ  Cá»­a hÃ ng chÃ­nh</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NhÃ¢n viÃªn táº¡o</label>
                    <input type="text" name="employee" class="form-control" value="Admin">
                </div>

                <div class="form-group">
                    <label>Ghi chÃº</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Nháº­p lÃ½ do chuyá»ƒn kho..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" class="btn btn-cancel" onclick="window.location.href='index.php?action=transfer_list'">Há»§y bá»</button>
        <button type="submit" class="btn btn-approve">ðŸ’¾ Táº¡o phiáº¿u chuyá»ƒn kho</button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) return;

        const id = selectedOption.value;
        // TÃ¡ch tÃªn sáº£n pháº©m bá» Ä‘i pháº§n "(Tá»“n hiá»‡n táº¡i...)"
        const name = selectedOption.text.split(' (')[0];
        const sku = selectedOption.getAttribute('data-sku');
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

        if (document.getElementById('row-' + id)) {
            let qtyInput = document.getElementById('qty-' + id);
            qtyInput.value = parseInt(qtyInput.value) + 1;
            select.value = '';
            return;
        }

        const tbody = document.getElementById('po-body');
        const tr = document.createElement('tr');
        tr.id = 'row-' + id;
        tr.innerHTML = `
            <td style="color: #0088ff; font-weight: 500;">${sku}</td>
            <td style="font-weight: 500; color: #212b36;">
                ${name}
                <div style="font-size: 12px; color: #637381; margin-top: 4px; font-weight: normal;">Kho xuáº¥t Ä‘ang cÃ³: <strong style="color: #108043;">${stock}</strong></div>
            </td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" max="${stock}" class="form-control" style="padding: 8px; text-align: center; font-weight: bold; width: 100px;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td style="text-align: center;">
                <a href="javascript:void(0)" onclick="document.getElementById('row-${id}').remove()" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">Ã—</a>
            </td>
        `;
        tbody.appendChild(tr);
        select.value = '';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

