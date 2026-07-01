<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    .v3-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #dfe3e8; margin-bottom: 20px; }
    .card-header { padding: 15px 20px; border-bottom: 1px solid #dfe3e8; background: #fafbfc; font-weight: 600; color: #212b36; }
    .card-body { padding: 20px; }
    
    .layout-grid { display: flex; gap: 20px; align-items: flex-start; }
    .col-main { flex: 0 0 65%; display: flex; flex-direction: column; gap: 20px; }
    .col-side { flex: 1; display: flex; flex-direction: column; gap: 20px; }
    
    table { width: 100%; border-collapse: collapse; }
    th { background: #f4f6f8; padding: 10px; color: #637381; font-size: 13px; text-align: left; border-bottom: 1px solid #dfe3e8; }
    td { padding: 15px 10px; border-bottom: 1px solid #dfe3e8; vertical-align: middle; }
    
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; }
    .qty-input { width: 60px; text-align: center; }
    
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #212b36; }
    .summary-row.total { font-weight: bold; font-size: 16px; border-top: 1px solid #dfe3e8; padding-top: 15px; margin-top: 5px; }
    
    .btn-primary { background: #0088ff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; width: 100%; margin-bottom: 10px; }
    .btn-outline { background: #fff; color: #212b36; padding: 10px 20px; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: 600; cursor: pointer; width: 100%; margin-bottom: 10px; }
    .btn-refund { background: #ff9900; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; width: 100%; }

    .search-box { position: relative; width: 100%; margin-bottom: 15px; }
    .dropdown-results { position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #dfe3e8; border-radius: 4px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 100; display: none; max-height: 200px; overflow-y: auto; }
    .dropdown-item { padding: 10px 15px; border-bottom: 1px solid #f4f6f8; cursor: pointer; display: flex; justify-content: space-between; }
    .dropdown-item:hover { background: #f4f6f8; }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=view_order&id=<?php echo $order['id']; ?>" style="text-decoration: none; color: #637381;">←</a> Tạo đơn trả hàng</div>
</div>

<div class="layout-grid">
    <div class="col-main">
        <!-- Khối Hàng Trả -->
        <div class="v3-card">
            <div class="card-header">1. Sản phẩm trả hàng</div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">Đã mua</th>
                            <th style="text-align: center;">Trả lại</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <?php $max_return = $item['quantity'] - $item['returned_qty']; ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div style="font-size: 12px; color: #637381;"><?php echo htmlspecialchars($item['sku']); ?></div>
                                </td>
                                <td style="text-align: center;"><?php echo $max_return; ?></td>
                                <td style="text-align: center;">
                                    <input type="number" class="form-control qty-input return-qty" 
                                           data-id="<?php echo $item['id']; ?>" 
                                           data-product-id="<?php echo $item['product_id']; ?>" 
                                           data-price="<?php echo $item['price']; ?>" 
                                           value="0" min="0" max="<?php echo $max_return; ?>" onchange="calculateTotals()">
                                </td>
                                <td style="text-align: right;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                <td style="text-align: right; font-weight: bold;" class="return-line-total">0đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Khối Hàng Đổi -->
        <div class="v3-card">
            <div class="card-header">2. Chọn sản phẩm đổi (Nếu có)</div>
            <div class="card-body">
                <div class="search-box">
                    <input type="text" id="exchange_search" class="form-control" placeholder="Tìm sản phẩm khách muốn đổi...">
                    <div id="exchange_dropdown" class="dropdown-results"></div>
                </div>
                
                <table id="exchange_table" style="display: none;">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">Số lượng</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="exchange_body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-side">
        <!-- Tóm tắt -->
        <div class="v3-card">
            <div class="card-header">Tóm tắt bù trừ</div>
            <div class="card-body">
                <div class="summary-row">
                    <span>Tổng hoàn sản phẩm (Hàng trả)</span>
                    <span id="sum_return">0đ</span>
                </div>
                <div class="summary-row">
                    <span>Tổng sản phẩm đổi (Hàng mới)</span>
                    <span id="sum_exchange">0đ</span>
                </div>
                
                <div class="summary-row total" id="refund_row">
                    <span>Cần hoàn tiền cho khách</span>
                    <span id="sum_refund" style="color: #d82c0d;">0đ</span>
                </div>
                <div class="summary-row total" id="charge_row" style="display: none;">
                    <span>Khách cần trả thêm</span>
                    <span id="sum_charge" style="color: #108043;">0đ</span>
                </div>

                <div style="margin-top: 20px;">
                    <textarea id="return_note" class="form-control" placeholder="Ghi chú đơn trả hàng (nếu có)..." rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="v3-card" style="padding: 15px; background: transparent; border: none; box-shadow: none;">
            <button class="btn-outline" onclick="submitReturn('draft')">Tạo phiếu nháp</button>
            <button class="btn-primary" onclick="submitReturn('confirm')">Xác nhận đơn đổi trả (Nhận hàng sau)</button>
            <button class="btn-refund" id="btn_refund" onclick="submitReturn('refund_now')">Tạo đơn & Hoàn tiền ngay</button>
        </div>
    </div>
</div>

<script>
    const allProducts = <?php echo $products_json; ?>;
    let exchangeCart = [];

    // TÌM SẢN PHẨM ĐỔI
    const searchInput = document.getElementById('exchange_search');
    const dropdown = document.getElementById('exchange_dropdown');
    
    searchInput.addEventListener('input', function() {
        let val = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        if (val.length < 1) { dropdown.style.display = 'none'; return; }
        
        let filtered = allProducts.filter(p => p.product_name.toLowerCase().includes(val) || p.sku.toLowerCase().includes(val));
        if (filtered.length > 0) {
            filtered.forEach(p => {
                let div = document.createElement('div');
                div.className = 'dropdown-item';
                div.innerHTML = `<div><strong>${p.product_name}</strong><br><small>${p.sku}</small></div> <div>${Number(p.price).toLocaleString('vi-VN')}đ<br><small>Tồn: ${p.stock}</small></div>`;
                div.onclick = () => addExchangeItem(p);
                dropdown.appendChild(div);
            });
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    function addExchangeItem(p) {
        let existing = exchangeCart.find(i => i.id === p.id);
        if (existing) {
            existing.qty += 1;
        } else {
            exchangeCart.push({ id: p.id, product_name: p.product_name, sku: p.sku, price: p.price, qty: 1 });
        }
        searchInput.value = '';
        dropdown.style.display = 'none';
        renderExchangeCart();
    }

    function removeExchangeItem(id) {
        exchangeCart = exchangeCart.filter(i => i.id !== id);
        renderExchangeCart();
    }

    function updateExchangeQty(id, qty) {
        let item = exchangeCart.find(i => i.id === id);
        if (item) {
            item.qty = parseInt(qty) || 1;
            renderExchangeCart();
        }
    }

    function renderExchangeCart() {
        const tbody = document.getElementById('exchange_body');
        const table = document.getElementById('exchange_table');
        tbody.innerHTML = '';
        
        if (exchangeCart.length === 0) {
            table.style.display = 'none';
        } else {
            table.style.display = 'table';
            exchangeCart.forEach(item => {
                let tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div style="font-weight: 600; color: #0088ff;">${item.product_name}</div>
                        <div style="font-size: 12px; color: #637381;">${item.sku}</div>
                    </td>
                    <td style="text-align: center;">
                        <input type="number" class="form-control qty-input" value="${item.qty}" min="1" onchange="updateExchangeQty(${item.id}, this.value)">
                    </td>
                    <td style="text-align: right;">${Number(item.price).toLocaleString('vi-VN')}đ</td>
                    <td style="text-align: right; font-weight: bold;">${(item.price * item.qty).toLocaleString('vi-VN')}đ</td>
                    <td style="text-align: center; color: #d82c0d; cursor: pointer;" onclick="removeExchangeItem(${item.id})">✖</td>
                `;
                tbody.appendChild(tr);
            });
        }
        calculateTotals();
    }

    function calculateTotals() {
        let totalReturn = 0;
        document.querySelectorAll('.return-qty').forEach(input => {
            let qty = parseInt(input.value) || 0;
            let price = parseFloat(input.dataset.price) || 0;
            let lineTotal = qty * price;
            input.closest('tr').querySelector('.return-line-total').innerText = lineTotal.toLocaleString('vi-VN') + 'đ';
            totalReturn += lineTotal;
        });

        let totalExchange = exchangeCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        
        let diff = totalReturn - totalExchange; // >0 là hoàn tiền, <0 là thu thêm

        document.getElementById('sum_return').innerText = totalReturn.toLocaleString('vi-VN') + 'đ';
        document.getElementById('sum_exchange').innerText = totalExchange.toLocaleString('vi-VN') + 'đ';

        if (diff > 0) {
            document.getElementById('refund_row').style.display = 'flex';
            document.getElementById('charge_row').style.display = 'none';
            document.getElementById('sum_refund').innerText = diff.toLocaleString('vi-VN') + 'đ';
            document.getElementById('btn_refund').innerText = 'Tạo đơn & Hoàn tiền ngay (' + diff.toLocaleString('vi-VN') + 'đ)';
            document.getElementById('btn_refund').style.background = '#ff9900';
            document.getElementById('btn_refund').style.display = 'block';
        } else if (diff < 0) {
            document.getElementById('refund_row').style.display = 'none';
            document.getElementById('charge_row').style.display = 'flex';
            document.getElementById('sum_charge').innerText = Math.abs(diff).toLocaleString('vi-VN') + 'đ';
            document.getElementById('btn_refund').innerText = 'Tạo đơn & Thu thêm khách (' + Math.abs(diff).toLocaleString('vi-VN') + 'đ)';
            document.getElementById('btn_refund').style.background = '#108043';
            document.getElementById('btn_refund').style.display = 'block';
        } else {
            document.getElementById('refund_row').style.display = 'flex';
            document.getElementById('charge_row').style.display = 'none';
            document.getElementById('sum_refund').innerText = '0đ';
            document.getElementById('btn_refund').style.display = 'none';
        }
    }

    function submitReturn(actionType) {
        let returnItems = [];
        let totalReturn = 0;
        document.querySelectorAll('.return-qty').forEach(input => {
            let qty = parseInt(input.value) || 0;
            if (qty > 0) {
                let price = parseFloat(input.dataset.price) || 0;
                returnItems.push({
                    order_item_id: input.dataset.id,
                    product_id: input.dataset.productId,
                    qty_returned: qty,
                    price: price,
                    line_total: qty * price
                });
                totalReturn += (qty * price);
            }
        });

        if (returnItems.length === 0 && exchangeCart.length === 0) {
            alert("Vui lòng chọn ít nhất 1 sản phẩm trả hoặc đổi!");
            return;
        }

        let totalExchange = exchangeCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        let refundAmount = totalReturn - totalExchange;

        let exchangeItems = exchangeCart.map(i => ({
            product_id: i.id,
            qty: i.qty,
            price: i.price,
            discount: 0,
            line_total: i.qty * i.price
        }));

        let payload = {
            order_id: <?php echo $order['id']; ?>,
            action_type: actionType,
            return_items: returnItems,
            exchange_items: exchangeItems,
            summary: {
                total_return_value: totalReturn,
                total_exchange_value: totalExchange,
                refund_amount: refundAmount
            },
            note: document.getElementById('return_note').value
        };

        fetch('index.php?action=store_return', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Tạo đơn đổi trả thành công!");
                window.location.href = 'index.php?action=return_detail&id=' + data.return_id;
            } else {
                alert("Lỗi: " + data.message);
            }
        })
        .catch(err => alert("Lỗi kết nối máy chủ!"));
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
