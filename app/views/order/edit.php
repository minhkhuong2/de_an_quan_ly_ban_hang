<?php

/**
 * @var array $products
 * @var array $customers
 * @var array $order_sources
 * @var array $employees
 * @var array $branches
 * @var array $shipping_partners
 * @var string $products_json
 * @var string $customers_json
 */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 22px;
        font-weight: bold;
        color: #212b36;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .v3-title a {
        color: #637381;
        text-decoration: none;
        font-size: 24px;
        margin-top: -4px;
    }

    .layout-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .col-main {
        flex: 0 0 65%;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .col-side {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        overflow: hidden;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
    }

    .card-body {
        padding: 20px;
    }

    /* Input & Search Styles */
    .search-box {
        position: relative;
        width: 100%;
        margin-bottom: 15px;
    }

    .search-box input,
    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .search-box input:focus,
    .form-control:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 2px rgba(0, 136, 255, 0.1);
    }

    .search-box .search-icon {
        position: absolute;
        left: 12px;
        top: 12px;
        color: #8c98a4;
    }

    .search-box input.has-icon {
        padding-left: 35px;
    }

    /* Bảng Sản phẩm */
    .table-cart {
        width: 100%;
        border-collapse: collapse;
    }

    .table-cart th {
        background: #f4f6f8;
        color: #637381;
        font-size: 13px;
        font-weight: 600;
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-cart td {
        padding: 15px 10px;
        border-bottom: 1px solid #dfe3e8;
        vertical-align: top;
    }

    .item-name {
        font-weight: 600;
        color: #0088ff;
        margin-bottom: 4px;
    }

    .item-sku {
        font-size: 12px;
        color: #637381;
    }

    .item-out-of-stock {
        color: #d82c0d;
        font-size: 11px;
        background: #fff1f0;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 5px;
        border: 1px solid #ffa39e;
        font-weight: normal;
    }

    .qty-input {
        width: 60px;
        text-align: center;
        padding: 6px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
    }

    .note-input {
        width: 100%;
        padding: 6px 10px;
        font-size: 12px;
        border: 1px dashed #c4cdd5;
        border-radius: 4px;
        margin-top: 8px;
        outline: none;
        background: #fafbfc;
    }

    .clickable-text {
        cursor: pointer;
        color: #0088ff;
        border-bottom: 1px dashed #0088ff;
        font-weight: 500;
    }

    .btn-action {
        color: #d82c0d;
        cursor: pointer;
        font-size: 16px;
        border: none;
        background: none;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        padding: 8px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #212b36;
    }

    .dropdown-results {
        position: absolute;
        top: 42px;
        left: 0;
        width: 100%;
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 4px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: none;
        max-height: 200px;
        overflow-y: auto;
    }

    .dropdown-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f4f6f8;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dropdown-item:hover {
        background: #f4f6f8;
    }

    /* Modals & Tabs */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 500px;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 85vh;
        overflow-y: auto;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        border-top: 1px solid #dfe3e8;
        padding-top: 15px;
    }

    .ship-tabs {
        display: flex;
        border-bottom: 1px solid #dfe3e8;
        margin-bottom: 15px;
        background: #f4f6f8;
        padding: 5px 5px 0 5px;
        border-radius: 4px;
    }

    .ship-tab-item {
        flex: 1;
        text-align: center;
        padding: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #637381;
        cursor: pointer;
        border-radius: 4px 4px 0 0;
    }

    .ship-tab-item.active {
        background: #fff;
        color: #0088ff;
        border: 1px solid #dfe3e8;
        border-bottom-color: transparent;
    }

    /* Tag Style */
    .tag-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 8px;
    }

    .tag-badge {
        background: #e5f0ff;
        color: #0088ff;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .tag-close {
        cursor: pointer;
        color: #8c98a4;
        font-weight: bold;
    }

    .tag-close:hover {
        color: #d82c0d;
    }
</style>

<div class="v3-header">
    <?php if(isset($_GET['copy']) && $_GET['copy'] == 1): ?>
        <div class="v3-title"><a href="index.php?action=order_list">←</a> Tạo đơn hàng (Sao chép từ <?php echo htmlspecialchars($order['order_code']); ?>)</div>
    <?php else: ?>
        <div class="v3-title"><a href="index.php?action=view_order&id=<?php echo $order['id']; ?>">←</a> Chỉnh sửa đơn hàng <?php echo htmlspecialchars($order['order_code']); ?></div>
    <?php endif; ?>
</div>

<div class="layout-grid">
    <div class="col-main">
        <div class="v3-card">
            <div class="card-header">
                <span>Chi tiết sản phẩm</span>
                <div style="display: flex; gap: 15px; font-weight: normal; font-size: 13px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;"><input type="checkbox" id="cb_separate_line"> Tách dòng</label>
                    <a href="javascript:void(0)" onclick="checkInventory()" style="color: #0088ff; text-decoration: none;">🔍 Kiểm tra tồn kho</a>
                </div>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="product_search" class="has-icon" placeholder="F3 - Nhập tên, SKU hoặc quét mã vạch sản phẩm...">
                    <div id="product_dropdown" class="dropdown-results"></div>
                </div>
                <table class="table-cart">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Sản phẩm</th>
                            <th style="width: 15%; text-align: center;">Số lượng</th>
                            <th style="width: 18%; text-align: right;">Đơn giá</th>
                            <th style="width: 17%; text-align: right;">Thành tiền</th>
                            <th style="width: 5%; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody id="cart_body">
                        <tr>
                            <td colspan="5" style="text-align: center; color: #8c98a4; padding: 30px;">Chưa có sản phẩm nào trong đơn hàng.</td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 15px;"><button class="btn-outline" style="border: 1px dashed #0088ff; color: #0088ff; background: transparent;" onclick="addCustomService()">+ Thêm phí phụ thu / dịch vụ tùy chỉnh</button></div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">8. Thông tin đối tác giao hàng & Phí vận chuyển</div>
            <div class="card-body">
                <div class="ship-tabs">
                    <div class="ship-tab-item active" onclick="switchShippingTab('carrier', this)">8.1. Qua hãng vận chuyển</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('self', this)">8.2. Tự giao hàng</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('delivered', this)">8.3. Đã giao hàng</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('later', this)">8.4. Giao hàng sau</div>
                </div>

                <div id="ship_block_carrier">
                    <p style="font-size:13px; color:#637381; margin-bottom:12px;">Hệ thống tự động liên kết kết nối API với các hãng tàu GHTK, GHN, Viettel Post...</p>
                    <div class="form-group">
                        <label style="font-size:13px; color:#212b36; margin-bottom:5px; display:block;">Chọn đối tác vận chuyển tích hợp</label>
                        <select class="form-control" onchange="setCarrierFee(this.value)">
                            <option value="0">-- Chọn hãng vận chuyển hàng hóa --</option>
                            <?php foreach ($shipping_partners as $partner): ?>
                                <option value="<?php echo htmlspecialchars($partner['base_fee']); ?>"><?php echo htmlspecialchars($partner['partner_name']); ?> (Dự kiến: <?php echo number_format($partner['base_fee'], 0, ',', '.'); ?>đ)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="ship_block_self" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom:15px;">
                        <div>
                            <label style="font-size:12px; color:#637381;">Địa chỉ lấy hàng (Kho xuất)</label>
                            <select id="self_pickup_address" class="form-control">
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b['branch_name']); ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">Địa chỉ giao khách hàng</label>
                            <input type="text" id="self_delivery_address" class="form-control" placeholder="Tự động bốc từ thông tin khách hàng">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">Tiền thu hộ COD (đ)</label>
                            <input type="number" id="self_cod_amount" class="form-control" value="0">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">Kích thước gói hàng (Dài x Rộng x Cao cm)</label>
                            <div style="display:flex; gap:5px;">
                                <input type="number" placeholder="D" class="form-control" style="padding:6px;" value="20">
                                <input type="number" placeholder="R" class="form-control" style="padding:6px;" value="10">
                                <input type="number" placeholder="C" class="form-control" style="padding:6px;" value="5">
                            </div>
                        </div>
                    </div>

                    <div style="background:#f4f6f8; padding:15px; border-radius:6px; display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap:15px;">
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">Chọn đối tác / Shiper</label>
                            <select id="self_shipper_partner" class="form-control">
                                <option value="Shiper Nguyễn Văn A">Shiper Nguyễn Văn A (Nội bộ)</option>
                                <option value="Đội xe ôm công nghệ">Đội xe công nghệ ngoài</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">Mã vận đơn</label>
                            <input type="text" id="self_tracking_code" class="form-control" placeholder="Mã theo dõi...">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">Người trả phí ship</label>
                            <select id="self_fee_payer" class="form-control" onchange="calculateOrderTotals()">
                                <option value="khach">Khách trả (Cộng vào đơn)</option>
                                <option value="shop">Shop trả (Trừ chi phí shop)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">Phí vận chuyển thực tế (đ)</label>
                            <input type="number" id="self_ship_fee_input" class="form-control" value="0" oninput="updateSelfShippingFee(this.value)">
                        </div>
                    </div>
                </div>

                <div id="ship_block_delivered" style="display: none;">
                    <div class="form-group">
                        <label style="font-size:13px; color:#637381;">Chọn hình thức giao hàng trực tiếp</label>
                        <select class="form-control">
                            <option>Khách nhận trực tiếp tại cửa hàng quầy</option>
                            <option>Dùng đối tác vận chuyển ngoài tự gọi tự trả</option>
                        </select>
                    </div>
                </div>

                <div id="ship_block_later" style="display: none;">
                    <p style="color:#e67e22; font-size:13px; font-weight:500;">📦 Hệ thống sẽ đóng gói lưu kho đơn hàng ở trạng thái Nháp/Chờ xử lý, chưa xuất hàng ngay lập tức.</p>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">
                <span>7. Thông tin Hóa đơn điện tử (Hệ thống Invoice)</span>
                <button type="button" class="clickable-text" style="border:none; background:none; font-size:13px;" onclick="openInvoiceModal()">⚙️ Thêm/Sửa thông tin xuất hoá đơn</button>
            </div>
            <div class="card-body" id="invoice_summary_box" style="font-size:13px; color:#637381; line-height:1.6;">
                <i>Chưa cập nhật thông tin xuất hóa đơn đỏ VAT cho đơn hàng này.</i>
            </div>
        </div>
    </div>

    <div class="col-side">
        <div class="v3-card">
            <div class="card-header">Khách hàng</div>
            <div class="card-body">
                <div class="search-box" style="margin-bottom: 0;">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="customer_search" class="has-icon" placeholder="Tìm tên, SĐT khách hàng...">
                    <div id="customer_dropdown" class="dropdown-results"></div>
                </div>
                <div id="selected_customer_box" style="display: none; margin-top: 15px; background: #f4f6f8; padding: 12px; border-radius: 4px; border: 1px solid #dfe3e8;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div style="font-weight: 600; color: #212b36;" id="display_cust_name"></div>
                            <div style="font-size: 13px; color: #637381; margin-top: 4px;" id="display_cust_phone"></div>
                            <div style="font-size: 12px; color: #637381; margin-top: 4px;" id="display_cust_address"></div>
                        </div>
                        <button class="btn-action" onclick="clearSelectedCustomer()">✖</button>
                    </div>
                </div>
                <div id="btn_add_cust_block" style="margin-top: 12px; text-align: right;"><button class="clickable-text" style="border:none; background:none; font-size:13px;" onclick="document.getElementById('add_customer_modal').style.display='flex'">+ Tạo nhanh khách hàng mới</button></div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">3. Nguồn đơn hàng (Động)</div>
            <div class="card-body">
                <select id="order_source" class="form-control">
                    <?php foreach ($order_sources as $src): ?>
                        <option value="<?php echo htmlspecialchars($src['source_name']); ?>" <?php echo $src['source_name'] == 'Admin' ? 'selected' : ''; ?>><?php echo htmlspecialchars($src['source_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">6. Thông tin bổ sung đơn hàng</div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.1. Bán tại chi nhánh</label>
                    <select id="order_branch" class="form-control">
                        <?php foreach ($branches as $b): ?>
                            <option value="<?php echo htmlspecialchars($b['branch_name']); ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.2. Nhân viên phụ trách</label>
                    <select id="order_assignee" class="form-control">
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo htmlspecialchars($emp['full_name']); ?>"><?php echo htmlspecialchars($emp['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.3. Ngày đặt hàng (Quá khứ/Hiện tại)</label>
                    <input type="datetime-local" id="order_date" class="form-control" onchange="validateOrderDate(this.value)">
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.4. Ngày hẹn giao (Hiện tại/Tương lai)</label>
                    <input type="datetime-local" id="delivery_date" class="form-control" onchange="validateDeliveryDate(this.value)">
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.5. Thẻ Tag phân loại (Gõ chữ rồi ấn Enter)</label>
                    <input type="text" id="tag_input" class="form-control" placeholder="Thêm tag quy trình...">
                    <div class="tag-container" id="tag_box"></div>
                </div>
            </div>
        </div>

        <div class="v3-card" style="background: #fafbfc;">
            <div class="card-header">Thanh toán đơn hàng</div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #dfe3e8;">
                    <div style="display:flex; gap:20px; align-items: center;">
                        <label style="cursor:pointer; font-weight:600;"><input type="radio" name="payment_status" value="paid" checked onchange="togglePaymentAmount()"> Đã thanh toán</label>
                        <label style="cursor:pointer; font-weight:600;"><input type="radio" name="payment_status" value="unpaid" onchange="togglePaymentAmount()"> Thanh toán sau (COD/Nợ)</label>
                    </div>
                    <div id="payment_details_block" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div>
                            <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">Hình thức thanh toán</label>
                            <select id="payment_method" class="form-control">
                                <?php foreach ($payment_methods as $pm): ?>
                                    <option value="<?php echo htmlspecialchars($pm['name']); ?>"><?php echo htmlspecialchars($pm['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">Số tiền (đ)</label>
                            <input type="number" id="payment_amount" class="form-control" value="0">
                        </div>
                    </div>
                </div>

                <div class="summary-row" style="font-size:13px;"><span>Tiền hàng ban đầu:</span> <span id="sum_original" style="font-weight:600;">0 ₫</span></div>
                <div class="summary-row" style="font-size:13px; align-items: center;">
                    <span>Khuyến mại / Giảm giá <span style="font-size:10px; color:#0088ff; cursor:pointer;" onclick="openDiscountModal()">[Sửa giảm giá (F6)]</span>:</span> 
                    <span id="sum_discount" style="font-weight:600; color:#d82c0d;">- 0 ₫</span>
                </div>
                <div class="summary-row" style="font-size:13px;"><span>Tiền hàng sau CK:</span> <span id="sum_after_dc" style="font-weight:600;">0 ₫</span></div>
                <div class="summary-row" style="font-size:13px;"><span>Phí ship vận chuyển:</span> <span id="sum_ship_fee" style="font-weight:600;">0 ₫</span></div>
                <div class="summary-row" style="font-size:15px; margin-top:10px; padding-top:10px; border-top:1px solid #dfe3e8; color:#d82c0d; font-weight:bold;">
                    <span>Tổng Khách Phải Trả:</span> <span id="sum_final" style="font-size:18px;">0 ₫</span>
                </div>

                <div id="action_buttons_container" style="display: flex; gap: 10px; margin-top: 15px;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="invoice_modal">
    <div class="modal-content" style="width:550px;">
        <h3 style="margin-bottom: 15px; color: #212b36; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Popup Thông tin xuất hóa đơn đỏ VAT</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
            <div class="form-group" style="grid-column: span 2;">
                <label>Mã số thuế doanh nghiệp <span>*</span></label>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="inv_mst" class="form-control" placeholder="10 hoặc 12 số chữ..." maxlength="14">
                    <button type="button" class="btn-outline" style="color:#0088ff; border-color:#0088ff;" onclick="fetchCompanyInfoByMST()">Lấy thông tin</button>
                </div>
            </div>
            <div class="form-group" style="grid-column: span 2;"><label>Tên công ty đơn vị</label><input type="text" id="inv_company" class="form-control"></div>
            <div class="form-group" style="grid-column: span 2;"><label>Địa chỉ công ty đăng ký</label><input type="text" id="inv_address" class="form-control"></div>
            <div class="form-group"><label>Tên người mua đại diện</label><input type="text" id="inv_buyer" class="form-control"></div>
            <div class="form-group"><label>Số Căn cước công dân (12 số)</label><input type="text" id="inv_cccd" class="form-control" maxlength="12"></div>
            <div class="form-group"><label>Mã ĐVQH ngân sách (7 số)</label><input type="text" id="inv_qhns" class="form-control" maxlength="7"></div>
            <div class="form-group"><label>Số điện thoại liên hệ</label><input type="text" id="inv_phone" class="form-control" value="+84 "></div>
            <div class="form-group" style="grid-column: span 2;"><label>Email nhận hóa đơn đỏ</label><input type="email" id="inv_email" class="form-control" placeholder="VD: info@aakc.com"></div>
        </div>

        <div style="margin-top:15px; background:#f4f6f8; padding:10px; border-radius:4px; font-size:13px; display:flex; flex-direction:column; gap:8px;">
            <label style="cursor:pointer; display:flex; gap:6px; font-weight:normal;"><input type="checkbox" id="chk_no_invoice"> Người mua không lấy hóa đơn (Xuất bán lẻ)</label>
            <label style="cursor:pointer; display:flex; gap:6px; font-weight:normal;"><input type="checkbox" id="chk_save_default" checked> Lưu làm thông tin xuất hóa đơn mặc định</label>
        </div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal('invoice_modal')">Hủy bỏ</button>
            <button class="btn-primary" onclick="saveInvoiceFormDetails()">Xác nhận lưu</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="add_customer_modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px; color: #0088ff;">Thêm mới khách hàng hệ thống</h3>
        <div class="form-group"><label>Họ tên khách hàng <span>*</span></label><input type="text" id="nc_name" class="form-control" required></div>
        <div class="form-group"><label>Số điện thoại <span>*</span></label><input type="text" id="nc_phone" class="form-control" required></div>
        <div class="form-group"><label>Địa chỉ thường trú</label><input type="text" id="nc_address" class="form-control"></div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal('add_customer_modal')">Hủy</button>
            <button class="btn-primary" onclick="submitQuickCustomerForm()">Lưu thông tin</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="discount_modal">
    <div class="modal-content" style="width: 400px;">
        <h3 style="margin-bottom: 15px; color: #212b36;">Áp dụng khuyến mại / Giảm giá</h3>
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Mã giảm giá (Coupon / Voucher)</label>
            <input type="text" id="discount_code_input" class="form-control" placeholder="Nhập mã giảm giá..." onkeyup="this.value = this.value.toUpperCase()">
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="cursor:pointer; display:flex; gap:8px; align-items:center;">
                <input type="checkbox" id="auto_promo_checkbox"> Tự động thêm chương trình khuyến mại phù hợp nhất
            </label>
        </div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal('discount_modal')">Hủy</button>
            <button class="btn-primary" onclick="applyDiscountAction()">Áp dụng</button>
        </div>
    </div>
</div>

<script>
    const PRODUCTS = <?php echo isset($products_json) ? $products_json : '[]'; ?>;
    const CUSTOMERS = <?php echo isset($customers_json) ? $customers_json : '[]'; ?>;

    let cart = [];
    <?php if (isset($order_items)): ?>
        <?php foreach ($order_items as $item): ?>
            cart.push({
                id: <?php echo $item['product_id']; ?>,
                sku: '<?php echo $item['sku']; ?>',
                product_name: '<?php echo addslashes($item['product_name']); ?>',
                price: <?php echo $item['original_price']; ?>,
                final_price: <?php echo $item['final_price']; ?>,
                qty: <?php echo $item['qty']; ?>,
                line_total: <?php echo $item['line_total']; ?>,
                stock: 100 // dummy stock
            });
        <?php endforeach; ?>
    <?php endif; ?>

    let tagsList = [];
    let selectedCustomer = null;
    <?php if (!empty($order['customer_id'])): ?>
        selectedCustomer = {
            id: <?php echo $order['customer_id']; ?>,
            customer_name: '<?php echo addslashes($order['customer_name']); ?>',
            phone: '<?php echo $order['phone']; ?>'
        };
        // wait for DOM to load to render
        window.addEventListener('DOMContentLoaded', () => {
            selectCustomer(selectedCustomer.id, selectedCustomer.customer_name, selectedCustomer.phone);
        });
    <?php endif; ?>
    
    let invoiceData = null; // Lưu cụm thông tin VAT hóa đơn điện tử

    let currentShippingMode = 'carrier'; // carrier, self, delivered, later
    let orderShippingFee = <?php echo $order['original_shipping_fee'] ?? 0; ?>; 
    let orderDiscountValue = <?php echo $order['total_order_discount'] ?? 0; ?>; 
    
    window.addEventListener('DOMContentLoaded', () => {
        if(cart.length > 0) {
            renderCart();
            calculateOrderTotals();
        }
    });

    function formatMoney(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Mặc định nạp ngày giờ hiện tại cho ô chọn thời gian đặt hàng khi load trang
    window.addEventListener('DOMContentLoaded', () => {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('order_date').value = now.toISOString().slice(0, 16);
    });

    // =====================================
    // 6. XỬ LÝ KHỐI THÔNG TIN BỔ SUNG (VALIDATIONS)
    // =====================================
    function validateOrderDate(inputVal) {
        if (!inputVal) return;
        let selectedDate = new Date(inputVal);
        let now = new Date();
        if (selectedDate > now) {
            alert("⚠️ Lỗi nghiệp vụ Hệ thống: Không thể chọn ngày trong tương lai làm ngày đặt hàng!");
            document.getElementById('order_date').value = now.toISOString().slice(0, 16);
        }
    }

    function validateDeliveryDate(inputVal) {
        if (!inputVal) return;
        let selectedDate = new Date(inputVal);
        let now = new Date();
        // Cắt bỏ phần giờ phút để so sánh ngày quá khứ chuẩn xác
        now.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        if (selectedDate < now) {
            alert("⚠️ Lỗi nghiệp vụ Hệ thống: Không thể chọn ngày hẹn giao trong quá khứ!");
            document.getElementById('delivery_date').value = "";
        }
    }

    // Gõ thẻ Tag ấn Enter (Mục 6.5)
    const tagInput = document.getElementById('tag_input');
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let text = this.value.trim();
            if (!text) return;
            if (!tagsList.includes(text)) {
                tagsList.push(text);
                renderTagsUI();
            }
            this.value = '';
        }
    });

    function renderTagsUI() {
        let box = document.getElementById('tag_box');
        box.innerHTML = '';
        tagsList.forEach((t, i) => {
            box.innerHTML += `<span class="tag-badge">🏷️ ${t} <span class="tag-close" onclick="removeTagItem(${i})">×</span></span>`;
        });
    }

    function removeTagItem(index) {
        tagsList.splice(index, 1);
        renderTagsUI();
    }

    // =====================================
    // 7. XỬ LÝ POPUP HÓA ĐƠN ĐIỆN TỬ
    // =====================================
    function openInvoiceModal() {
        // Nếu trước đó đã chọn khách hàng và khách hàng có sẵn dữ liệu thì tự động điền (Mục 7)
        if (selectedCustomer && !invoiceData) {
            document.getElementById('inv_buyer').value = selectedCustomer.customer_name;
            document.getElementById('inv_phone').value = "+84 " + selectedCustomer.phone;
            document.getElementById('inv_address').value = selectedCustomer.address || '';
        }
        document.getElementById('invoice_modal').style.display = 'flex';
    }

    // Giả lập chức năng tra cứu MST tự động của Hệ thống
    function fetchCompanyInfoByMST() {
        let mst = document.getElementById('inv_mst').value.trim();
        if (mst.length < 10) {
            alert("Mã số thuế doanh nghiệp phải từ 10-12 ký tự số!");
            return;
        }

        // Giả lập bắn API tra cứu thuế nội bộ
        document.getElementById('inv_company').value = "CÔNG TY CỔ PHẦN CÔNG NGHỆ THƯƠNG MẠI ĐIỆN TỬ AAKC";
        document.getElementById('inv_address').value = "Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Thành phố Hà Nội";
        alert("✨ Đã liên kết API cơ sở dữ liệu Tổng cục Thuế: Lấy thông tin công ty thành công!");
    }

    function saveInvoiceFormDetails() {
        let email = document.getElementById('inv_email').value.trim();
        let cccd = document.getElementById('inv_cccd').value.trim();
        let qhns = document.getElementById('inv_qhns').value.trim();

        // Kiểm tra định dạng dữ liệu chặt chẽ theo tài liệu Hệ thống
        if (email && !email.includes('@')) {
            alert("Định dạng email nhận hóa đơn không hợp lệ!");
            return;
        }
        if (cccd && cccd.length !== 12) {
            alert("Căn cước công dân phải nhập đúng định dạng 12 ký tự số!");
            return;
        }
        if (qhns && qhns.length !== 7) {
            alert("Mã đơn vị quan hệ ngân sách phải nhập đúng định dạng 7 ký tự số!");
            return;
        }

        invoiceData = {
            mst: document.getElementById('inv_mst').value.trim(),
            company: document.getElementById('inv_company').value.trim(),
            address: document.getElementById('inv_address').value.trim(),
            buyer: document.getElementById('inv_buyer').value.trim(),
            cccd: cccd,
            qhns: qhns,
            phone: document.getElementById('inv_phone').value.trim(),
            email: email,
            no_invoice: document.getElementById('chk_no_invoice').checked
        };

        // Cập nhật tóm tắt thông tin ra giao diện chính
        let summaryBox = document.getElementById('invoice_summary_box');
        if (invoiceData.no_invoice) {
            summaryBox.innerHTML = "❌ Đơn hàng ghi nhận: <b>Khách hàng không lấy hóa đơn đỏ VAT</b>";
        } else {
            summaryBox.innerHTML = `🏢 Công ty: <b>${invoiceData.company || 'Chưa điền'}</b><br>🔢 MST: ${invoiceData.mst} | 📧 Email: ${invoiceData.email}`;
        }
        closeModal('invoice_modal');
    }

    // =====================================
    // 8. HỆ THỐNG TAB VẬN CHUYỂN & PHÍ SHIP
    // =====================================
    function switchShippingTab(mode, btnElement) {
        currentShippingMode = mode;
        document.querySelectorAll('.ship-tab-item').forEach(b => b.classList.remove('active'));
        btnElement.classList.add('active');

        // Ẩn toàn bộ các khối subform vận chuyển
        document.getElementById('ship_block_carrier').style.display = 'none';
        document.getElementById('ship_block_self').style.display = 'none';
        document.getElementById('ship_block_delivered').style.display = 'none';
        document.getElementById('ship_block_later').style.display = 'none';

        // Bật đúng khối form người dùng chọn
        document.getElementById('ship_block_' + mode).style.display = 'block';

        // Đặt lại phí ship tương ứng
        if (mode === 'carrier' || mode === 'delivered' || mode === 'later') {
            orderShippingFee = 0;
            document.getElementById('lbl_shipping_fee').innerText = formatMoney(0);
        } else if (mode === 'self') {
            let val = parseFloat(document.getElementById('self_ship_fee_input').value) || 0;
            updateSelfShippingFee(val);
        }
        calculateOrderTotals();
    }

    function setCarrierFee(fee) {
        orderShippingFee = parseFloat(fee);
        document.getElementById('lbl_shipping_fee').innerText = formatMoney(orderShippingFee);
        calculateOrderTotals();
    }

    function updateSelfShippingFee(feeVal) {
        let payer = document.getElementById('self_fee_payer').value;
        if (payer === 'khach') {
            orderShippingFee = parseFloat(feeVal) || 0;
            document.getElementById('lbl_shipping_fee').innerText = formatMoney(orderShippingFee);
        } else {
            // Nếu shop tự trả phí ship, phí ship tính toán cộng thêm vào đơn khách hàng bằng = 0đ
            orderShippingFee = 0;
            document.getElementById('lbl_shipping_fee').innerText = formatMoney(0) + " (Shop chịu phí)";
        }
        calculateOrderTotals();
    }

    // =====================================
    // LOGIC CORE TÍNH TOÁN TIỀN CHUẨN ĐÉT
    // =====================================
    function calculateOrderTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += (item.price - item.discount) * item.qty;
        });

        // Xử lý phí ship tự giao hàng theo tùy chọn người trả phí (Mục 8.2)
        if (currentShippingMode === 'self') {
            let feeVal = parseFloat(document.getElementById('self_ship_fee_input').value) || 0;
            let payer = document.getElementById('self_fee_payer').value;
            if (payer === 'khach') {
                orderShippingFee = feeVal;
                if(document.getElementById('lbl_shipping_fee')) document.getElementById('lbl_shipping_fee').innerText = formatMoney(orderShippingFee);
            } else {
                orderShippingFee = 0;
                if(document.getElementById('lbl_shipping_fee')) document.getElementById('lbl_shipping_fee').innerText = formatMoney(0) + " (Shop tự chi)";
            }
        }

        let isTax = document.getElementById('cb_apply_tax') ? document.getElementById('cb_apply_tax').checked : false;
        let taxAmount = isTax ? Math.round(subtotal * 0.1) : 0;
        let grandTotal = subtotal - orderDiscountValue + taxAmount + orderShippingFee;
        if(grandTotal < 0) grandTotal = 0;

        // Cập nhật khối tóm tắt tiền phía dưới góc phải
        if(document.getElementById('sum_subtotal')) document.getElementById('sum_subtotal').innerText = formatMoney(subtotal);
        if(document.getElementById('sum_tax')) document.getElementById('sum_tax').innerText = formatMoney(taxAmount);
        
        if(document.getElementById('sum_original')) document.getElementById('sum_original').innerText = formatMoney(subtotal);
        if(document.getElementById('sum_discount')) document.getElementById('sum_discount').innerText = "- " + formatMoney(orderDiscountValue);
        if(document.getElementById('sum_after_dc')) document.getElementById('sum_after_dc').innerText = formatMoney(subtotal - orderDiscountValue);
        if(document.getElementById('sum_ship_fee')) document.getElementById('sum_ship_fee').innerText = formatMoney(orderShippingFee);
        if(document.getElementById('sum_final')) document.getElementById('sum_final').innerText = formatMoney(grandTotal);

        // Đồng bộ số tiền thu hộ COD mặc định bằng tổng tiền đơn hàng (Mục 8.2)
        let codInput = document.getElementById('self_cod_amount');
        if (codInput && document.activeElement !== codInput) {
            codInput.value = grandTotal;
        }

        togglePaymentAmount();
    }

    // =====================================
    // CORE CÁC HÀM PHỤ TRỢ (GIỮ NGUYÊN BÀI CŨ)
    // =====================================
    const pSearch = document.getElementById('product_search');
    const pDropdown = document.getElementById('product_dropdown');
    window.addEventListener('keydown', function(e) {
        if (e.key === 'F3') {
            e.preventDefault();
            pSearch.focus();
        }
    });

    pSearch.addEventListener('input', function() {
        let kw = this.value.toLowerCase().trim();
        pDropdown.innerHTML = '';
        if (!kw) {
            pDropdown.style.display = 'none';
            return;
        }
        let results = PRODUCTS.filter(p => p.product_name.toLowerCase().includes(kw) || (p.sku && p.sku.toLowerCase().includes(kw)));
        if (results.length > 0) {
            pDropdown.style.display = 'block';
            results.forEach(p => {
                let div = document.createElement('div');
                div.className = 'dropdown-item';
                div.innerHTML = `<div><div class="item-name">${p.product_name}</div><div class="item-sku">${p.sku} | Tồn: ${p.stock}</div></div><b>${formatMoney(p.price)}</b>`;
                div.onmousedown = () => {
                    cart.push({
                        ...p,
                        qty: 1,
                        discount: 0,
                        note: ''
                    });
                    calculateOrderTotals();
                    pSearch.value = '';
                    pDropdown.style.display = 'none';
                };
                pDropdown.appendChild(div);
            });
        }
    });
    pSearch.addEventListener('blur', () => setTimeout(() => pDropdown.style.display = 'none', 200));

    // Chọn khách hàng và điền địa chỉ giao tự động sang ô Ship (Mục 8.1)
    const cSearch = document.getElementById('customer_search');
    const cDropdown = document.getElementById('customer_dropdown');
    cSearch.addEventListener('input', function() {
        let val = this.value.toLowerCase().trim();
        cDropdown.innerHTML = '';
        if (!val) {
            cDropdown.style.display = 'none';
            return;
        }
        let res = CUSTOMERS.filter(c => c.customer_name.toLowerCase().includes(val) || c.phone.includes(val));
        if (res.length > 0) {
            cDropdown.style.display = 'block';
            res.forEach(c => {
                let div = document.createElement('div');
                div.className = 'dropdown-item';
                div.innerHTML = `<span>👤 <b>${c.customer_name}</b></span> <small>📞 ${c.phone}</small>`;
                div.onmousedown = () => {
                    selectedCustomer = c;
                    document.getElementById('display_cust_name').innerText = c.customer_name;
                    document.getElementById('display_cust_phone').innerText = '📞 ' + c.phone;
                    document.getElementById('display_cust_address').innerText = '📍 ' + (c.address || 'Chưa có địa chỉ');
                    document.getElementById('self_delivery_address').value = c.address || ''; // Auto fill sang mục 8.2
                    document.getElementById('selected_customer_box').style.display = 'block';
                    cSearch.style.display = 'none';
                    document.getElementById('btn_add_cust_block').style.display = 'none';
                };
                cDropdown.appendChild(div);
            });
        }
    });
    cSearch.addEventListener('blur', () => setTimeout(() => cDropdown.style.display = 'none', 200));

    function clearSelectedCustomer() {
        selectedCustomer = null;
        document.getElementById('selected_customer_box').style.display = 'none';
        cSearch.style.display = 'block';
        cSearch.value = '';
        document.getElementById('btn_add_cust_block').style.display = 'block';
    }

    function addCustomService() {
        let name = prompt("Nhập tên dịch vụ tùy chỉnh (Mục 1.4):");
        let price = prompt("Nhập đơn giá:");
        if (name && price) {
            cart.push({
                id: 'SRV_' + Date.now(),
                product_name: name,
                sku: 'SERVICE',
                price: parseFloat(price),
                stock: 999,
                qty: 1,
                discount: 0,
                note: ''
            });
            calculateOrderTotals();
        }
    }
    // =====================================
    // HÀM RENDER NÚT BẤM DỰA VÀO TAB GIAO HÀNG
    // =====================================
    function renderActionButtons() {
        let container = document.getElementById('action_buttons_container');
        const urlParams = new URLSearchParams(window.location.search);
        const isCopyMode = urlParams.get('copy') == '1';

        if (currentShippingMode === 'later') {
            if (isCopyMode) {
                container.innerHTML = `
                    <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('draft')">Lưu nháp</button>
                    <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px; color:#0088ff; border-color:#0088ff;" onclick="handleOrderSubmit('create')">Tạo đơn hàng</button>
                    <button class="btn-primary" style="flex: 1.2; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('confirm')">Tạo & Xác nhận</button>
                `;
            } else {
                container.innerHTML = `
                    <?php if(isset($order['draft_status']) && $order['draft_status'] == 'open'): ?>
                        <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('draft')">Lưu nháp</button>
                        <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px; color:#0088ff; border-color:#0088ff;" onclick="handleOrderSubmit('create')">Tạo đơn hàng</button>
                        <button class="btn-primary" style="flex: 1.2; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('confirm')">Tạo & Xác nhận</button>
                    <?php else: ?>
                        <button class="btn-primary" style="padding: 10px 25px;" onclick="handleOrderSubmit('confirm')">Lưu cập nhật</button>
                    <?php endif; ?>
                `;
            }
        } else {
            if (isCopyMode) {
                container.innerHTML = `
                    <button class="btn-primary" style="width: 100%; padding: 12px; font-size:15px;" onclick="handleOrderSubmit('ship')">🚀 Tạo đơn và Giao hàng</button>
                `;
            } else {
                container.innerHTML = `
                    <?php if(isset($order['draft_status']) && $order['draft_status'] == 'open'): ?>
                        <button class="btn-primary" style="width: 100%; padding: 12px; font-size:15px;" onclick="handleOrderSubmit('ship')">🚀 Tạo đơn và Giao hàng</button>
                    <?php else: ?>
                        <button class="btn-primary" style="width: 100%; padding: 12px; font-size:15px;" onclick="handleOrderSubmit('confirm')">Lưu cập nhật</button>
                    <?php endif; ?>
                `;
            }
        }
    }

    // Gọi hàm render lần đầu khi vừa mở trang
    window.addEventListener('DOMContentLoaded', renderActionButtons);

    // Bổ sung gọi hàm render mỗi khi chuyển Tab giao hàng
    // (Khương tìm hàm switchShippingTab cũ và THÊM dòng `renderActionButtons();` vào cuối hàm đó)
    const originalSwitchShippingTab = switchShippingTab;
    switchShippingTab = function(mode, btnElement) {
        originalSwitchShippingTab(mode, btnElement);
        renderActionButtons(); // Cập nhật nút ngay khi đổi tab
    }

    // =====================================
    // G. PHÁT LỆNH SUBMIT ĐƠN HÀNG ĐỘNG (CÓ CHECK BÁN ÂM)
    // =====================================
    function handleOrderSubmit(actionType) {
        if (cart.length === 0) {
            alert("Giỏ hàng đang trống, không thể xuất đơn!");
            return;
        }

        // CẢNH BÁO BÁN ÂM (Trường hợp mua vượt số lượng tồn kho)
        let outOfStockItems = cart.filter(item => item.qty > item.stock);
        if (outOfStockItems.length > 0) {
            let msg = "⚠️ CẢNH BÁO: MỘT SỐ MẶT HÀNG ĐÃ BÁN HẾT!\n\n";
            outOfStockItems.forEach(i => {
                msg += `- ${i.product_name} (Tồn: ${i.stock} | Khách đặt: ${i.qty})\n`;
            });
            msg += "\nBạn có muốn CHO PHÉP BÁN ÂM để tiếp tục lên đơn không?\n(Nhấn OK để bán âm / Nhấn Cancel để Quay lại sửa đơn)";

            if (!confirm(msg)) {
                return; // Dừng lại, cho người dùng sửa số lượng
            }
        }

        // ĐÓNG GÓI PAYLOAD DỮ LIỆU
        let payload = {
            order_id: <?php echo $order['id']; ?>,
            action_type: actionType, // 'draft', 'create', 'confirm', 'ship'
            cart_items: cart,
            customer_id: selectedCustomer ? selectedCustomer.id : null,
            source: document.getElementById('order_source').value,
            branch: document.getElementById('order_branch').value,
            assignee: document.getElementById('order_assignee').value,
            order_date: document.getElementById('order_date').value,
            delivery_date: document.getElementById('delivery_date').value,
            tags: tagsList,
            invoice_details: invoiceData,
            shipping_mode: currentShippingMode,
            shipping_fee: orderShippingFee,
            payment_status: document.querySelector('input[name="payment_status"]:checked')?.value || 'unpaid',
            payment_method: document.getElementById('order_payment_method').value,
            main_note: document.getElementById('order_main_note').value.trim(),
            summary: {
                subtotal: parseFloat(document.getElementById('sum_subtotal').innerText.replace(/[^\d]/g, '')),
                tax_amount: parseFloat(document.getElementById('sum_tax').innerText.replace(/[^\d]/g, '')),
                total_order_discount: orderDiscountValue,
                grand_total: parseFloat(document.getElementById('sum_final').innerText.replace(/[^\d]/g, ''))
            }
        };

        const urlParams = new URLSearchParams(window.location.search);
        const isCopy = urlParams.get('copy') == '1';
        const cancelOld = urlParams.get('cancel_old') == '1';
        if (isCopy) {
            payload.is_copy = 1;
            if (cancelOld) {
                payload.cancel_old_id = <?php echo $order['id']; ?>;
            }
            delete payload.order_id;
        }

        // Gửi lên Server (Ví dụ: Fetch API)
        console.log("Dữ liệu gửi lên Backend:", payload);

        let actionUrl = 'index.php?action=' + (isCopy ? 'store_online_order' : 'update_order');

        fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    alert(res.msg);
                    window.location.href = res.new_order_id ? ('index.php?action=view_order&id=' + res.new_order_id) : 'index.php?action=view_order&id=<?php echo $order['id']; ?>'; // Chuyển về chi tiết

                } else {
                    alert("Lỗi: " + res.msg);
                }
            }).catch(err => {
                alert("Lỗi hệ thống: " + err);
            });
    }
    // =====================================
    // KHUYẾN MẠI (F6) & THANH TOÁN
    // =====================================
    window.addEventListener('keydown', function(e) {
        if (e.key === 'F6') {
            e.preventDefault();
            openDiscountModal();
        }
    });

    function openDiscountModal() {
        document.getElementById('discount_modal').style.display = 'flex';
        document.getElementById('discount_code_input').focus();
    }

    function applyDiscountAction() {
        let isAuto = document.getElementById('auto_promo_checkbox').checked;
        let code = document.getElementById('discount_code_input').value.trim();

        if (isAuto) {
            orderDiscountValue = 50000; // Giả lập API tự động giảm 50k
            alert("Hệ thống tự động áp dụng chương trình khuyến mại phù hợp nhất: Giảm 50.000đ");
        } else if (code) {
            orderDiscountValue = 30000; // Giả lập API mã giảm 30k
            alert("Áp dụng mã giảm giá " + code + " thành công: Giảm 30.000đ");
        } else {
            orderDiscountValue = 0;
        }
        
        closeModal('discount_modal');
        calculateOrderTotals();
    }

    function togglePaymentAmount() {
        let isPaid = document.querySelector('input[name="payment_status"]:checked').value === 'paid';
        let amountInput = document.getElementById('payment_amount');
        let methodSelect = document.getElementById('payment_method');
        let detailsBlock = document.getElementById('payment_details_block');

        if (isPaid) {
            detailsBlock.style.opacity = '1';
            methodSelect.disabled = false;
            amountInput.disabled = false;
            // Gợi ý điền đầy đủ tiền nếu chưa nhập
            let finalSum = parseFloat(document.getElementById('sum_final').innerText.replace(/[^\d]/g, '')) || 0;
            if(amountInput.value == "0" || parseFloat(amountInput.dataset.lastTotal) === finalSum) {
                amountInput.value = finalSum;
            }
            amountInput.dataset.lastTotal = finalSum;
        } else {
            detailsBlock.style.opacity = '0.5';
            methodSelect.disabled = true;
            amountInput.disabled = true;
            amountInput.value = 0;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
