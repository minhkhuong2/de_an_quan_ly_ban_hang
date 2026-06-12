<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    /* CSS CHUẨN SAPO OMNIAI V3 CHO MÀN HÌNH POS */
    .pos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .pos-title {
        font-size: 22px;
        font-weight: 700;
        color: #212b36;
    }

    .pos-layout {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .pos-left {
        flex: 0 0 68%;
    }

    .pos-right {
        flex: 1;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dfe3e8;
    }

    .v3-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Thanh tìm kiếm F3 */
    .search-bar-container {
        position: relative;
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 12px 15px 12px 40px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 15px;
        box-sizing: border-box;
    }

    .search-input:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 12px;
        font-size: 16px;
        color: #8c98a4;
    }

    /* Bảng giỏ hàng */
    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-table th {
        text-align: left;
        padding: 10px;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #dfe3e8;
    }

    .cart-table td {
        padding: 15px 10px;
        border-bottom: 1px solid #dfe3e8;
        vertical-align: middle;
        font-size: 14px;
        color: #212b36;
    }

    .qty-input {
        width: 60px;
        padding: 6px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        text-align: center;
    }

    /* Khối tóm tắt đơn hàng (Summary) */
    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #212b36;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #dfe3e8;
        font-size: 18px;
        font-weight: 700;
        color: #d82c0d;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        width: 100%;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        color: #212b36;
        cursor: pointer;
        width: 100%;
    }

    .action-row {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
</style>

<div class="pos-header">
    <div class="pos-title">Tạo đơn hàng</div>
</div>

<div class="pos-layout">
    <div class="pos-left">
        <div class="v3-card">
            <div class="v3-card-title">Sản phẩm</div>

            <div class="search-bar-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="product_search" class="search-input" placeholder="Nhấn F3 để tìm kiếm sản phẩm hoặc quét mã vạch...">
            </div>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Sản phẩm</th>
                        <th style="width: 15%; text-align: center;">Số lượng</th>
                        <th style="width: 15%; text-align: right;">Đơn giá</th>
                        <th style="width: 15%; text-align: right;">Thành tiền</th>
                        <th style="width: 5%; text-align: center;"></th>
                    </tr>
                </thead>
                <tbody id="cart_body">
                    <tr>
                        <td colspan="5" style="text-align: center; color: #8c98a4; padding: 40px 0;">
                            Chưa có sản phẩm nào trong đơn hàng.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="v3-card">
            <div class="v3-card-title">Thanh toán</div>
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; color: #637381; margin-bottom: 8px; display:block;">Xác nhận thanh toán</label>
                    <select class="form-control" id="payment_status">
                        <option value="pending">Thanh toán sau (COD)</option>
                        <option value="paid">Đã thanh toán</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; color: #637381; margin-bottom: 8px; display:block;">Hình thức</label>
                    <select class="form-control" id="payment_method">
                        <option value="cash">Tiền mặt</option>
                        <option value="transfer">Chuyển khoản</option>
                        <option value="card">Quẹt thẻ</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="pos-right">

        <div class="v3-card">
            <div class="v3-card-title">Khách hàng</div>
            <div class="search-bar-container" style="margin-bottom: 0;">
                <span class="search-icon">👤</span>
                <input type="text" class="search-input" placeholder="Tìm khách hàng (SĐT, Tên)...">
            </div>
        </div>

        <div class="v3-card">
            <div class="v3-card-title">
                Khuyến mại & Mã giảm giá
                <a href="#" style="font-size: 13px; font-weight: normal; color: #0088ff; text-decoration: none;">Chọn mã</a>
            </div>
            <div style="display: flex; gap: 10px;">
                <input type="text" class="form-control" placeholder="Nhập mã khuyến mại..." style="text-transform: uppercase;">
                <button class="btn-outline" style="width: auto;">Áp dụng</button>
            </div>
        </div>

        <div class="v3-card" style="background: #f4f6f8; border: none;">
            <div class="v3-card-title">Tóm tắt đơn hàng</div>

            <div class="summary-line">
                <span>Tổng tiền hàng (0 sản phẩm)</span>
                <span id="txt_subtotal">0 ₫</span>
            </div>
            <div class="summary-line" style="color: #108043;">
                <span>Chiết khấu đơn hàng</span>
                <span id="txt_discount">0 ₫</span>
            </div>
            <div class="summary-line">
                <span>Phí giao hàng</span>
                <span id="txt_shipping">0 ₫</span>
            </div>

            <div class="summary-total">
                <span>Khách phải trả</span>
                <span id="txt_grand_total">0 ₫</span>
            </div>
        </div>

        <div class="action-row">
            <button class="btn-outline">Lưu nháp</button>
            <button class="btn-primary">Tạo đơn hàng</button>
        </div>
        <div style="margin-top: 10px;">
            <button class="btn-primary" style="background: #108043;">Tạo đơn và Giao hàng</button>
        </div>

    </div>
</div>

<script>
    // Dữ liệu từ PHP
    const PRODUCTS = <?php echo $products_json; ?>;
    let cart = [];
    let shippingFee = 0;

    // 1. TÍNH NĂNG TÌM KIẾM SẢN PHẨM NHƯ SAPO
    const searchInput = document.getElementById('product_search');
    let searchDropdown = document.createElement('div');
    searchDropdown.style.cssText = 'position:absolute; width:100%; background:#fff; border:1px solid #dfe3e8; border-radius:4px; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-height:250px; overflow-y:auto; display:none; z-index:100;';
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
                item.style.cssText = 'padding:10px 15px; border-bottom:1px solid #f4f6f8; cursor:pointer; display:flex; justify-content:space-between; align-items:center;';
                item.innerHTML = `
                    <div><div style="font-weight:500; font-size:14px; color:#212b36;">${p.product_name}</div><div style="font-size:12px; color:#8c98a4;">${p.sku || 'N/A'}</div></div>
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
        } else {
            searchDropdown.style.display = 'block';
            searchDropdown.innerHTML = '<div style="padding:15px; text-align:center; color:#8c98a4; font-size:14px;">Không tìm thấy sản phẩm</div>';
        }
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) searchDropdown.style.display = 'none';
    });

    // Bắt sự kiện F3
    window.addEventListener('keydown', function(e) {
        if (e.key === 'F3') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    // 2. LOGIC GIỎ HÀNG
    function addToCart(product) {
        let existingItem = cart.find(i => i.id === product.id);
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.product_name,
                sku: product.sku,
                price: parseFloat(product.price),
                qty: 1
            });
        }
        triggerCalculation(); // Gọi hàm tính tiền
    }

    function updateQty(index, newQty) {
        let qty = parseInt(newQty);
        if (qty <= 0 || isNaN(qty)) {
            cart.splice(index, 1);
        } else {
            cart[index].qty = qty;
        }
        triggerCalculation();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        triggerCalculation();
    }

    // 3. KẾT NỐI API TÍNH TOÁN CỦA SAPO OMNIAI V3 (AJAX)
    function triggerCalculation() {
        if (cart.length === 0) {
            renderCartUI([], {
                total_product_discount: 0,
                total_order_discount: 0,
                final_shipping_fee: 0,
                grand_total: 0
            });
            return;
        }

        // Định tuyến URL theo MVC của bạn
        let apiUrl = 'index.php?action=calculate_api';
        // Nếu file index.php của bạn dùng ?action= thay vì ?url= thì đổi thành: 'index.php?action=calculate_api'

        fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_items: cart,
                    shipping_fee: shippingFee
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    renderCartUI(res.data.cart_items, res.data.summary);
                }
            })
            .catch(error => console.error('Lỗi tính toán:', error));
    }

    // 4. VẼ GIAO DIỆN GIỎ HÀNG VÀ TÓM TẮT
    function renderCartUI(final_cart_items, summary) {
        const tbody = document.getElementById('cart_body');
        tbody.innerHTML = '';

        let subtotal_p0 = 0; // Tiền hàng chưa giảm

        if (final_cart_items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #8c98a4; padding: 40px 0;">Chưa có sản phẩm nào trong đơn hàng.</td></tr>';
        } else {
            final_cart_items.forEach((item, index) => {
                subtotal_p0 += item.price * item.qty;

                // Đánh dấu nếu là hàng tặng (Mua X Tặng Y)
                let isGift = item.final_price == 0 ? '<span style="background:#ffea8a; color:#8a6100; font-size:11px; padding:2px 6px; border-radius:4px; margin-left:8px;">🎁 Quà tặng</span>' : '';
                let originalPriceStrike = (item.final_price < item.price) ? `<div style="text-decoration:line-through; color:#8c98a4; font-size:12px;">${new Intl.NumberFormat('vi-VN').format(item.price)} ₫</div>` : '';

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div style="font-weight:600; color:#0088ff;">${item.name} ${isGift}</div>
                            <div style="font-size:12px; color:#637381;">Mã: ${item.sku || '---'}</div>
                        </td>
                        <td style="text-align:center;">
                            <input type="number" class="qty-input" value="${item.qty}" onchange="updateQty(${index}, this.value)">
                        </td>
                        <td style="text-align:right;">
                            ${originalPriceStrike}
                            <div style="font-weight:500;">${new Intl.NumberFormat('vi-VN').format(item.final_price)} ₫</div>
                        </td>
                        <td style="text-align:right; font-weight:600;">${new Intl.NumberFormat('vi-VN').format(item.line_total)} ₫</td>
                        <td style="text-align:center;">
                            <button onclick="removeItem(${index})" style="background:none; border:none; color:#d82c0d; cursor:pointer; font-size:16px;">×</button>
                        </td>
                    </tr>
                `;
            });
        }

        // Cập nhật Tóm tắt đơn hàng
        document.getElementById('txt_subtotal').innerText = new Intl.NumberFormat('vi-VN').format(subtotal_p0) + ' ₫';

        let totalDiscount = summary.total_product_discount + summary.total_order_discount;
        document.getElementById('txt_discount').innerText = '-' + new Intl.NumberFormat('vi-VN').format(totalDiscount) + ' ₫';

        document.getElementById('txt_shipping').innerText = new Intl.NumberFormat('vi-VN').format(summary.final_shipping_fee) + ' ₫';
        document.getElementById('txt_grand_total').innerText = new Intl.NumberFormat('vi-VN').format(summary.grand_total) + ' ₫';

        // Cập nhật số lượng sản phẩm trên tiêu đề
        document.querySelector('.summary-line span').innerText = `Tổng tiền hàng (${final_cart_items.reduce((sum, i) => sum + i.qty, 0)} sản phẩm)`;
    }
    // 5. CHỨC NĂNG LƯU ĐƠN HÀNG (SUBMIT TO DATABASE)
    function submitOrder() {
        if (cart.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm để tạo đơn!');
            return;
        }

        // Lấy thông tin thanh toán từ giao diện
        let paymentStatus = document.getElementById('payment_status').value;
        let paymentMethod = document.getElementById('payment_method').value;

        // Lấy lại cái summary hiện tại trên màn hình
        let summary = {
            total_product_discount: parseFloat(document.getElementById('txt_discount').innerText.replace(/[^\d]/g, '')) || 0,
            total_order_discount: 0,
            final_shipping_fee: parseFloat(document.getElementById('txt_shipping').innerText.replace(/[^\d]/g, '')) || 0,
            grand_total: parseFloat(document.getElementById('txt_grand_total').innerText.replace(/[^\d]/g, '')) || 0
        };

        let btn = document.querySelector('.btn-primary');
        btn.innerText = 'Đang tạo đơn... ⏳';
        btn.disabled = true;

        // Gửi dữ liệu về backend
        fetch('index.php?action=store_order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_items: cart,
                    summary: summary,
                    payment_status: paymentStatus,
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    alert('🎉 ' + res.msg + ' Mã đơn: ' + res.order_code);
                    window.location.href = 'index.php?action=create_order'; // Reset lại màn hình
                } else {
                    alert('❌ Lỗi: ' + res.msg);
                    btn.innerText = 'Tạo đơn hàng';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối máy chủ!');
                btn.innerText = 'Tạo đơn hàng';
                btn.disabled = false;
            });
    }

    // Gắn sự kiện vào nút "Tạo đơn hàng" trên giao diện
    document.addEventListener('DOMContentLoaded', () => {
        let btnSubmit = document.querySelectorAll('.action-row .btn-primary')[0];
        if (btnSubmit) {
            btnSubmit.onclick = submitOrder;
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
