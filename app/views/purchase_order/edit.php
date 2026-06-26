<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $order */
/** @var array $details */
/** @var array $allProducts */
?>

<style>
    /* Nâng cấp CSS giao diện cho đẹp và sắc nét hơn */
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212b36;
        padding-bottom: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .sapo-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    .sapo-col-left {
        flex: 1 1 65%;
        min-width: 600px;
    }

    .sapo-col-right {
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
        <a href="index.php?action=purchase_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Chỉnh sửa đơn đặt hàng: <span style="color: #0088ff;">#PON<?php echo $order['id']; ?></span>
    </h2>
</div>

<form action="index.php?action=edit_purchase&id=<?php echo $order['id']; ?>" method="POST">
    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">👤 Thông tin Nhà cung cấp</div>
                <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($order['supplier_name']); ?>" placeholder="Tìm nhà cung cấp...">
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">📦 Sản phẩm đặt mua</div>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 11px; color: #637381;">🔍</span>
                    <select id="product-select" class="form-control" style="padding-left: 40px; font-weight: 500;" onchange="addProductRow()">
                        <option value="">Thêm sản phẩm mới vào đơn hàng này...</option>
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
                            <th style="width: 15%;">Mã SKU</th>
                            <th style="width: 30%;">Tên sản phẩm</th>
                            <th style="width: 12%;">Đơn vị</th>
                            <th style="width: 12%;">Số lượng</th>
                            <th style="width: 15%;">Đơn giá</th>
                            <th style="width: 15%;">Thành tiền</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                        <?php foreach ($details as $item): ?>
                            <tr id="row-<?php echo $item['product_id']; ?>">
                                <td style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($item['sku'] ?? '---'); ?></td>
                                <td style="font-weight: 500; color: #212b36;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><select class="form-control" style="padding: 8px;">
                                        <option>Cái</option>
                                    </select></td>
                                <td>
                                    <input type="number" name="quantity[]" id="qty-<?php echo $item['product_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" oninput="updateRowTotal('<?php echo $item['product_id']; ?>')" style="padding: 8px; text-align: center; font-weight: bold;">
                                    <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
                                </td>
                                <td>
                                    <input type="number" name="price[]" id="price-<?php echo $item['product_id']; ?>" value="<?php echo $item['unit_price']; ?>" class="form-control" oninput="updateRowTotal('<?php echo $item['product_id']; ?>')" style="padding: 8px;">
                                </td>
                                <td id="total-<?php echo $item['product_id']; ?>" style="font-weight: bold; color: #212b36;"><?php echo number_format($item['quantity'] * $item['unit_price'], 0, ',', '.'); ?> ₫</td>
                                <td style="text-align: center;"><a href="javascript:void(0)" onclick="removeRow('<?php echo $item['product_id']; ?>')" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">×</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 30px; font-size: 16px; color: #212b36; border-top: 1px dashed #dfe3e8; padding-top: 20px;">
                    Tổng tiền đơn đặt hàng: <strong id="total-amount" style="color: #0088ff; font-size: 22px; margin-left: 10px;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</strong>
                </div>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">📝 Thông tin phiếu</div>
                <div class="form-group">
                    <label>Chi nhánh nhập</label>
                    <select name="branch" class="form-control">
                        <option <?php echo ($order['branch'] == 'Cửa hàng chính') ? 'selected' : ''; ?>>Cửa hàng chính</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhân viên phụ trách</label>
                    <input type="text" name="employee" class="form-control" value="<?php echo htmlspecialchars($order['employee']); ?>">
                </div>
                <div class="form-group">
                    <label>Ngày nhập dự kiến</label>
                    <input type="date" name="expected_date" class="form-control" value="<?php echo $order['expected_date']; ?>">
                </div>
                <div class="form-group">
                    <label>Mã tham chiếu</label>
                    <input type="text" name="reference" class="form-control" value="<?php echo htmlspecialchars($order['reference']); ?>" placeholder="Điền mã tham chiếu...">
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" class="btn btn-cancel" onclick="window.location.href='index.php?action=purchase_list'">Hủy bỏ</button>
        <button type="submit" class="btn btn-approve">💾 Lưu thay đổi phiếu</button>
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
            <td><select class="form-control" style="padding: 8px;"><option>Cái</option></select></td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 8px; text-align: center; font-weight: bold;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td><input type="number" name="price[]" id="price-${id}" value="${price}" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 8px;"></td>
            <td id="total-${id}" style="font-weight: bold; color: #212b36;">${new Intl.NumberFormat('vi-VN').format(price)} ₫</td>
            <td style="text-align: center;"><a href="javascript:void(0)" onclick="removeRow('${id}')" style="color: #ff4d4f; text-decoration: none; font-size: 22px; font-weight: bold; display: block; margin-top: -5px;">×</a></td>
        `;
        tbody.appendChild(tr);
        select.value = '';
        updateGrandTotal();
    }

    function updateRowTotal(id) {
        const qty = parseInt(document.getElementById('qty-' + id).value) || 0;
        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        const total = qty * price;
        document.getElementById('total-' + id).innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
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
        document.getElementById('total-amount').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' ₫';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
