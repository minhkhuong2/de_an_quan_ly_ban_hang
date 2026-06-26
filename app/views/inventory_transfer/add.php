<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $allProducts */ ?>

<style>
    .akc-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 20px;
    }

    .akc-card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212b36;
        padding-bottom: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .akc-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    .akc-col-left {
        flex: 1 1 65%;
        min-width: 600px;
    }

    .akc-col-right {
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
        <a href="index.php?action=transfer_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Tạo phiếu chuyển kho mới
    </h2>
</div>

<form action="index.php?action=add_transfer" method="POST">
    <div class="akc-grid">
        <div class="akc-col-left">
            <div class="akc-card">
                <div class="akc-card-title">📦 Sản phẩm chuyển kho</div>

                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 11px; color: #637381;">🔍</span>
                    <select id="product-select" class="form-control" style="padding-left: 40px; font-weight: 500;" onchange="addProductRow()">
                        <option value="">Tìm kiếm sản phẩm để chuyển kho...</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-sku="<?php echo htmlspecialchars($p['sku'] ?? ''); ?>" data-stock="<?php echo $p['stock'] ?? 0; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> (Tồn hiện tại: <?php echo $p['stock'] ?? 0; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="po-table" id="product-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Mã SKU</th>
                            <th style="width: 45%;">Tên sản phẩm</th>
                            <th style="width: 25%;">Số lượng chuyển</th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="akc-col-right">
            <div class="akc-card">
                <div class="akc-card-title">📝 Thông tin phiếu chuyển</div>

                <div class="form-group">
                    <label>Từ chi nhánh (Kho xuất hàng)</label>
                    <select name="from_branch" class="form-control">
                        <option value="Cửa hàng chính">🏠 Cửa hàng chính</option>
                        <option value="Chi nhánh 2">🏢 Chi nhánh 2</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Đến chi nhánh (Kho nhận hàng)</label>
                    <select name="to_branch" class="form-control">
                        <option value="Chi nhánh 2">🏢 Chi nhánh 2</option>
                        <option value="Cửa hàng chính">🏠 Cửa hàng chính</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhân viên tạo</label>
                    <input type="text" name="employee" class="form-control" value="Admin">
                </div>

                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Nhập lý do chuyển kho..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" class="btn btn-cancel" onclick="window.location.href='index.php?action=transfer_list'">Hủy bỏ</button>
        <button type="submit" class="btn btn-approve">💾 Tạo phiếu chuyển kho</button>
    </div>
</form>

<script>
    function addProductRow() {
        const select = document.getElementById('product-select');
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) return;

        const id = selectedOption.value;
        // Tách tên sản phẩm bỏ đi phần "(Tồn hiện tại...)"
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
                <div style="font-size: 12px; color: #637381; margin-top: 4px; font-weight: normal;">Kho xuất đang có: <strong style="color: #108043;">${stock}</strong></div>
            </td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" max="${stock}" class="form-control" style="padding: 8px; text-align: center; font-weight: bold; width: 100px;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td style="text-align: center;">
                <a href="javascript:void(0)" onclick="document.getElementById('row-${id}').remove()" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">×</a>
            </td>
        `;
        tbody.appendChild(tr);
        select.value = '';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
