<?php

/** @var string $products_json */
/** @var string $customers_json */
/** @var array $settings_db */
$is_two_step = (isset($settings_db['pos_payment_steps']) && $settings_db['pos_payment_steps'] == '2') ? true : false;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sapo OmniAI - POS Bán hàng</title>
    <link rel="stylesheet" href="css/pos.css?v=1.2">
    <style>
        /* CSS Bổ sung cho các Modal và Tính năng mới */
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .pay-method-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid #c4cdd5;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            font-weight: 500;
            color: #212b36;
        }

        .pay-method-btn.active {
            border-color: #0088ff;
            background: #e5f0ff;
            color: #0088ff;
            font-weight: 600;
        }

        .suggest-money {
            display: flex;
            gap: 5px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .suggest-btn {
            padding: 4px 8px;
            background: #f4f6f8;
            border: 1px solid #dfe3e8;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            color: #0088ff;
        }

        .suggest-btn:hover {
            background: #e5f0ff;
        }

        .input-money {
            width: 100%;
            text-align: right;
            padding: 8px;
            font-size: 16px;
            font-weight: bold;
            color: #0088ff;
            border: 1px solid #c4cdd5;
            border-radius: 4px;
            outline: none;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #fff;
            width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-outline {
            background: #fff;
            border: 1px solid #c4cdd5;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background: #0088ff;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="pos-topbar" style="background: #0088ff; color: #fff; display: flex; align-items: center; padding: 0 20px; height: 55px; gap: 15px;">
        <a href="index.php?action=dashboard" style="color: #fff; text-decoration: none; font-weight: 600;">← Admin</a>

        <div id="network_status" onclick="openSyncModal()" style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px;">
            <span id="wifi_icon">📶</span> <span id="network_text">Online</span>
            <span id="unsynced_badge" style="background: #d82c0d; color: #fff; padding: 2px 6px; border-radius: 10px; font-size: 12px; font-weight: bold; display: none;">0</span>
        </div>

        <a href="index.php?action=end_of_day_report" target="_blank" style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 4px; color: #fff; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px;">
            📊 Báo cáo
        </a>

        <div class="order-tabs-container" id="order_tabs_bar" style="display: flex; gap: 5px; height: 100%; align-items: flex-end; margin-left: 10px;"></div>
        <button onclick="createNewOrderTab()" style="background: rgba(255,255,255,0.3); color: #fff; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer;">+</button>

        <div style="display: flex; align-items: center; gap: 10px; margin-left: auto; width: 400px; position: relative;">
            <div style="position: relative; flex: 1;">
                <span style="position: absolute; left: 12px; top: 8px; color: #8c98a4;">🔍</span>
                <input type="text" id="product_search" placeholder="F3 - Tìm SP hoặc quét mã..." style="width: 100%; padding: 8px 15px 8px 35px; border-radius: 20px; border: none; outline: none;">
            </div>
            <button id="btn_toggle_scale" title="Bật/Tắt Cân điện tử" style="width: 36px; height: 36px; border-radius: 4px; border: 1px solid #c4cdd5; background: #f4f6f8; cursor: pointer; font-size: 18px;">⚖️</button>
        </div>
    </div>

    <div class="pos-main" style="display: flex; height: calc(100vh - 55px);">
        <div class="pos-left" style="flex: 0 0 65%; background: #fff; display: flex; flex-direction: column; border-right: 1px solid #dfe3e8;">
            <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="cb_separate_line" style="width: 16px; height: 16px;"> Tách dòng sản phẩm
                </label>
                <button onclick="addCustomProduct()" style="background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600;">+ SP tùy chỉnh (F2)</button>
            </div>
            <div style="display: flex; font-size: 13px; font-weight: 600; color: #637381; padding: 12px 20px; border-bottom: 1px solid #dfe3e8; background: #fafbfc;">
                <div style="width: 30px;">#</div>
                <div style="flex: 1;">Sản phẩm</div>
                <div style="width: 100px; text-align: center;">SL</div>
                <div style="width: 130px; text-align: right;">Đơn giá</div>
                <div style="width: 130px; text-align: right;">Thành tiền</div>
                <div style="width: 40px;"></div>
            </div>
            <div id="cart_body" style="flex: 1; overflow-y: auto;"></div>
        </div>

        <div class="pos-right" style="flex: 1; background: #fff; display: flex; flex-direction: column; padding: 20px;">
            <div id="customer_search_mode" style="display: flex; gap: 10px; margin-bottom: 15px; position: relative;">
                <input type="text" id="customer_search" placeholder="F4 - Tìm khách hàng..." style="flex: 1; padding: 10px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none;">
                <button onclick="document.getElementById('add_customer_modal').style.display='flex'" style="width: 38px; height: 38px; background: #fff; border: 1px solid #c4cdd5; border-radius: 4px; cursor: pointer;">+</button>
            </div>
            <div id="customer_selected_mode" style="display: none; background: #f4f6f8; padding: 12px; border-radius: 4px; border: 1px solid #dfe3e8; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="background: #0088ff; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">👤</div>
                    <div>
                        <div style="font-weight: 600;" id="txt_cust_name"></div>
                        <div style="font-size: 12px;" id="txt_cust_phone"></div>
                    </div>
                </div>
                <div onclick="removeCustomer()" style="color: #d82c0d; cursor: pointer;">🗑️</div>
            </div>

            <?php if (isset($settings_db['pos_use_promo_code']) && $settings_db['pos_use_promo_code'] == '1'): ?>
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <input type="text" id="promo_code_input" placeholder="Mã khuyến mại..." style="flex: 1; padding: 10px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none;">
                    <button id="btn_apply_promo" style="background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 0 15px; border-radius: 4px; font-weight: 600; cursor: pointer;">ÁP DỤNG</button>
                </div>
                <div id="applied_promo_tag" style="display:none; color:#108043; font-size:14px; margin-bottom:15px;">
                    ✅ Mã: <span id="current_code_text"></span> <span style="color:#d82c0d; cursor:pointer; float:right;" onclick="removePromoCode()">[Xóa]</span>
                </div>
            <?php endif; ?>

            <div style="flex: 1; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;"><span>Tổng tiền hàng <span id="txt_total_qty" style="color:#8c98a4;">(0)</span></span><span id="txt_subtotal" style="font-weight: 500;">0</span></div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #108043;"><span>Giảm giá</span><span id="txt_discount">- 0</span></div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #8c98a4;"><span>Thuế VAT (10%)</span><span id="txt_tax">0</span></div>
                <div style="display: flex; justify-content: space-between; margin-top: auto; padding-top: 15px; border-top: 1px dashed #dfe3e8; font-size: 22px; font-weight: bold; color: #0088ff;"><span>KHÁCH CẦN TRẢ</span><span id="txt_grand_total">0</span></div>

                <?php if (!$is_two_step): ?>
                    <div style="margin-top: 15px; border-top: 1px dashed #dfe3e8; padding-top: 15px;">
                        <div class="payment-methods">
                            <button class="pay-method-btn active" onclick="setPaymentMethod('cash', this)">Tiền mặt</button>
                            <button class="pay-method-btn" onclick="setPaymentMethod('transfer', this)">Chuyển khoản</button>
                            <button class="pay-method-btn" onclick="setPaymentMethod('vietqr', this)">Quét VietQR</button>
                            <button class="pay-method-btn" onclick="setPaymentMethod('zalopay', this)">ZaloPay</button>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 600;">Khách thanh toán</span>
                            <div style="width: 150px;">
                                <input type="text" class="input-money" id="input_amount_given" value="0" onkeyup="formatCurrencyInput(this); calculateChange();">
                                <div class="suggest-money" id="suggest_money_box"></div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between;"><span>Tiền thừa:</span><span id="txt_change" style="font-weight: bold; color: #d82c0d;">0</span></div>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <?php if (isset($settings_db['pos_preprint_invoice']) && $settings_db['pos_preprint_invoice'] == '1'): ?>
                    <button class="btn-outline" style="flex: 0 0 30%; padding: 18px; border-radius: 6px; font-weight: 600; color: #0088ff; border-color: #0088ff;" onclick="printProvisional()">🖨️ In tạm tính</button>
                <?php endif; ?>
                <button class="btn-primary" id="btn_checkout" style="flex: 1; padding: 18px; border-radius: 6px; font-size: 18px;">THANH TOÁN (F9)</button>
            </div>
        </div>
    </div>

    <?php if ($is_two_step): ?>
        <div class="modal-overlay" id="payment_modal" style="z-index: 1000;">
            <div class="modal-content" style="width: 500px;">
                <h3>Xác nhận thanh toán</h3>
                <div style="background: #f4f6f8; padding: 15px; border-radius: 4px; text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #637381;">Khách cần trả</div>
                    <div style="font-size: 28px; font-weight: bold; color: #0088ff;" id="modal_grand_total">0 ₫</div>
                </div>
                <label style="font-weight: 600; margin-bottom: 10px; display: block;">Phương thức thanh toán</label>
                <div class="payment-methods">
                    <button class="pay-method-btn active" onclick="setPaymentMethod('cash', this)">Tiền mặt</button>
                    <button class="pay-method-btn" onclick="setPaymentMethod('transfer', this)">Chuyển khoản</button>
                    <button class="pay-method-btn" onclick="setPaymentMethod('qr', this)">Quét mã QR</button>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <span style="font-weight: 600;">Khách thanh toán:</span>
                    <input type="text" class="input-money" id="input_amount_given_modal" value="0" style="width: 200px;" onkeyup="formatCurrencyInput(this); calculateChange();">
                </div>
                <div class="suggest-money" id="suggest_money_box_modal" style="justify-content: flex-end; margin-bottom: 15px;"></div>
                <div style="display: flex; justify-content: space-between; font-size: 16px; margin-top: 10px;">
                    <span>Tiền thừa trả khách:</span>
                    <span id="txt_change_modal" style="font-weight: 700; color: #d82c0d;">0 ₫</span>
                </div>
                <div class="modal-actions">
                    <button class="btn-outline" onclick="document.getElementById('payment_modal').style.display='none'">Hủy</button>
                    <button class="btn-primary" onclick="processFinalPayment()">HOÀN TẤT (F9)</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal-overlay" id="add_customer_modal" style="z-index: 1000;">
        <div class="modal-content">
            <h3>Thêm khách hàng mới</h3>
            <div style="margin-bottom: 15px;"><label>Tên khách hàng</label><input type="text" id="new_cust_name" style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px;"></div>
            <div style="margin-bottom: 15px;"><label>Số điện thoại</label><input type="text" id="new_cust_phone" style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px;"></div>
            <div class="modal-actions">
                <button class="btn-outline" onclick="document.getElementById('add_customer_modal').style.display='none'">Hủy</button>
                <button class="btn-primary" onclick="quickAddCustomer()">Lưu khách hàng</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="qr_display_modal" style="z-index: 10000;">
        <div class="modal-content" style="width: 450px; text-align: center; padding: 20px;">
            <h3 style="color: #0088ff; margin-bottom: 5px;">MÀN HÌNH CHỜ THANH TOÁN</h3>
            <p style="color: #637381; font-size: 14px; margin-bottom: 15px;">Mã QR có hiệu lực trong: <span id="qr_timer" style="color: #d82c0d; font-weight: bold; font-size: 16px;">05:00</span></p>

            <div style="background: #f4f6f8; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: inline-block; border: 1px solid #dfe3e8;">
                <img id="vietqr_image" src="" alt="Mã QR" style="width: 250px; height: 250px; border-radius: 8px;">
            </div>

            <div style="font-size: 15px;">Khách cần trả:</div>
            <div id="qr_amount_text" style="font-size: 28px; font-weight: bold; color: #108043; margin-bottom: 15px;">0 ₫</div>

            <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
                <button class="btn-outline" style="font-size: 12px; padding: 6px 10px;" onclick="printOnlyQR()"><i class="fa-solid fa-qrcode"></i> In mã QR</button>
                <button class="btn-outline" style="font-size: 12px; padding: 6px 10px;"><i class="fa-solid fa-print"></i> In đơn hàng</button>
            </div>

            <button class="btn-primary" style="width: 100%; padding: 12px; font-size: 15px; margin-bottom: 15px;" onclick="completeQRPayment()">✅ Khách đã thanh toán thành công?</button>

            <div style="display: flex; justify-content: space-between; border-top: 1px solid #dfe3e8; padding-top: 15px;">
                <button class="btn-outline" style="color: #d82c0d; border-color: #fca5a5; padding: 6px 10px;" onclick="cancelQRPayment()">Hủy đơn hàng</button>
                <button class="btn-outline" style="padding: 6px 10px;" onclick="editOrderQR()">Sửa đơn hàng</button>
                <button class="btn-outline" style="color: #0088ff; border-color: #0088ff; padding: 6px 10px;" onclick="changePaymentMethod()">Đổi phương thức</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="sync_offline_modal" style="z-index: 9999;">
        <div class="modal-content" style="width: 600px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px; margin-bottom: 15px;">
                <h3 style="margin: 0;">Đơn chưa đồng bộ <span id="modal_unsynced_count" style="color: #d82c0d;">(0)</span></h3>
                <button style="border: none; background: transparent; font-size: 20px; cursor: pointer;" onclick="document.getElementById('sync_offline_modal').style.display='none'">×</button>
            </div>
            <div style="background: #fff8ea; color: #8a6100; padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px;">⚠️ Vui lòng không xóa Cache trình duyệt trước khi đồng bộ!</div>
            <div id="offline_orders_list" style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;"></div>
            <div class="modal-actions" style="border-top: 1px solid #dfe3e8; padding-top: 15px;">
                <button class="btn-outline" onclick="document.getElementById('sync_offline_modal').style.display='none'">Đóng</button>
                <button class="btn-primary" id="btn_sync_all" style="background: #108043;" onclick="syncAllOfflineOrders()">🔄 ĐỒNG BỘ TẤT CẢ</button>
            </div>
        </div>
    </div>

    <script>
        const PAYMENT_METHODS = <?php echo isset($payment_methods_json) ? $payment_methods_json : '[]'; ?>;
        let qrCountdownInterval;
        const PRODUCTS = <?php echo isset($products_json) ? $products_json : '[]'; ?>;
        const CUSTOMERS = <?php echo isset($customers_json) ? $customers_json : '[]'; ?>;
        const IS_TWO_STEP = <?php echo $is_two_step ? 'true' : 'false'; ?>;

        let ordersData = {
            1: {
                cart: [],
                customer: null,
                appliedPromoCode: '',
                paymentMethod: 'cash',
                amountGiven: 0,
                summary: {
                    total_product_discount: 0,
                    total_order_discount: 0,
                    tax_amount: 0,
                    grand_total: 0
                }
            }
        };
        let activeTabId = 1;
        let nextTabId = 2;

        // --- LOGIC OFFLINE & MẠNG ---
        let offlineOrders = JSON.parse(localStorage.getItem('sapo_offline_orders')) || [];
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);

        function updateNetworkStatus() {
            if (navigator.onLine) {
                document.getElementById('wifi_icon').innerText = '📶';
                document.getElementById('network_text').innerText = 'Online';
                document.getElementById('network_status').style.background = 'rgba(16, 128, 67, 0.8)';
                if (offlineOrders.length > 0) openSyncModal();
            } else {
                document.getElementById('wifi_icon').innerText = '📵';
                document.getElementById('network_text').innerText = 'Offline';
                document.getElementById('network_status').style.background = 'rgba(216, 44, 13, 0.8)';
            }
            document.getElementById('unsynced_badge').style.display = offlineOrders.length > 0 ? 'inline-block' : 'none';
            document.getElementById('unsynced_badge').innerText = offlineOrders.length;
        }

        function openSyncModal() {
            document.getElementById('sync_offline_modal').style.display = 'flex';
            document.getElementById('modal_unsynced_count').innerText = `(${offlineOrders.length})`;
            let listDiv = document.getElementById('offline_orders_list');
            listDiv.innerHTML = '';
            if (offlineOrders.length === 0) {
                listDiv.innerHTML = '<div style="text-align:center; color:#8c98a4; padding:20px;">Không có đơn kẹt.</div>';
                document.getElementById('btn_sync_all').style.display = 'none';
                return;
            }
            document.getElementById('btn_sync_all').style.display = 'inline-block';
            offlineOrders.forEach(order => {
                let err = order.error ? `<div style="color:#d82c0d; font-size:12px;">⚠️ ${order.error}</div>` : '';
                listDiv.innerHTML += `<div style="border: 1px solid #dfe3e8; padding: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; background: ${order.error ? '#fff1f0' : '#fff'};"><div><b>${order.offline_id}</b><br><span style="font-size:12px;">${order.created_at}</span>${err}</div><b style="color:#0088ff;">${formatCurrency(order.summary.grand_total)} ₫</b></div>`;
            });
        }

        async function syncAllOfflineOrders() {
            if (!navigator.onLine) {
                alert("Chưa có mạng!");
                return;
            }
            let btnSync = document.getElementById('btn_sync_all');
            btnSync.innerText = '🔄 ĐANG ĐỒNG BỘ...';
            btnSync.disabled = true;
            let newOfflineOrders = [];
            let successCount = 0;

            for (let order of offlineOrders) {
                try {
                    let res = await fetch('index.php?action=store_order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(order)
                    });
                    let data = await res.json();
                    if (data.status === 'success') successCount++;
                    else {
                        order.error = data.msg;
                        newOfflineOrders.push(order);
                    }
                } catch (err) {
                    order.error = "Lỗi kết nối!";
                    newOfflineOrders.push(order);
                }
            }
            offlineOrders = newOfflineOrders;
            localStorage.setItem('sapo_offline_orders', JSON.stringify(offlineOrders));
            updateNetworkStatus();
            openSyncModal();
            btnSync.innerText = '🔄 ĐỒNG BỘ TẤT CẢ';
            btnSync.disabled = false;
            alert(`Đã đồng bộ ${successCount} đơn!`);
        }

        // --- TIỆN ÍCH TIỀN TỆ ---
        function formatCurrency(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        function parseCurrency(str) {
            return parseFloat(str.toString().replace(/[^\d]/g, '')) || 0;
        }

        function formatCurrencyInput(input) {
            let val = parseCurrency(input.value);
            input.value = formatCurrency(val);
            ordersData[activeTabId].amountGiven = val;
        }

        // --- LOGIC TABS ---
        function renderTabsBar() {
            let tabsBar = document.getElementById('order_tabs_bar');
            if (!tabsBar) return;
            tabsBar.innerHTML = '';
            Object.keys(ordersData).forEach(tabId => {
                let active = (tabId == activeTabId) ? 'background:#fff; color:#0088ff; font-weight:bold;' : 'background:rgba(255,255,255,0.2); color:#fff;';
                let closeBtn = Object.keys(ordersData).length > 1 ? `<span onclick="event.stopPropagation(); closeOrderTab(${tabId})" style="margin-left:8px; cursor:pointer;">×</span>` : '';
                tabsBar.innerHTML += `<button onclick="switchOrderTab(${tabId})" style="border:none; padding:8px 16px; border-radius:6px 6px 0 0; cursor:pointer; font-size:13px; ${active}">📦 Đơn ${tabId} ${closeBtn}</button>`;
            });
        }

        function createNewOrderTab() {
            ordersData[nextTabId] = {
                cart: [],
                customer: null,
                appliedPromoCode: '',
                paymentMethod: 'cash',
                amountGiven: 0,
                summary: {
                    total_product_discount: 0,
                    total_order_discount: 0,
                    tax_amount: 0,
                    grand_total: 0
                }
            };
            activeTabId = nextTabId;
            nextTabId++;
            renderTabsBar();
            switchOrderTab(activeTabId);
        }

        function switchOrderTab(tabId) {
            activeTabId = tabId;
            renderTabsBar();
            renderCustomerUI();
            let promoInput = document.getElementById('promo_code_input');
            if (promoInput) {
                let currentPromo = ordersData[activeTabId].appliedPromoCode;
                document.getElementById('applied_promo_tag').style.display = currentPromo ? 'block' : 'none';
                document.getElementById('current_code_text').innerText = currentPromo || '';
            }
            renderCartUI(ordersData[activeTabId].cart, ordersData[activeTabId].summary);
            let inputGiven = document.getElementById(IS_TWO_STEP ? 'input_amount_given_modal' : 'input_amount_given');
            if (inputGiven) inputGiven.value = formatCurrency(ordersData[activeTabId].amountGiven);
            document.querySelectorAll('.pay-method-btn').forEach(btn => btn.classList.remove('active'));
            let activeMethodBtn = document.querySelector(`.pay-method-btn[onclick*="${ordersData[activeTabId].paymentMethod}"]`);
            if (activeMethodBtn) activeMethodBtn.classList.add('active');
            calculateChange();
        }

        function closeOrderTab(tabId) {
            if (confirm(`Đóng Đơn ${tabId}?`)) {
                delete ordersData[tabId];
                if (activeTabId == tabId) activeTabId = Object.keys(ordersData)[0];
                renderTabsBar();
                switchOrderTab(activeTabId);
            }
        }

        // --- CÂN ĐIỆN TỬ & TÌM KIẾM SẢN PHẨM ---
        let isScaleMode = false;
        const SCALE_PREFIX = '21';
        document.getElementById('btn_toggle_scale').onclick = function() {
            isScaleMode = !isScaleMode;
            this.style.background = isScaleMode ? '#e5f0ff' : '#f4f6f8';
            this.style.borderColor = isScaleMode ? '#0088ff' : '#c4cdd5';
            document.getElementById('product_search').placeholder = isScaleMode ? "Chế độ Cân: Quét mã 13 số..." : "F3 - Tìm SP hoặc quét mã...";
            document.getElementById('product_search').focus();
        };

        const searchInput = document.getElementById('product_search');
        let searchDropdown = document.createElement('div');
        searchDropdown.style.cssText = 'position:absolute; width:100%; background:#fff; border:1px solid #dfe3e8; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-height:300px; overflow-y:auto; display:none; z-index:999; top:40px; border-radius:4px;';
        searchInput.parentNode.appendChild(searchDropdown);

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let keyword = this.value.trim().toLowerCase();
                if (!keyword) return;

                let results = PRODUCTS.filter(p => p.product_name.toLowerCase().includes(keyword) || (p.sku && p.sku.toLowerCase().includes(keyword)));
                if (results.length === 1) {
                    addToCart(results[0]);
                    this.value = '';
                    searchDropdown.style.display = 'none';
                } else if (results.length > 1) {
                    let exactMatch = results.find(p => p.sku && p.sku.toLowerCase() === keyword);
                    if (exactMatch) {
                        addToCart(exactMatch);
                        this.value = '';
                        searchDropdown.style.display = 'none';
                    }
                }
            }
        });

        searchInput.addEventListener('input', function() {
            let keyword = this.value.trim();

            // Thuật toán quét mã cân điện tử
            if (isScaleMode && keyword.length === 13 && keyword.startsWith(SCALE_PREFIX)) {
                let skuCode = keyword.substring(2, 7);
                let weightKg = parseInt(keyword.substring(7, 12), 10) / 1000;
                let matchedProduct = PRODUCTS.find(p => p.sku === skuCode || p.id == parseInt(skuCode));
                if (matchedProduct) {
                    let currentCart = ordersData[activeTabId].cart;
                    currentCart.push({
                        id: matchedProduct.id,
                        name: matchedProduct.product_name + ' (Cân)',
                        sku: matchedProduct.sku,
                        price: parseFloat(matchedProduct.price),
                        qty: weightKg
                    });
                    triggerCalculation();
                    this.value = '';
                    searchDropdown.style.display = 'none';
                    return;
                }
            }

            keyword = keyword.toLowerCase();
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
                    item.style.cssText = 'padding:10px 15px; border-bottom:1px solid #f4f6f8; cursor:pointer; display:flex; justify-content:space-between;';
                    item.innerHTML = `<div><b>${p.product_name}</b><br><span style="font-size:12px;color:#8c98a4;">${p.sku || ''}</span></div><b style="color:#0088ff;">${formatCurrency(p.price)} ₫</b>`;
                    item.onclick = () => {
                        addToCart(p);
                        searchInput.value = '';
                        searchDropdown.style.display = 'none';
                    };
                    searchDropdown.appendChild(item);
                });
            }
        });

        function addCustomProduct() {
            let name = prompt("Tên Dịch vụ/SP tùy chỉnh (F2):");
            if (!name) return;
            let price = parseCurrency(prompt("Đơn giá (VNĐ):"));
            if (isNaN(price)) return;
            ordersData[activeTabId].cart.push({
                id: 'CUSTOM_' + Date.now(),
                name: name,
                sku: 'DỊCH VỤ',
                price: price,
                qty: 1
            });
            triggerCalculation();
        }

        // --- KHÁCH HÀNG ---
        const custSearchInput = document.getElementById('customer_search');
        let custDropdown = document.createElement('div');
        custDropdown.style.cssText = 'position:absolute; width:100%; background:#fff; border:1px solid #dfe3e8; box-shadow:0 4px 8px rgba(0,0,0,0.1); max-height:250px; overflow-y:auto; display:none; z-index:999; top:45px; border-radius:4px;';
        custSearchInput.parentNode.appendChild(custDropdown);

        function renderCustomerUI() {
            let cust = ordersData[activeTabId].customer;
            if (cust) {
                document.getElementById('customer_search_mode').style.display = 'none';
                document.getElementById('customer_selected_mode').style.display = 'flex';
                document.getElementById('txt_cust_name').innerText = cust.customer_name;
                document.getElementById('txt_cust_phone').innerText = cust.phone;
            } else {
                document.getElementById('customer_search_mode').style.display = 'flex';
                document.getElementById('customer_selected_mode').style.display = 'none';
                custSearchInput.value = '';
            }
        }

        custSearchInput.addEventListener('input', function() {
            let kw = this.value.toLowerCase().trim();
            custDropdown.innerHTML = '';
            if (kw.length === 0) {
                custDropdown.style.display = 'none';
                return;
            }
            let results = CUSTOMERS.filter(c => c.customer_name.toLowerCase().includes(kw) || c.phone.includes(kw)).slice(0, 20);
            if (results.length > 0) {
                custDropdown.style.display = 'block';
                results.forEach(c => {
                    let item = document.createElement('div');
                    item.style.cssText = 'padding:10px; border-bottom:1px solid #eee; cursor:pointer;';
                    item.innerHTML = `<b>${c.customer_name}</b> <span style="font-size:12px;color:#888;">${c.phone}</span>`;
                    item.onclick = () => {
                        ordersData[activeTabId].customer = c;
                        custDropdown.style.display = 'none';
                        renderCustomerUI();
                    };
                    custDropdown.appendChild(item);
                });
            }
        });

        function removeCustomer() {
            ordersData[activeTabId].customer = null;
            renderCustomerUI();
        }

        function quickAddCustomer() {
            let name = document.getElementById('new_cust_name').value;
            let phone = document.getElementById('new_cust_phone').value;
            fetch('index.php?action=quick_add_customer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: phone
                    })
                })
                .then(res => res.json()).then(res => {
                    if (res.status === 'success') {
                        CUSTOMERS.push(res.customer);
                        ordersData[activeTabId].customer = res.customer;
                        document.getElementById('add_customer_modal').style.display = 'none';
                        renderCustomerUI();
                    } else alert(res.msg);
                });
        }

        // --- GIỎ HÀNG & TÍNH TIỀN ---
        function addToCart(product) {
            let currentCart = ordersData[activeTabId].cart;
            let isSep = document.getElementById('cb_separate_line').checked;
            if (isSep) currentCart.push({
                id: product.id,
                name: product.product_name,
                sku: product.sku,
                price: parseFloat(product.price),
                qty: 1
            });
            else {
                let item = currentCart.find(i => i.id === product.id);
                if (item) item.qty += 1;
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

        function updateQty(index, newQty) {
            let qty = parseFloat(newQty); // Dùng parseFloat cho cân điện tử
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
                    tax_amount: 0,
                    grand_total: 0
                };
                renderCartUI([], ordersData[activeTabId].summary);
                generateSuggestMoney(0);
                calculateChange();
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
                .then(res => res.json()).then(res => {
                    if (res.status === 'success') {

                        // --- THÊM ĐOẠN HIỂN THỊ CẢNH BÁO MÃ GIẢM GIÁ Ở ĐÂY ---
                        if (res.msg && res.msg.includes("không tồn tại") || res.msg && res.msg.includes("tối thiểu")) {
                            alert('⚠️ ' + res.msg);
                            removePromoCode(); // Tự động xóa mã lỗi khỏi UI
                            return;
                        }
                        // -----------------------------------------------------

                        ordersData[activeTabId].cart = res.data.cart_items;
                        let subtotalAfterDiscount = res.data.summary.grand_total;
                        let tax = Math.round(subtotalAfterDiscount * 0.1); // VAT 10%
                        res.data.summary.tax_amount = tax;
                        res.data.summary.grand_total = subtotalAfterDiscount + tax;

                        ordersData[activeTabId].summary = res.data.summary;
                        ordersData[activeTabId].amountGiven = res.data.summary.grand_total;

                        let inputGiven = document.getElementById(IS_TWO_STEP ? 'input_amount_given_modal' : 'input_amount_given');
                        if (inputGiven) inputGiven.value = formatCurrency(res.data.summary.grand_total);

                        renderCartUI(ordersData[activeTabId].cart, res.data.summary);
                        generateSuggestMoney(res.data.summary.grand_total);
                        calculateChange();
                        renderTabsBar();
                    }
                });
        }

        function renderCartUI(items, summary) {
            const tbody = document.getElementById('cart_body');
            tbody.innerHTML = '';
            let subtotal = 0;
            let qtyTotal = 0;
            items.forEach((item, index) => {
                subtotal += item.price * item.qty;
                qtyTotal += item.qty;
                let strike = (item.final_price < item.price) ? `<del style="color:#8c98a4;font-size:12px;">${formatCurrency(item.price)}</del>` : '';
                tbody.innerHTML += `
                <div style="display:flex; align-items:center; padding:15px 20px; border-bottom:1px dashed #eee;">
                    <div style="width:30px;color:#888;">${index+1}</div>
                    <div style="flex:1;"><b>${item.name}</b><br><span style="font-size:12px;color:#888;">${item.sku}</span></div>
                    <div style="width:100px; display:flex; gap:3px;">
                        <button onclick="updateQty(${index}, ${item.qty - 1})" style="width:24px;border:1px solid #ccc;cursor:pointer;">-</button>
                        <input type="number" value="${item.qty}" onchange="updateQty(${index}, this.value)" style="width:40px;text-align:center;border:1px solid #ccc;">
                        <button onclick="updateQty(${index}, ${item.qty + 1})" style="width:24px;border:1px solid #ccc;cursor:pointer;">+</button>
                    </div>
                    <div style="width:130px; text-align:right;">${strike} ${formatCurrency(item.final_price)}</div>
                    <div style="width:130px; text-align:right; font-weight:bold; color:#0088ff;">${formatCurrency(item.line_total)}</div>
                    <div style="width:40px; text-align:right; color:red; cursor:pointer;" onclick="updateQty(${index}, 0)">🗑️</div>
                </div>`;
            });
            document.getElementById('txt_total_qty').innerText = `(${qtyTotal})`;
            document.getElementById('txt_subtotal').innerText = formatCurrency(subtotal);
            document.getElementById('txt_discount').innerText = '-' + formatCurrency((summary.total_product_discount || 0) + (summary.total_order_discount || 0));
            document.getElementById('txt_tax').innerText = formatCurrency(summary.tax_amount);
            document.getElementById('txt_grand_total').innerText = formatCurrency(summary.grand_total);
            if (document.getElementById('modal_grand_total')) document.getElementById('modal_grand_total').innerText = formatCurrency(summary.grand_total) + ' ₫';
        }

        // --- GỢI Ý TIỀN & PHƯƠNG THỨC THANH TOÁN ---
        function calculateChange() {
            let change = ordersData[activeTabId].amountGiven - ordersData[activeTabId].summary.grand_total;
            document.querySelectorAll('[id^="txt_change"]').forEach(el => el.innerText = change > 0 ? formatCurrency(change) : '0');
        }

        function generateSuggestMoney(total) {
            let box = document.getElementById(IS_TWO_STEP ? 'suggest_money_box_modal' : 'suggest_money_box');
            if (!box) return;
            box.innerHTML = '';
            let suggs = [total];
            [10000, 50000, 100000, 200000, 500000].forEach(base => {
                let s = Math.ceil(total / base) * base;
                if (s > total && !suggs.includes(s)) suggs.push(s);
            });
            suggs.slice(0, 4).forEach(amt => {
                let btn = document.createElement('div');
                btn.className = 'suggest-btn';
                btn.innerText = formatCurrency(amt);
                btn.onclick = () => {
                    let inp = document.getElementById(IS_TWO_STEP ? 'input_amount_given_modal' : 'input_amount_given');
                    if (inp) inp.value = formatCurrency(amt);
                    ordersData[activeTabId].amountGiven = amt;
                    calculateChange();
                };
                box.appendChild(btn);
            });
        }

        function setPaymentMethod(method, btnElement) {
            // 1. Cập nhật phương thức vào đơn hàng hiện tại
            ordersData[activeTabId].paymentMethod = method;

            // 2. Đổi màu nút bấm hiển thị trên giao diện
            document.querySelectorAll('.pay-method-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            // 3. NẾU LÀ VIETQR HOẶC ZALOPAY -> BUNG MODAL QR LẬP TỨC KHÔNG CẦN CHỜ BẤM F9
            if (method === 'vietqr' || method === 'zalopay') {
                let order = ordersData[activeTabId];

                // Kiểm tra giỏ hàng trống
                if (order.cart.length === 0) {
                    alert('Giỏ hàng đang trống, vui lòng chọn sản phẩm trước!');
                    // Trả về mặc định tiền mặt
                    setPaymentMethod('cash', document.querySelector(".pay-method-btn[onclick*='cash']"));
                    return;
                }

                let qrUrl = '';

                if (method === 'vietqr') {
                    // Lấy thông tin từ mảng PAYMENT_METHODS do PHP truyền xuống
                    let selectedMethod = PAYMENT_METHODS.find(m => m.code === 'vietqr');

                    // Trường hợp dự phòng nếu API fetch PAYMENT_METHODS từ DB bị chậm hoặc rỗng
                    let bank = "MB";
                    let acc = "0123456789"; // Số tài khoản dự phòng
                    let name = "BUI VAN KHUONG";

                    // Nếu có data cấu hình thật trong DB thì bốc ra dùng
                    if (selectedMethod && selectedMethod.config_data) {
                        let config = JSON.parse(selectedMethod.config_data);
                        bank = config.bank_code || "MB";
                        acc = config.account_no || acc;
                        name = config.fullname || config.account_name || name;
                    }

                    let amount = order.summary.grand_total;
                    let desc = encodeURIComponent('Thanh toan don ' + activeTabId);

                    // Sinh link ảnh VietQR chuẩn
                    qrUrl = `https://img.vietqr.io/image/${bank}-${acc}-compact.png?amount=${amount}&addInfo=${desc}&accountName=${encodeURIComponent(name)}`;
                } else {
                    // Nếu là ZaloPay
                    qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=ZALOPAY_ORDER_${activeTabId}_AMT_${order.summary.grand_total}`;
                }

                // Đổ dữ liệu vào Modal QR
                document.getElementById('vietqr_image').src = qrUrl;
                document.getElementById('qr_amount_text').innerText = formatCurrency(order.summary.grand_total) + ' ₫';

                // Hiện Modal QR lên màn hình
                if (document.getElementById('payment_modal')) document.getElementById('payment_modal').style.display = 'none';
                document.getElementById('qr_display_modal').style.display = 'flex';

                // Chạy đồng hồ đếm ngược 5 phút (300 giây)
                startQRTimer(5 * 60);
            } else {
                // Nếu chọn Tiền mặt hoặc Chuyển khoản thường thì tính lại tiền thừa
                calculateChange();
            }
        }

        // --- COUPON & THANH TOÁN ---
        let btnApply = document.getElementById('btn_apply_promo');
        if (btnApply) btnApply.onclick = function() {
            let code = document.getElementById('promo_code_input').value.trim().toUpperCase();
            if (!code) return;
            ordersData[activeTabId].appliedPromoCode = code;
            document.getElementById('promo_code_input').value = '';
            triggerCalculation();
        };

        function removePromoCode() {
            ordersData[activeTabId].appliedPromoCode = '';
            triggerCalculation();
        }

        document.getElementById('btn_checkout').onclick = function() {
            if (ordersData[activeTabId].cart.length === 0) {
                alert('Giỏ hàng trống!');
                return;
            }
            if (IS_TWO_STEP) document.getElementById('payment_modal').style.display = 'flex';
            else processFinalPayment();
        };

        function processFinalPayment() {
            let order = ordersData[activeTabId];
            let btn = document.getElementById('btn_checkout');

            // Lấy cấu hình ngân hàng từ Database
            let selectedMethod = PAYMENT_METHODS.find(m => m.code === order.paymentMethod);

            // NẾU CHỌN VIETQR HOẶC ZALOPAY THÌ BẬT MÀN HÌNH QR
            if ((order.paymentMethod === 'vietqr' || order.paymentMethod === 'zalopay') && document.getElementById('qr_display_modal').style.display !== 'flex') {

                if (!selectedMethod || !selectedMethod.config_data) {
                    alert("Phương thức này chưa được Cấu hình số tài khoản!");
                    return;
                }

                let config = JSON.parse(selectedMethod.config_data);
                let qrUrl = '';

                if (order.paymentMethod === 'vietqr') {
                    // TỰ ĐỘNG LẤY TÀI KHOẢN MBBANK CỦA BẠN ĐỂ TẠO QR
                    let bank = config.bank_code;
                    let acc = config.account_no;
                    let name = encodeURIComponent(config.fullname);
                    let amount = order.summary.grand_total;
                    let desc = encodeURIComponent('Thanh toan don ' + activeTabId);
                    qrUrl = `https://img.vietqr.io/image/${bank}-${acc}-compact.png?amount=${amount}&addInfo=${desc}&accountName=${name}`;
                } else {
                    // ZaloPay (Giả lập QR Code ZaloPay)
                    qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=ZALOPAY_PAYMENT_ORDER_${activeTabId}`;
                }

                document.getElementById('vietqr_image').src = qrUrl;
                document.getElementById('qr_amount_text').innerText = formatCurrency(order.summary.grand_total) + ' ₫';

                if (document.getElementById('payment_modal')) document.getElementById('payment_modal').style.display = 'none';
                document.getElementById('qr_display_modal').style.display = 'flex';

                // Khởi động đồng hồ đếm ngược 5 phút
                startQRTimer(5 * 60);
                return;
            }

            // Nếu OFFLINE
            if (!navigator.onLine) {
                offlineOrders.push({
                    offline_id: 'OFF_' + Date.now(),
                    created_at: new Date().toLocaleString('vi-VN'),
                    cart_items: order.cart,
                    summary: order.summary,
                    payment_status: 'paid',
                    payment_method: order.paymentMethod,
                    amount_paid: order.amountGiven,
                    customer_id: order.customer ? order.customer.id : null,
                    customer_name: order.customer ? order.customer.customer_name : 'Khách lẻ',
                    error: null
                });
                localStorage.setItem('sapo_offline_orders', JSON.stringify(offlineOrders));
                updateNetworkStatus();
                alert(`📵 OFFLINE! Đã lưu tạm vào máy.`);
                printProvisional();
                if (document.getElementById('payment_modal')) document.getElementById('payment_modal').style.display = 'none';
                closeOrderTab(activeTabId);
                return;
            }

            // Online Checkout
            if (btn) {
                btn.innerText = 'ĐANG XỬ LÝ...';
                btn.disabled = true;
            }
            fetch('index.php?action=store_order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_items: order.cart,
                    summary: order.summary,
                    payment_status: 'paid',
                    payment_method: order.paymentMethod,
                    amount_paid: order.amountGiven,
                    customer_id: order.customer ? order.customer.id : null
                })
            }).then(res => res.json()).then(res => {
                if (res.status === 'success') {
                    if (document.getElementById('payment_modal')) document.getElementById('payment_modal').style.display = 'none';
                    let change = order.amountGiven - order.summary.grand_total;
                    alert(`🎉 Thành công! Tiền thừa: ${change > 0 ? formatCurrency(change) : 0} ₫`);
                    <?php if (isset($settings_db['pos_auto_print']) && $settings_db['pos_auto_print'] == '1') echo "window.open('index.php?action=print_order&id=' + (res.order_id || ''), '_blank');"; ?>
                    closeOrderTab(activeTabId);
                } else alert('Lỗi: ' + res.msg);
                if (btn) {
                    btn.innerText = 'THANH TOÁN (F9)';
                    btn.disabled = false;
                }
            });
        }

        function completeQRPayment() {
            document.getElementById('qr_display_modal').style.display = 'none';
            processFinalPayment();
        }

        // --- IN TẠM TÍNH ---
        function printProvisional() {
            let order = ordersData[activeTabId];
            if (order.cart.length === 0) {
                alert('Giỏ hàng trống!');
                return;
            }
            let win = window.open('', '_blank', 'width=400,height=600');
            let html = `<div style="font-family: monospace; width: 80mm; margin: 0 auto; text-align: center;"><h2>TẠM TÍNH (ĐƠN ${activeTabId})</h2><hr style="border:1px dashed #000;"><div style="text-align:left;font-size:13px;">`;
            order.cart.forEach(i => html += `<p><b>${i.name}</b><br>${i.qty} x ${formatCurrency(i.final_price)} = <b>${formatCurrency(i.line_total)}</b></p>`);
            html += `<hr style="border:1px dashed #000;"><p style="display:flex; justify-content:space-between;"><span>TỔNG CỘNG:</span> <b>${formatCurrency(order.summary.grand_total)}</b></p></div><p><i>Chưa có giá trị thanh toán</i></p></div><script>window.onload=function(){window.print();window.close();}<\/script>`;
            win.document.write(html);
            win.document.close();
        }

        // --- SỰ KIỆN GÕ PHÍM ---
        window.addEventListener('keydown', function(e) {
            if (e.key === 'F3') {
                e.preventDefault();
                document.getElementById('product_search').focus();
            }
            if (e.key === 'F4') {
                e.preventDefault();
                document.getElementById('customer_search').focus();
            }
            if (e.key === 'F2') {
                e.preventDefault();
                addCustomProduct();
            }
            if (e.key === 'F9') {
                e.preventDefault();
                let m = document.getElementById('payment_modal');
                if (m && m.style.display === 'flex') processFinalPayment();
                else document.getElementById('btn_checkout').click();
            }
        });
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target)) searchDropdown.style.display = 'none';
            if (!custSearchInput.contains(e.target)) custDropdown.style.display = 'none';
        });

        // --- KHỞI ĐỘNG ---
        document.addEventListener('DOMContentLoaded', () => {
            renderTabsBar();
            renderCustomerUI();
            triggerCalculation();
            updateNetworkStatus();
        });

        function startQRTimer(duration) {
            clearInterval(qrCountdownInterval);
            let timerDisplay = document.getElementById('qr_timer');
            let timer = duration;
            qrCountdownInterval = setInterval(function() {
                let minutes = parseInt(timer / 60, 10);
                let seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                timerDisplay.textContent = minutes + ":" + seconds;
                if (--timer < 0) {
                    clearInterval(qrCountdownInterval);
                    timerDisplay.textContent = "HẾT HẠN";
                    alert("Mã QR đã hết hạn. Vui lòng lấy lại mã!");
                }
            }, 1000);
        }

        function cancelQRPayment() {
            clearInterval(qrCountdownInterval);
            document.getElementById('qr_display_modal').style.display = 'none';
            closeOrderTab(activeTabId);
        }

        function editOrderQR() {
            clearInterval(qrCountdownInterval);
            document.getElementById('qr_display_modal').style.display = 'none';
        }

        function changePaymentMethod() {
            clearInterval(qrCountdownInterval);
            document.getElementById('qr_display_modal').style.display = 'none';
            if (IS_TWO_STEP) document.getElementById('payment_modal').style.display = 'flex';
        }

        function printOnlyQR() {
            let qrSrc = document.getElementById('vietqr_image').src;
            let win = window.open('', '_blank', 'width=400,height=400');
            win.document.write(`<div style="text-align:center;"><h3>Mã QR Thanh Toán</h3><img src="${qrSrc}" width="300"><p>Đơn hàng: ${activeTabId}</p></div><script>window.onload=function(){window.print();window.close();}<\/script>`);
            win.document.close();
        }
    </script>
</body>

</html>
