<?php

/** @var string $products_json */
/** @var string $customers_json */
/** @var array $settings_db */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sapo OmniAI - POS Đa Đơn Hàng</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f6f8;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* TOP BAR & TABS */
        .pos-topbar {
            background: #0088ff;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            height: 55px;
            gap: 20px;
        }

        .pos-topbar .back-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* THANH ĐIỀU HƯỚNG TABS THEO ẢNH SAPO */
        .order-tabs-container {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
            height: 100%;
            align-items: flex-end;
        }

        .order-tab {
            background: rgba(255, 255, 255, 0.2);
            color: #e5f0ff;
            padding: 8px 16px;
            border-radius: 6px 6px 0 0;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            height: 38px;
        }

        .order-tab.active {
            background: #fff;
            color: #0088ff;
            font-weight: 600;
        }

        .btn-add-tab {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3px;
            margin-left: 5px;
            transition: 0.2s;
        }

        .btn-add-tab:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .close-tab {
            font-size: 14px;
            color: #8c98a4;
            cursor: pointer;
            padding: 2px;
        }

        .close-tab:hover {
            color: #d82c0d;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
            margin-left: auto;
        }

        .search-bar input {
            width: 100%;
            padding: 8px 15px 8px 35px;
            border-radius: 20px;
            border: none;
            outline: none;
            font-size: 13px;
        }

        .search-bar span {
            position: absolute;
            left: 12px;
            top: 8px;
            color: #8c98a4;
        }

        /* MAIN LAYOUT */
        .pos-main {
            display: flex;
            flex: 1;
            height: calc(100vh - 55px);
        }

        .pos-left {
            flex: 0 0 65%;
            background: #fff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #dfe3e8;
        }

        .cart-header {
            display: flex;
            font-size: 13px;
            font-weight: 600;
            color: #637381;
            padding: 12px 20px;
            border-bottom: 1px solid #dfe3e8;
            background: #fafbfc;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px dashed #dfe3e8;
        }

        .item-stt {
            width: 30px;
            font-weight: 600;
            color: #8c98a4;
        }

        .item-info {
            flex: 1;
        }

        .item-qty {
            width: 100px;
            text-align: center;
        }

        .item-qty input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
        }

        .item-price {
            width: 150px;
            text-align: right;
        }

        .item-total {
            width: 150px;
            text-align: right;
            font-weight: 600;
            color: #0088ff;
        }

        .item-del {
            width: 40px;
            text-align: right;
            cursor: pointer;
            color: #d82c0d;
        }

        .pos-right {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .customer-section {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #dfe3e8;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .customer-section input {
            width: 100%;
            border: none;
            font-size: 15px;
            outline: none;
        }

        .billing-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 15px;
            color: #212b36;
        }

        .billing-total {
            font-size: 22px;
            font-weight: 700;
            color: #0088ff;
            padding-top: 15px;
            border-top: 1px dashed #dfe3e8;
            margin-top: auto;
        }

        .promo-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .promo-input-group input {
            flex: 1;
            padding: 10px;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
            outline: none;
            text-transform: uppercase;
        }

        .promo-input-group button {
            background: #fff;
            border: 1px solid #0088ff;
            color: #0088ff;
            padding: 0 15px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        .pay-btn {
            background: #0088ff;
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .pay-btn:hover {
            background: #0070d2;
        }
    </style>
</head>

<body>

    <div class="pos-topbar">
        <a href="index.php?action=dashboard" class="back-link">← Admin</a>

        <div class="order-tabs-container" id="order_tabs_bar">
        </div>
        <button class="btn-add-tab" onclick="createNewOrderTab()" title="Tạo thêm đơn hàng mới (Dấu + trong ảnh Sapo)">+</button>

        <div class="search-bar">
            <span>🔍</span>
            <input type="text" id="product_search" placeholder="F3 - Tìm mặt hàng hoặc quét mã vạch...">
        </div>
    </div>

    <div class="pos-main">
        <div class="pos-left">
            <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <label style="font-size: 14px; cursor: pointer; color: #212b36; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="cb_separate_line" style="width: 16px; height: 16px;"> Tách dòng sản phẩm
                </label>
                <button onclick="addCustomProduct()" style="background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600;">+ SP tùy chỉnh (F2)</button>
            </div>
            <div class="cart-header">
                <div style="width: 30px;">#</div>
                <div style="flex: 1;">Tên sản phẩm</div>
                <div style="width: 100px; text-align: center;">Số lượng</div>
                <div style="width: 150px; text-align: right;">Đơn giá</div>
                <div style="width: 150px; text-align: right;">Thành tiền</div>
                <div style="width: 40px;"></div>
            </div>
            <div class="cart-body" id="cart_body">
            </div>
        </div>

        <div class="pos-right">
            <div class="customer-section">
                <span style="margin-right: 10px;">👤</span>
                <input type="text" placeholder="Tìm tên hoặc SĐT khách hàng...">
            </div>

            <?php if (isset($settings_db['pos_use_promo_code']) && $settings_db['pos_use_promo_code'] == '1'): ?>
                <div class="promo-input-group">
                    <input type="text" id="promo_code_input" placeholder="Nhập mã khuyến mại (nếu có)">
                    <button type="button" id="btn_apply_promo">ÁP DỤNG</button>
                </div>
                <div id="applied_promo_tag" style="display:none; color:#108043; font-size:14px; font-weight:500; margin-top:-10px; margin-bottom:20px;">
                    ✅ Mã: <span id="current_code_text"></span>
                    <span style="color:#d82c0d; cursor:pointer; font-weight:normal; float:right;" onclick="removePromoCode()">[Xóa]</span>
                </div>
            <?php endif; ?>

            <div class="billing-row">
                <span>Tổng tiền hàng <span id="txt_total_qty" style="color:#8c98a4; font-size:13px;">(0)</span></span>
                <span id="txt_subtotal" style="font-weight: 500;">0 ₫</span>
            </div>
            <div class="billing-row" style="color: #108043;">
                <span>Giảm giá tự động / Coupon</span>
                <span id="txt_discount">- 0 ₫</span>
            </div>

            <div class="billing-row billing-total">
                <span>KHÁCH CẦN TRẢ</span>
                <span id="txt_grand_total">0 ₫</span>
            </div>

            <button class="pay-btn" id="btn_checkout">THANH TOÁN (F9)</button>
        </div>
    </div>

    <script>
        const PRODUCTS = <?php echo $products_json; ?>;

        // ĐỘT PHÁ TƯ DUY: Quản lý nhiều đơn hàng bằng Object cấu trúc Mảng
        let ordersData = {
            1: {
                cart: [],
                appliedPromoCode: '',
                summary: {
                    total_product_discount: 0,
                    total_order_discount: 0,
                    grand_total: 0
                }
            }
        };
        let activeTabId = 1;
        let nextTabId = 2;

        // ==========================================
        // 1. QUẢN LÝ TABS ĐƠN HÀNG (SỬA LỖI THEO ĐÚNG GỢI Ý ẢNH SAPO)
        // ==========================================
        function renderTabsBar() {
            const tabsBar = document.getElementById('order_tabs_bar');
            tabsBar.innerHTML = '';

            Object.keys(ordersData).forEach(tabId => {
                let isActive = (tabId == activeTabId) ? 'active' : '';
                let countItems = ordersData[tabId].cart.reduce((sum, i) => sum + i.qty, 0);
                let badgeCount = countItems > 0 ? ` (${countItems})` : '';

                // Nút đóng tab ẩn đi nếu chỉ còn duy nhất 1 tab
                let closeBtn = Object.keys(ordersData).length > 1 ? `<span class="close-tab" onclick="event.stopPropagation(); closeOrderTab(${tabId})">×</span>` : '';

                tabsBar.innerHTML += `
                <button class="order-tab ${isActive}" onclick="switchOrderTab(${tabId})">
                    📦 Đơn ${tabId}${badgeCount}
                    ${closeBtn}
                </button>
            `;
            });
        }

        function createNewOrderTab() {
            ordersData[nextTabId] = {
                cart: [],
                appliedPromoCode: '',
                summary: {
                    total_product_discount: 0,
                    total_order_discount: 0,
                    grand_total: 0
                }
            };
            activeTabId = nextTabId;
            nextTabId++;
            renderTabsBar();
            triggerCalculation(); // Vẽ lại giỏ hàng rỗng của tab mới
        }

        function switchOrderTab(tabId) {
            activeTabId = tabId;
            renderTabsBar();

            // Đưa mã coupon của tab này lên ô input nếu có
            let promoInput = document.getElementById('promo_code_input');
            if (promoInput) {
                let currentPromo = ordersData[activeTabId].appliedPromoCode;
                if (currentPromo) {
                    document.getElementById('applied_promo_tag').style.display = 'block';
                    document.getElementById('current_code_text').innerText = currentPromo;
                } else {
                    document.getElementById('applied_promo_tag').style.display = 'none';
                }
            }

            // Ép hệ thống vẽ lại giỏ hàng của tab vừa bấm chuyển
            renderCartUI(ordersData[activeTabId].cart, ordersData[activeTabId].summary);
        }

        function closeOrderTab(tabId) {
            if (confirm(`Bạn muốn đóng Đơn ${tabId}? Giỏ hàng của đơn này sẽ bị xóa.`)) {
                delete ordersData[tabId];
                // Nếu đóng đúng cái tab đang active thì chuyển tự động sang tab đầu tiên còn lại
                if (activeTabId == tabId) {
                    activeTabId = Object.keys(ordersData)[0];
                }
                renderTabsBar();
                switchOrderTab(activeTabId);
            }
        }

        // Khởi tạo thanh tab lúc vừa mở trang
        document.addEventListener('DOMContentLoaded', () => {
            renderTabsBar();
            triggerCalculation();
        });

        // ==========================================
        // 2. TÌM KIẾM SẢN PHẨM LIVE SEARCH F3
        // ==========================================
        const searchInput = document.getElementById('product_search');
        let searchDropdown = document.createElement('div');
        searchDropdown.style.cssText = 'position:absolute; width:100%; background:#fff; border:1px solid #dfe3e8; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-height:300px; overflow-y:auto; display:none; z-index:999; top:40px; color:#333; border-radius:4px;';
        searchInput.parentNode.appendChild(searchDropdown);

        searchInput.addEventListener('input', function() {
            let keyword = this.value.toLowerCase().trim();
            searchDropdown.innerHTML = '';
            if (keyword.length === 0) {
                searchDropdown.style.display = 'none';
                return;
            }

            let results = PRODUCTS.filter(p => p.product_name.toLowerCase().includes(keyword) || (p.sku && p.sku.toLowerCase().includes(keyword)));

            if (results.length > 0) {
                searchDropdown.style.display = 'block';
                results.forEach(p => {
                    let item = document.createElement('div');
                    item.style.cssText = 'padding:12px 15px; border-bottom:1px solid #f4f6f8; cursor:pointer; display:flex; justify-content:space-between; align-items:center;';
                    item.innerHTML = `
                    <div><div style="font-weight:600; font-size:14px;">${p.product_name}</div><div style="font-size:12px; color:#8c98a4;">${p.sku || 'N/A'}</div></div>
                    <div style="color:#0088ff; font-weight:600;">${new Intl.NumberFormat('vi-VN').format(p.price)} ₫</div>
                `;
                    item.onmouseenter = () => item.style.background = '#f4f6f8';
                    item.onmouseleave = () => item.style.background = '#fff';
                    item.onclick = () => {
                        addToCart(p);
                        searchInput.value = '';
                        searchDropdown.style.display = 'none';
                    };
                    searchDropdown.appendChild(item);
                });
            }
        });

        window.addEventListener('keydown', function(e) {
            if (e.key === 'F3') {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'F9') {
                e.preventDefault();
                document.getElementById('btn_checkout').click();
            }
            if (e.key === 'F2') {
                e.preventDefault();
                addCustomProduct();
            }
        });

        // Hộp thả ẩn khi bấm ra ngoài
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) searchDropdown.style.display = 'none';
        });

        // ==========================================
        // 3. LOGIC GIỎ HÀNG THÔNG MINH CHO TỪNG TAB
        // ==========================================
        function addToCart(product) {
            let currentCart = ordersData[activeTabId].cart;
            let isSeparate = document.getElementById('cb_separate_line').checked;

            if (isSeparate) {
                // NẾU TÍCH TÁCH DÒNG: Luôn đẩy thành 1 dòng mới tinh
                currentCart.push({
                    id: product.id,
                    name: product.product_name,
                    sku: product.sku,
                    price: parseFloat(product.price),
                    qty: 1
                });
            } else {
                // NẾU GỘP DÒNG (Mặc định): Cộng dồn số lượng nếu trùng ID
                let existingItem = currentCart.find(i => i.id === product.id);
                if (existingItem) existingItem.qty += 1;
                else currentCart.push({
                    id: product.id,
                    name: product.product_name,
                    sku: product.sku,
                    price: parseFloat(product.price),
                    qty: 1
                });
            }
            triggerCalculation();
        }

        // HÀM: THÊM SẢN PHẨM TÙY CHỈNH / DỊCH VỤ (Không có trong DB)
        function addCustomProduct() {
            let name = prompt("Nhập tên Dịch vụ / Sản phẩm tùy chỉnh:");
            if (!name || name.trim() === '') return;

            let priceStr = prompt("Nhập đơn giá (VNĐ):");
            if (!priceStr) return;

            let price = parseFloat(priceStr.replace(/[^\d]/g, ''));
            if (isNaN(price)) {
                alert("Đơn giá không hợp lệ!");
                return;
            }

            let customItem = {
                id: 'CUSTOM_' + Date.now(), // Sinh ID ảo để hệ thống không đi tìm trong kho
                name: name.trim(),
                sku: 'DỊCH VỤ',
                price: price,
                qty: 1
            };

            ordersData[activeTabId].cart.push(customItem);
            triggerCalculation();
        }

        function updateQty(index, newQty) {
            let qty = parseInt(newQty);
            if (qty <= 0 || isNaN(qty)) ordersData[activeTabId].cart.splice(index, 1);
            else ordersData[activeTabId].cart[index].qty = qty;
            triggerCalculation();
        }

        function triggerCalculation() {
            let currentCart = ordersData[activeTabId].cart;
            if (currentCart.length === 0) {
                ordersData[activeTabId].summary = {
                    total_product_discount: 0,
                    total_order_discount: 0,
                    grand_total: 0
                };
                renderCartUI([], ordersData[activeTabId].summary);
                renderTabsBar();
                return;
            }

            fetch('index.php?action=calculate_api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_items: currentCart,
                        shipping_fee: 0,
                        promo_code: ordersData[activeTabId].appliedPromoCode
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        ordersData[activeTabId].cart = res.data.cart_items; // đồng bộ giá final_price xịn
                        ordersData[activeTabId].summary = res.data.summary;
                        renderCartUI(ordersData[activeTabId].cart, res.data.summary);
                        renderTabsBar(); // vẽ lại tab để cập nhật số lượng badge
                    }
                });
        }

        function renderCartUI(final_cart_items, summary) {
            const tbody = document.getElementById('cart_body');
            tbody.innerHTML = '';
            let subtotal_p0 = 0;
            let total_qty = 0;

            if (final_cart_items.length === 0) {
                tbody.innerHTML = `<div style="text-align:center; margin-top:100px; color:#8c98a4;"><h2 style="font-size:40px; margin-bottom:10px;">🛒</h2><p>Đơn ${activeTabId} đang trống. Vui lòng quét mã vạch hoặc nhấn F3.</p></div>`;
            } else {
                final_cart_items.forEach((item, index) => {
                    subtotal_p0 += item.price * item.qty;
                    total_qty += item.qty;

                    let isGift = item.final_price == 0 ? '<span style="background:#ffea8a; color:#8a6100; font-size:11px; padding:2px 6px; border-radius:4px; margin-left:8px;">🎁 Quà tặng</span>' : '';
                    let originalPrice = (item.final_price < item.price) ? `<div style="text-decoration:line-through; color:#8c98a4; font-size:12px;">${new Intl.NumberFormat('vi-VN').format(item.price)}</div>` : '';

                    tbody.innerHTML += `
                    <div class="cart-item">
                        <div class="item-stt">${index + 1}</div>
                        <div class="item-info">
                            <div style="font-weight:600; color:#212b36; font-size:14px;">${item.name} ${isGift}</div>
                            <div style="font-size:12px; color:#8c98a4;">Mã: ${item.sku || '---'}</div>
                        </div>
                        <div class="item-qty" style="display:flex; align-items:center; gap:3px;">
                            <button onclick="updateQty(${index}, ${item.qty - 1})" style="width:24px; height:24px; cursor:pointer; background:#f4f6f8; border:1px solid #c4cdd5; border-radius:4px;">-</button>
                            <input type="number" value="${item.qty}" onchange="updateQty(${index}, this.value)" style="width:40px; text-align:center; border:1px solid #c4cdd5; padding:4px 0; border-radius:4px;">
                            <button onclick="updateQty(${index}, ${item.qty + 1})" style="width:24px; height:24px; cursor:pointer; background:#f4f6f8; border:1px solid #c4cdd5; border-radius:4px;">+</button>
                        </div>
                        <div class="item-price">${originalPrice} ${new Intl.NumberFormat('vi-VN').format(item.final_price)} ₫</div>
                        <div class="item-total">${new Intl.NumberFormat('vi-VN').format(item.line_total)} ₫</div>
                        <div class="item-del" onclick="updateQty(${index}, 0)">🗑️</div>
                    </div>
                `;
                });
            }

            document.getElementById('txt_total_qty').innerText = `(${total_qty})`;
            document.getElementById('txt_subtotal').innerText = new Intl.NumberFormat('vi-VN').format(subtotal_p0) + ' ₫';

            let totalDiscount = summary.total_product_discount + summary.total_order_discount;
            document.getElementById('txt_discount').innerText = '-' + new Intl.NumberFormat('vi-VN').format(totalDiscount) + ' ₫';
            document.getElementById('txt_grand_total').innerText = new Intl.NumberFormat('vi-VN').format(summary.grand_total) + ' ₫';
        }

        // ==========================================
        // 4. COUPON & THANH TOÁN THEO TAB ACTIVE
        // ==========================================
        let btnApply = document.getElementById('btn_apply_promo');
        if (btnApply) {
            btnApply.onclick = function() {
                let code = document.getElementById('promo_code_input').value.trim().toUpperCase();
                if (code === '') return;
                ordersData[activeTabId].appliedPromoCode = code;
                document.getElementById('applied_promo_tag').style.display = 'block';
                document.getElementById('current_code_text').innerText = code;
                document.getElementById('promo_code_input').value = '';
                triggerCalculation();
            };
        }

        function removePromoCode() {
            ordersData[activeTabId].appliedPromoCode = '';
            document.getElementById('applied_promo_tag').style.display = 'none';
            triggerCalculation();
        }

        // Nút thanh toán F9
        document.getElementById('btn_checkout').onclick = function() {
            let currentCart = ordersData[activeTabId].cart;
            let currentSummary = ordersData[activeTabId].summary;

            if (currentCart.length === 0) {
                alert('Giỏ hàng trống!');
                return;
            }

            this.innerText = 'ĐANG XỬ LÝ...';
            this.disabled = true;

            fetch('index.php?action=store_order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_items: currentCart,
                        summary: currentSummary,
                        payment_status: 'paid',
                        payment_method: 'cash'
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert(`🎉 Đơn ${activeTabId} thanh toán thành công!\nMã đơn hệ thống sinh ra: ${res.order_code}`);

                        // Nếu chỉ có 1 tab độc nhất thì reset trắng tab đó
                        if (Object.keys(ordersData).length === 1) {
                            ordersData[activeTabId] = {
                                cart: [],
                                appliedPromoCode: '',
                                summary: {
                                    total_product_discount: 0,
                                    total_order_discount: 0,
                                    grand_total: 0
                                }
                            };
                            switchOrderTab(activeTabId);
                        } else {
                            // Nếu có nhiều tab, thanh toán xong đơn nào thì xóa hẳn tab đơn đó đi
                            let tabToClose = activeTabId;
                            delete ordersData[tabToClose];
                            activeTabId = Object.keys(ordersData)[0];
                            renderTabsBar();
                            switchOrderTab(activeTabId);
                        }
                    } else {
                        alert('Lỗi: ' + res.msg);
                        this.innerText = 'THANH TOÁN (F9)';
                        this.disabled = false;
                    }
                });
        };
    </script>
</body>

</html>
