<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>AAKC POS - Bán hàng tại quầy</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Thanh Header POS */
        .pos-header {
            background: #001529;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pos-header .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .pos-header a {
            color: #1890ff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .pos-header a:hover {
            text-decoration: underline;
        }

        /* Khu vực chính */
        .pos-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Cột trái (Giỏ hàng & Quét mã) */
        .pos-left {
            flex: 7;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #dfe3e8;
            background: #fff;
        }

        .search-bar {
            padding: 15px;
            border-bottom: 1px solid #dfe3e8;
            background: #fafbfc;
        }

        .search-bar input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
            outline: none;
        }

        .search-bar input:focus {
            border-color: #0088ff;
        }

        .cart-area {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .cart-table th {
            color: #637381;
            font-weight: 500;
            font-size: 14px;
        }

        /* Cột phải (Khách hàng & Thanh toán) */
        .pos-right {
            flex: 3;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .customer-info {
            padding: 20px;
            border-bottom: 1px solid #dfe3e8;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
        }

        .payment-summary {
            padding: 20px;
            flex: 1;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 15px;
            color: #212b36;
        }

        .summary-total {
            font-size: 24px;
            font-weight: bold;
            color: #0088ff;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px dashed #dfe3e8;
        }

        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: #0088ff;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-checkout:hover {
            background: #0070d2;
        }
    </style>
</head>

<body>

    <div class="pos-header">
        <div class="logo">🛒 AAKC POS</div>
        <div style="display: flex; gap: 20px; align-items: center;">
            <span style="font-size: 14px; color: #a6adb4;">Nhân viên: Admin</span>
            <a href="index.php?action=dashboard">← Thoát về trang Quản trị</a>
        </div>
    </div>

    <div class="pos-container">
        <div class="pos-left">
            <div class="search-bar">
                <input type="text" placeholder="🔍 Quét mã vạch hoặc nhập mã IMEI/Serial thiết bị vào đây (Enter)..." autofocus>
            </div>
            <div class="cart-area">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã IMEI / Serial</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><strong>359123456789012</strong></td>
                            <td>iPhone 15 Pro Max</td>
                            <td>1</td>
                            <td style="text-align: right;">30.000.000 ₫</td>
                            <td style="text-align: center; color: red; cursor: pointer;">🗑️</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pos-right">
            <div class="customer-info">
                <h3 style="font-size: 16px; margin-bottom: 10px;">👤 Thông tin khách hàng (Bảo hành)</h3>
                <input type="text" id="customer_phone" class="form-control" placeholder="Tìm hoặc nhập số điện thoại khách hàng...">
                <input type="text" id="customer_name" class="form-control" placeholder="Tên khách hàng">
            </div>

            <div class="payment-summary">
                <div class="summary-row">
                    <span>Tổng tiền hàng (1 sản phẩm)</span>
                    <span>30.000.000 ₫</span>
                </div>
                <div class="summary-row">
                    <span>Chiết khấu</span>
                    <span>0 ₫</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Khách cần trả</span>
                    <span>30.000.000 ₫</span>
                </div>
            </div>

            <button class="btn-checkout" onclick="processCheckout()">THANH TOÁN (F9)</button>
        </div>
    </div>

</body>
<script>
    const searchInput = document.querySelector('.search-bar input');
    const cartBody = document.querySelector('.cart-table tbody');
    const totalAmountText = document.querySelectorAll('.summary-row span:nth-child(2)'); // Các dòng hiển thị tiền

    let cartItems = []; // Mảng lưu các máy đang có trong giỏ

    // Lắng nghe sự kiện gõ phím
    searchInput.addEventListener('keypress', function(e) {
        // 13 là mã của phím Enter (Máy quét mã vạch khi tít xong cũng tự động gửi phím Enter)
        if (e.key === 'Enter') {
            let code = this.value.trim();
            if (code === '') return;

            // Gọi API ngầm lên Server
            fetch(`index.php?action=scan_imei&code=${code}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        addToCart(result.data);
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => console.error('Error:', error));

            // Xóa trắng ô nhập liệu để quét mã tiếp theo
            this.value = '';
        }
    });

    function addToCart(product) {
        // Kiểm tra xem máy này đã quét chưa
        if (cartItems.find(item => item.id === product.id)) {
            alert('Máy này đã có trong giỏ hàng rồi!');
            return;
        }

        cartItems.push(product);
        renderCart();
    }

    function removeFromCart(id) {
        cartItems = cartItems.filter(item => item.id !== id);
        renderCart();
    }

    function renderCart() {
        cartBody.innerHTML = '';
        let total = 0;

        cartItems.forEach((item, index) => {
            total += parseFloat(item.base_price);

            // Format tiền tệ VNĐ
            let priceFormatted = new Intl.NumberFormat('vi-VN').format(item.base_price) + ' ₫';

            let tr = document.createElement('tr');
            tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td><strong>${item.imei_code}</strong></td>
                    <td>${item.product_name}</td>
                    <td>1</td>
                    <td style="text-align: right;">${priceFormatted}</td>
                    <td style="text-align: center; color: red; cursor: pointer;" onclick="removeFromCart(${item.id})">🗑️</td>
                `;
            cartBody.appendChild(tr);
        });

        // Cập nhật tổng tiền
        let totalFormatted = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        totalAmountText[0].innerText = totalFormatted; // Tổng tiền hàng
        totalAmountText[2].innerText = totalFormatted; // Khách cần trả
    }

    // Khởi tạo giỏ hàng trống lúc mới vào trang
    renderCart();

    function processCheckout() {
        if (cartItems.length === 0) {
            alert("Giỏ hàng đang trống! Vui lòng quét mã IMEI.");
            return;
        }

        let c_phone = document.getElementById('customer_phone').value.trim();
        let c_name = document.getElementById('customer_name').value.trim();

        if (c_phone === '' || c_name === '') {
            alert("Vui lòng nhập Tên và Số điện thoại khách hàng để lưu bảo hành!");
            return;
        }

        // Tính lại tổng tiền
        let totalAmount = cartItems.reduce((sum, item) => sum + parseFloat(item.base_price), 0);

        let orderData = {
            customer_name: c_name,
            customer_phone: c_phone,
            total_amount: totalAmount,
            cart: cartItems
        };

        // Gọi API lưu Hóa đơn
        fetch('index.php?action=checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    // Dọn sạch giỏ hàng và ô nhập liệu
                    cartItems = [];
                    renderCart();
                    document.getElementById('customer_phone').value = '';
                    document.getElementById('customer_name').value = '';
                } else {
                    alert(result.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

</html>
