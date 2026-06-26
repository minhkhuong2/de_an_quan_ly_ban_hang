<!-- Đường dẫn: app/views/purchase_order/add.php -->
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $allProducts */ ?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
    }

    .sapo-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .sapo-col-left {
        flex: 0 0 68%;
    }

    .sapo-col-right {
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

    .btn-save {
        background: #fff;
        border-color: #c4cdd5;
        color: #212b36;
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
    <h2><a href="index.php?action=inventory_list" style="text-decoration:none; color:#637381;">←</a> Tạo đơn đặt hàng nhập</h2>
</div>

<form action="index.php?action=add_purchase" method="POST">
    <div class="sapo-grid">
        <!-- CỘT TRÁI -->
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Nhà cung cấp</div>
                <input type="text" name="supplier_name" class="form-control" placeholder="Tìm nhà cung cấp hoặc thêm mới...">
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin sản phẩm</div>
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
                    <select id="product-select" class="form-control" style="padding-left: 35px;" onchange="addProductRow()">
                        <option value="">Tìm kiếm sản phẩm theo tên, SKU, Barcode...</option>
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
                            <th>Mã SKU</th>
                            <th>Tên sản phẩm</th>
                            <th style="width: 100px;">Đơn vị</th>
                            <th style="width: 100px;">Số lượng</th>
                            <th style="width: 150px;">Đơn giá</th>
                            <th style="width: 150px;">Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="po-body">
                        <!-- Sản phẩm được chọn sẽ tự động chèn vào đây -->
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 20px; font-size: 16px; color: #212b36;">
                    <strong>Tổng tiền: <span id="total-amount" style="color: #0088ff; font-size: 18px;">0</span> ₫</strong>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin đơn nhập</div>
                <div class="form-group">
                    <label>Chi nhánh nhập</label>
                    <select name="branch" class="form-control">
                        <option>Cửa hàng chính</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nhân viên phụ trách</label>
                    <input type="text" name="employee" class="form-control" value="Admin">
                </div>
                <div class="form-group">
                    <label>Ngày nhập dự kiến</label>
                    <input type="date" name="expected_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Mã đơn nhập</label>
                    <input type="text" name="reference" class="form-control" placeholder="Để trống hệ thống tự tạo">
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid #dfe3e8; padding-top: 20px;">
        <button type="button" class="btn" style="border-color: #c4cdd5;" onclick="window.location.href='index.php?action=inventory_list'">Hủy</button>
        <button type="submit" name="btn_draft" class="btn btn-save">Tạo đơn nháp</button>
        <button type="submit" name="btn_approve" class="btn btn-approve">Tạo & duyệt đơn</button>
    </div>
</form>

<script>
    // Xử lý Javascript thêm dòng và tính tổng tiền tự động
    function addProductRow() {
        const select = document.getElementById('product-select');
        const selectedOption = select.options[select.selectedIndex];

        if (!selectedOption.value) return;

        const id = selectedOption.value;
        const name = selectedOption.text.split(' - ')[0];
        const sku = selectedOption.getAttribute('data-sku');
        const price = selectedOption.getAttribute('data-price') || 0;

        // Nếu đã có trong bảng thì chỉ cộng dồn số lượng
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
            <td style="color: #0088ff;">${sku}</td>
            <td style="font-weight: 500;">${name}</td>
            <td>
                <select class="form-control" style="padding: 6px;"><option>Cái</option></select>
            </td>
            <td>
                <input type="number" name="quantity[]" id="qty-${id}" value="1" min="1" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 6px;">
                <input type="hidden" name="product_id[]" value="${id}">
            </td>
            <td>
                <input type="number" name="price[]" id="price-${id}" value="${price}" class="form-control" oninput="updateRowTotal('${id}')" style="padding: 6px;">
            </td>
            <td id="total-${id}" style="font-weight: 500;">${new Intl.NumberFormat('vi-VN').format(price)}</td>
            <td><a href="javascript:void(0)" onclick="removeRow('${id}')" style="color: #ff4d4f; text-decoration: none; font-size: 20px; font-weight: bold;">×</a></td>
        `;
        tbody.appendChild(tr);

        select.value = '';
        updateGrandTotal();
    }

    function updateRowTotal(id) {
        const qty = parseInt(document.getElementById('qty-' + id).value) || 0;
        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        const total = qty * price;
        document.getElementById('total-' + id).innerText = new Intl.NumberFormat('vi-VN').format(total);
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
        document.getElementById('total-amount').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal);
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
