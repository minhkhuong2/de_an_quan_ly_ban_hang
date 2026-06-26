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

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .ic-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .ic-table th,
    .ic-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #dfe3e8;
        text-align: center;
    }

    .ic-table th {
        background: #fafbfc;
        color: #637381;
        font-weight: 500;
    }

    .diff-positive {
        color: #108043;
        font-weight: bold;
    }

    /* TÄƒng */
    .diff-negative {
        color: #cf1322;
        font-weight: bold;
    }

    /* Giáº£m */
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><a href="index.php?action=inventory_check_list" style="text-decoration:none; color:#637381;">â†</a> Táº¡o phiáº¿u kiá»ƒm kho</h2>
</div>

<form action="index.php?action=add_inventory_check" method="POST">
    <div style="display: flex; gap: 20px;">
        <div style="flex: 0 0 70%;">
            <div class="Há»‡ thá»‘ng-card">
                <div style="font-weight: bold; margin-bottom: 15px;">Sáº£n pháº©m cáº§n kiá»ƒm</div>

                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 8px;">ðŸ”</span>
                    <select id="product-select" class="form-control" style="padding-left: 35px;" onchange="addProductRow()">
                        <option value="">TÃ¬m kiáº¿m sáº£n pháº©m Ä‘á»ƒ kiá»ƒm kho...</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-sku="<?php echo htmlspecialchars($p['sku'] ?? ''); ?>" data-stock="<?php echo $p['stock'] ?? 0; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> (Tá»“n: <?php echo $p['stock'] ?? 0; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="ic-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Sáº£n pháº©m</th>
                            <th>Tá»“n chi nhÃ¡nh</th>
                            <th>Tá»“n thá»±c táº¿</th>
                            <th>SL ChÃªnh lá»‡ch</th>
                            <th>LÃ½ do</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="ic-body"></tbody>
                </table>
            </div>
        </div>

        <div style="flex: 0 0 calc(30% - 20px);">
            <div class="Há»‡ thá»‘ng-card">
                <div style="font-weight: bold; margin-bottom: 15px;">ThÃ´ng tin phiáº¿u</div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #637381; font-size: 13px;">Chi nhÃ¡nh</label>
                    <select name="branch" class="form-control">
                        <option>Cá»­a hÃ ng chÃ­nh</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #637381; font-size: 13px;">NhÃ¢n viÃªn kiá»ƒm</label>
                    <input type="text" name="employee" class="form-control" value="Admin">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #637381; font-size: 13px;">Ghi chÃº</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="VÃ­ dá»¥: Kiá»ƒm kho Ä‘á»‹nh ká»³ thÃ¡ng..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="text-align: right; border-top: 1px solid #dfe3e8; padding-top: 20px;">
        <button type="submit" style="padding: 10px 20px; background: #0088ff; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            XÃ¡c nháº­n CÃ¢n Báº±ng Kho
        </button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const option = select.options[select.selectedIndex];
        if (!option.value) return;

        const id = option.value;
        const name = option.text.split(' (')[0];
        const sysStock = parseInt(option.getAttribute('data-stock')) || 0;

        if (document.getElementById('row-' + id)) {
            alert('Sáº£n pháº©m nÃ y Ä‘Ã£ cÃ³ trong danh sÃ¡ch kiá»ƒm!');
            select.value = '';
            return;
        }

        const tbody = document.getElementById('ic-body');
        const tr = document.createElement('tr');
        tr.id = 'row-' + id;
        tr.innerHTML = `
            <td style="text-align: left; font-weight: 500; color: #0088ff;">${name}</td>
            <td style="color: #637381;">
                ${sysStock}
                <input type="hidden" name="product_id[]" value="${id}">
                <input type="hidden" name="system_stock[]" id="sys-${id}" value="${sysStock}">
            </td>
            <td>
                <input type="number" name="actual_stock[]" id="act-${id}" value="${sysStock}" class="form-control" style="width: 80px; font-weight: bold;" oninput="calcDiff('${id}')">
            </td>
            <td id="diff-${id}">0</td>
            <td>
                <select name="reason[]" class="form-control"><option value="">-- Chá»n --</option><option value="HÃ ng há»ng">HÃ ng há»ng/lá»—i</option><option value="Máº¥t cáº¯p">Tháº¥t thoÃ¡t</option><option value="Nháº­p sai">Sai sá»‘ liá»‡u nháº­p</option></select>
            </td>
            <td><a href="javascript:void(0)" onclick="document.getElementById('row-${id}').remove()" style="color: #ff4d4f; text-decoration:none; font-weight:bold;">Ã—</a></td>
        `;
        tbody.appendChild(tr);
        select.value = '';
    }

    function calcDiff(id) {
        const sys = parseInt(document.getElementById('sys-' + id).value);
        let actInput = document.getElementById('act-' + id).value;
        const act = actInput === '' ? 0 : parseInt(actInput);

        const diff = act - sys;
        const diffCell = document.getElementById('diff-' + id);

        if (diff > 0) {
            diffCell.innerHTML = `<span class="diff-positive">+${diff}</span>`;
        } else if (diff < 0) {
            diffCell.innerHTML = `<span class="diff-negative">${diff}</span>`;
        } else {
            diffCell.innerHTML = `0`;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

