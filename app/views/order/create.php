<?php

/** * @var array $products
 * @var array $customers
 * @var array $order_sources
 * @var array $employees
 * @var array $branches
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

    /* Báº£ng Sáº£n pháº©m */
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
    <div class="v3-title"><a href="index.php?action=order_list">â†</a> Táº¡o Ä‘Æ¡n hÃ ng má»›i (Online)</div>
</div>

<div class="layout-grid">
    <div class="col-main">
        <div class="v3-card">
            <div class="card-header">
                <span>Chi tiáº¿t sáº£n pháº©m</span>
                <div style="display: flex; gap: 15px; font-weight: normal; font-size: 13px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;"><input type="checkbox" id="cb_separate_line"> TÃ¡ch dÃ²ng</label>
                    <a href="javascript:void(0)" onclick="checkInventory()" style="color: #0088ff; text-decoration: none;">ðŸ” Kiá»ƒm tra tá»“n kho</a>
                </div>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <span class="search-icon">ðŸ”</span>
                    <input type="text" id="product_search" class="has-icon" placeholder="F3 - Nháº­p tÃªn, SKU hoáº·c quÃ©t mÃ£ váº¡ch sáº£n pháº©m...">
                    <div id="product_dropdown" class="dropdown-results"></div>
                </div>
                <table class="table-cart">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Sáº£n pháº©m</th>
                            <th style="width: 15%; text-align: center;">Sá»‘ lÆ°á»£ng</th>
                            <th style="width: 18%; text-align: right;">ÄÆ¡n giÃ¡</th>
                            <th style="width: 17%; text-align: right;">ThÃ nh tiá»n</th>
                            <th style="width: 5%; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody id="cart_body">
                        <tr>
                            <td colspan="5" style="text-align: center; color: #8c98a4; padding: 30px;">ChÆ°a cÃ³ sáº£n pháº©m nÃ o trong Ä‘Æ¡n hÃ ng.</td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 15px;"><button class="btn-outline" style="border: 1px dashed #0088ff; color: #0088ff; background: transparent;" onclick="addCustomService()">+ ThÃªm phÃ­ phá»¥ thu / dá»‹ch vá»¥ tÃ¹y chá»‰nh</button></div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">8. ThÃ´ng tin Ä‘á»‘i tÃ¡c giao hÃ ng & PhÃ­ váº­n chuyá»ƒn</div>
            <div class="card-body">
                <div class="ship-tabs">
                    <div class="ship-tab-item active" onclick="switchShippingTab('carrier', this)">8.1. Qua hÃ£ng váº­n chuyá»ƒn</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('self', this)">8.2. Tá»± giao hÃ ng</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('delivered', this)">8.3. ÄÃ£ giao hÃ ng</div>
                    <div class="ship-tab-item" onclick="switchShippingTab('later', this)">8.4. Giao hÃ ng sau</div>
                </div>

                <div id="ship_block_carrier">
                    <p style="font-size:13px; color:#637381; margin-bottom:12px;">Há»‡ thá»‘ng tá»± Ä‘á»™ng liÃªn káº¿t káº¿t ná»‘i API vá»›i cÃ¡c hÃ£ng tÃ u GHTK, GHN, Viettel Post...</p>
                    <div class="form-group">
                        <label style="font-size:13px; color:#212b36; margin-bottom:5px; display:block;">Chá»n Ä‘á»‘i tÃ¡c váº­n chuyá»ƒn tÃ­ch há»£p</label>
                        <select class="form-control" onchange="setCarrierFee(this.value)">
                            <option value="0">-- Chá»n hÃ£ng váº­n chuyá»ƒn hÃ ng hÃ³a --</option>
                            <option value="30000">Giao HÃ ng Tiáº¿t Kiá»‡m (Dá»± kiáº¿n: 30.000Ä‘)</option>
                            <option value="35000">Giao HÃ ng Nhanh Express (Dá»± kiáº¿n: 35.000Ä‘)</option>
                            <option value="25000">Viettel Post Standard (Dá»± kiáº¿n: 25.000Ä‘)</option>
                        </select>
                    </div>
                </div>

                <div id="ship_block_self" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom:15px;">
                        <div>
                            <label style="font-size:12px; color:#637381;">Äá»‹a chá»‰ láº¥y hÃ ng (Kho xuáº¥t)</label>
                            <select id="self_pickup_address" class="form-control">
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b['branch_name']); ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">Äá»‹a chá»‰ giao khÃ¡ch hÃ ng</label>
                            <input type="text" id="self_delivery_address" class="form-control" placeholder="Tá»± Ä‘á»™ng bá»‘c tá»« thÃ´ng tin khÃ¡ch hÃ ng">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">Tiá»n thu há»™ COD (Ä‘)</label>
                            <input type="number" id="self_cod_amount" class="form-control" value="0">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#637381;">KÃ­ch thÆ°á»›c gÃ³i hÃ ng (DÃ i x Rá»™ng x Cao cm)</label>
                            <div style="display:flex; gap:5px;">
                                <input type="number" placeholder="D" class="form-control" style="padding:6px;" value="20">
                                <input type="number" placeholder="R" class="form-control" style="padding:6px;" value="10">
                                <input type="number" placeholder="C" class="form-control" style="padding:6px;" value="5">
                            </div>
                        </div>
                    </div>

                    <div style="background:#f4f6f8; padding:15px; border-radius:6px; display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">Chá»n Ä‘á»‘i tÃ¡c / Shiper</label>
                            <select id="self_shipper_partner" class="form-control">
                                <option value="Shiper Nguyá»…n VÄƒn A">Shiper Nguyá»…n VÄƒn A (Ná»™i bá»™)</option>
                                <option value="Äá»™i xe Ã´m cÃ´ng nghá»‡">Äá»™i xe cÃ´ng nghá»‡ ngoÃ i</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">NgÆ°á»i tráº£ phÃ­ ship</label>
                            <select id="self_fee_payer" class="form-control" onchange="calculateOrderTotals()">
                                <option value="khach">KhÃ¡ch tráº£ (Cá»™ng vÃ o Ä‘Æ¡n)</option>
                                <option value="shop">Shop tráº£ (Trá»« chi phÃ­ shop)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px; color:#212b36; font-weight:600;">PhÃ­ váº­n chuyá»ƒn thá»±c táº¿ (Ä‘)</label>
                            <input type="number" id="self_ship_fee_input" class="form-control" value="0" oninput="updateSelfShippingFee(this.value)">
                        </div>
                    </div>
                </div>

                <div id="ship_block_delivered" style="display: none;">
                    <div class="form-group">
                        <label style="font-size:13px; color:#637381;">Chá»n hÃ¬nh thá»©c giao hÃ ng trá»±c tiáº¿p</label>
                        <select class="form-control">
                            <option>KhÃ¡ch nháº­n trá»±c tiáº¿p táº¡i cá»­a hÃ ng quáº§y</option>
                            <option>DÃ¹ng Ä‘á»‘i tÃ¡c váº­n chuyá»ƒn ngoÃ i tá»± gá»i tá»± tráº£</option>
                        </select>
                    </div>
                </div>

                <div id="ship_block_later" style="display: none;">
                    <p style="color:#e67e22; font-size:13px; font-weight:500;">ðŸ“¦ Há»‡ thá»‘ng sáº½ Ä‘Ã³ng gÃ³i lÆ°u kho Ä‘Æ¡n hÃ ng á»Ÿ tráº¡ng thÃ¡i NhÃ¡p/Chá» xá»­ lÃ½, chÆ°a xuáº¥t hÃ ng ngay láº­p tá»©c.</p>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">
                <span>7. ThÃ´ng tin HÃ³a Ä‘Æ¡n Ä‘iá»‡n tá»­ (Há»‡ thá»‘ng Invoice)</span>
                <button type="button" class="clickable-text" style="border:none; background:none; font-size:13px;" onclick="openInvoiceModal()">âš™ï¸ ThÃªm/Sá»­a thÃ´ng tin xuáº¥t hoÃ¡ Ä‘Æ¡n</button>
            </div>
            <div class="card-body" id="invoice_summary_box" style="font-size:13px; color:#637381; line-height:1.6;">
                <i>ChÆ°a cáº­p nháº­t thÃ´ng tin xuáº¥t hÃ³a Ä‘Æ¡n Ä‘á» VAT cho Ä‘Æ¡n hÃ ng nÃ y.</i>
            </div>
        </div>
    </div>

    <div class="col-side">
        <div class="v3-card">
            <div class="card-header">KhÃ¡ch hÃ ng</div>
            <div class="card-body">
                <div class="search-box" style="margin-bottom: 0;">
                    <span class="search-icon">ðŸ”</span>
                    <input type="text" id="customer_search" class="has-icon" placeholder="TÃ¬m tÃªn, SÄT khÃ¡ch hÃ ng...">
                    <div id="customer_dropdown" class="dropdown-results"></div>
                </div>
                <div id="selected_customer_box" style="display: none; margin-top: 15px; background: #f4f6f8; padding: 12px; border-radius: 4px; border: 1px solid #dfe3e8;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div style="font-weight: 600; color: #212b36;" id="display_cust_name"></div>
                            <div style="font-size: 13px; color: #637381; margin-top: 4px;" id="display_cust_phone"></div>
                            <div style="font-size: 12px; color: #637381; margin-top: 4px;" id="display_cust_address"></div>
                        </div>
                        <button class="btn-action" onclick="clearSelectedCustomer()">âœ–</button>
                    </div>
                </div>
                <div id="btn_add_cust_block" style="margin-top: 12px; text-align: right;"><button class="clickable-text" style="border:none; background:none; font-size:13px;" onclick="document.getElementById('add_customer_modal').style.display='flex'">+ Táº¡o nhanh khÃ¡ch hÃ ng má»›i</button></div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">3. Nguá»“n Ä‘Æ¡n hÃ ng (Äá»™ng)</div>
            <div class="card-body">
                <select id="order_source" class="form-control">
                    <?php foreach ($order_sources as $src): ?>
                        <option value="<?php echo htmlspecialchars($src['source_name']); ?>" <?php echo $src['source_name'] == 'Admin' ? 'selected' : ''; ?>><?php echo htmlspecialchars($src['source_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">6. ThÃ´ng tin bá»• sung Ä‘Æ¡n hÃ ng</div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.1. BÃ¡n táº¡i chi nhÃ¡nh</label>
                    <select id="order_branch" class="form-control">
                        <?php foreach ($branches as $b): ?>
                            <option value="<?php echo htmlspecialchars($b['branch_name']); ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.2. NhÃ¢n viÃªn phá»¥ trÃ¡ch</label>
                    <select id="order_assignee" class="form-control">
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo htmlspecialchars($emp['full_name']); ?>"><?php echo htmlspecialchars($emp['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.3. NgÃ y Ä‘áº·t hÃ ng (QuÃ¡ khá»©/Hiá»‡n táº¡i)</label>
                    <input type="datetime-local" id="order_date" class="form-control" onchange="validateOrderDate(this.value)">
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.4. NgÃ y háº¹n giao (Hiá»‡n táº¡i/TÆ°Æ¡ng lai)</label>
                    <input type="datetime-local" id="delivery_date" class="form-control" onchange="validateDeliveryDate(this.value)">
                </div>
                <div>
                    <label style="font-size:12px; color:#637381; display:block; margin-bottom:4px;">6.5. Tháº» Tag phÃ¢n loáº¡i (GÃµ chá»¯ rá»“i áº¥n Enter)</label>
                    <input type="text" id="tag_input" class="form-control" placeholder="ThÃªm tag quy trÃ¬nh...">
                    <div class="tag-container" id="tag_box"></div>
                </div>
            </div>
        </div>

        <div class="v3-card" style="background: #fafbfc;">
            <div class="card-body">
                <div class="summary-row" style="font-size:13px;"><span>Tiá»n hÃ ng sau CK:</span> <span id="sum_after_dc" style="font-weight:600;">0 â‚«</span></div>
                <div class="summary-row" style="font-size:13px;"><span>PhÃ­ ship váº­n chuyá»ƒn:</span> <span id="sum_ship_fee" style="font-weight:600;">0 â‚«</span></div>
                <div class="summary-row" style="font-size:15px; margin-top:10px; padding-top:10px; border-top:1px solid #dfe3e8; color:#d82c0d; font-weight:bold;">
                    <span>Tá»•ng KhÃ¡ch Pháº£i Tráº£:</span> <span id="sum_final" style="font-size:18px;">0 â‚«</span>
                </div>
                <div class="v3-card" style="background: #fafbfc;">
                    <div class="card-body">
                        <div class="summary-row" style="font-size:13px;"><span>Tiá»n hÃ ng sau CK:</span> <span id="sum_after_dc" style="font-weight:600;">0 â‚«</span></div>
                        <div class="summary-row" style="font-size:13px;"><span>PhÃ­ ship váº­n chuyá»ƒn:</span> <span id="sum_ship_fee" style="font-weight:600;">0 â‚«</span></div>
                        <div class="summary-row" style="font-size:15px; margin-top:10px; padding-top:10px; border-top:1px solid #dfe3e8; color:#d82c0d; font-weight:bold;">
                            <span>Tá»•ng KhÃ¡ch Pháº£i Tráº£:</span> <span id="sum_final" style="font-size:18px;">0 â‚«</span>
                        </div>

                        <div id="action_buttons_container" style="display: flex; gap: 10px; margin-top: 15px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="invoice_modal">
    <div class="modal-content" style="width:550px;">
        <h3 style="margin-bottom: 15px; color: #212b36; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Popup ThÃ´ng tin xuáº¥t hÃ³a Ä‘Æ¡n Ä‘á» VAT</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
            <div class="form-group" style="grid-column: span 2;">
                <label>MÃ£ sá»‘ thuáº¿ doanh nghiá»‡p <span>*</span></label>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="inv_mst" class="form-control" placeholder="10 hoáº·c 12 sá»‘ chá»¯..." maxlength="14">
                    <button type="button" class="btn-outline" style="color:#0088ff; border-color:#0088ff;" onclick="fetchCompanyInfoByMST()">Láº¥y thÃ´ng tin</button>
                </div>
            </div>
            <div class="form-group" style="grid-column: span 2;"><label>TÃªn cÃ´ng ty Ä‘Æ¡n vá»‹</label><input type="text" id="inv_company" class="form-control"></div>
            <div class="form-group" style="grid-column: span 2;"><label>Äá»‹a chá»‰ cÃ´ng ty Ä‘Äƒng kÃ½</label><input type="text" id="inv_address" class="form-control"></div>
            <div class="form-group"><label>TÃªn ngÆ°á»i mua Ä‘áº¡i diá»‡n</label><input type="text" id="inv_buyer" class="form-control"></div>
            <div class="form-group"><label>Sá»‘ CÄƒn cÆ°á»›c cÃ´ng dÃ¢n (12 sá»‘)</label><input type="text" id="inv_cccd" class="form-control" maxlength="12"></div>
            <div class="form-group"><label>MÃ£ ÄVQH ngÃ¢n sÃ¡ch (7 sá»‘)</label><input type="text" id="inv_qhns" class="form-control" maxlength="7"></div>
            <div class="form-group"><label>Sá»‘ Ä‘iá»‡n thoáº¡i liÃªn há»‡</label><input type="text" id="inv_phone" class="form-control" value="+84 "></div>
            <div class="form-group" style="grid-column: span 2;"><label>Email nháº­n hÃ³a Ä‘Æ¡n Ä‘á»</label><input type="email" id="inv_email" class="form-control" placeholder="VD: info@aakc.com"></div>
        </div>

        <div style="margin-top:15px; background:#f4f6f8; padding:10px; border-radius:4px; font-size:13px; display:flex; flex-direction:column; gap:8px;">
            <label style="cursor:pointer; display:flex; gap:6px; font-weight:normal;"><input type="checkbox" id="chk_no_invoice"> NgÆ°á»i mua khÃ´ng láº¥y hÃ³a Ä‘Æ¡n (Xuáº¥t bÃ¡n láº»)</label>
            <label style="cursor:pointer; display:flex; gap:6px; font-weight:normal;"><input type="checkbox" id="chk_save_default" checked> LÆ°u lÃ m thÃ´ng tin xuáº¥t hÃ³a Ä‘Æ¡n máº·c Ä‘á»‹nh</label>
        </div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal('invoice_modal')">Há»§y bá»</button>
            <button class="btn-primary" onclick="saveInvoiceFormDetails()">XÃ¡c nháº­n lÆ°u</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="add_customer_modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px; color: #0088ff;">ThÃªm má»›i khÃ¡ch hÃ ng há»‡ thá»‘ng</h3>
        <div class="form-group"><label>Há» tÃªn khÃ¡ch hÃ ng <span>*</span></label><input type="text" id="nc_name" class="form-control" required></div>
        <div class="form-group"><label>Sá»‘ Ä‘iá»‡n thoáº¡i <span>*</span></label><input type="text" id="nc_phone" class="form-control" required></div>
        <div class="form-group"><label>Äá»‹a chá»‰ thÆ°á»ng trÃº</label><input type="text" id="nc_address" class="form-control"></div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal('add_customer_modal')">Há»§y</button>
            <button class="btn-primary" onclick="submitQuickCustomerForm()">LÆ°u thÃ´ng tin</button>
        </div>
    </div>
</div>

<script>
    const PRODUCTS = <?php echo isset($products_json) ? $products_json : '[]'; ?>;
    const CUSTOMERS = <?php echo isset($customers_json) ? $customers_json : '[]'; ?>;

    let cart = [];
    let tagsList = [];
    let selectedCustomer = null;
    let invoiceData = null; // LÆ°u cá»¥m thÃ´ng tin VAT hÃ³a Ä‘Æ¡n Ä‘iá»‡n tá»­

    let currentShippingMode = 'carrier'; // carrier, self, delivered, later
    let orderShippingFee = 0; // GiÃ¡ trá»‹ ship Ä‘á»™ng cÃ´ng vÃ  tá»•ng tiá»n khÃ¡ch tráº£

    function formatMoney(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + ' â‚«';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Máº·c Ä‘á»‹nh náº¡p ngÃ y giá» hiá»‡n táº¡i cho Ã´ chá»n thá»i gian Ä‘áº·t hÃ ng khi load trang
    window.addEventListener('DOMContentLoaded', () => {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('order_date').value = now.toISOString().slice(0, 16);
    });

    // =====================================
    // 6. Xá»¬ LÃ KHá»I THÃ”NG TIN Bá»” SUNG (VALIDATIONS)
    // =====================================
    function validateOrderDate(inputVal) {
        if (!inputVal) return;
        let selectedDate = new Date(inputVal);
        let now = new Date();
        if (selectedDate > now) {
            alert("âš ï¸ Lá»—i nghiá»‡p vá»¥ Há»‡ thá»‘ng: KhÃ´ng thá»ƒ chá»n ngÃ y trong tÆ°Æ¡ng lai lÃ m ngÃ y Ä‘áº·t hÃ ng!");
            document.getElementById('order_date').value = now.toISOString().slice(0, 16);
        }
    }

    function validateDeliveryDate(inputVal) {
        if (!inputVal) return;
        let selectedDate = new Date(inputVal);
        let now = new Date();
        // Cáº¯t bá» pháº§n giá» phÃºt Ä‘á»ƒ so sÃ¡nh ngÃ y quÃ¡ khá»© chuáº©n xÃ¡c
        now.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        if (selectedDate < now) {
            alert("âš ï¸ Lá»—i nghiá»‡p vá»¥ Há»‡ thá»‘ng: KhÃ´ng thá»ƒ chá»n ngÃ y háº¹n giao trong quÃ¡ khá»©!");
            document.getElementById('delivery_date').value = "";
        }
    }

    // GÃµ tháº» Tag áº¥n Enter (Má»¥c 6.5)
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
            box.innerHTML += `<span class="tag-badge">ðŸ·ï¸ ${t} <span class="tag-close" onclick="removeTagItem(${i})">Ã—</span></span>`;
        });
    }

    function removeTagItem(index) {
        tagsList.splice(index, 1);
        renderTagsUI();
    }

    // =====================================
    // 7. Xá»¬ LÃ POPUP HÃ“A ÄÆ N ÄIá»†N Tá»¬
    // =====================================
    function openInvoiceModal() {
        // Náº¿u trÆ°á»›c Ä‘Ã³ Ä‘Ã£ chá»n khÃ¡ch hÃ ng vÃ  khÃ¡ch hÃ ng cÃ³ sáºµn dá»¯ liá»‡u thÃ¬ tá»± Ä‘á»™ng Ä‘iá»n (Má»¥c 7)
        if (selectedCustomer && !invoiceData) {
            document.getElementById('inv_buyer').value = selectedCustomer.customer_name;
            document.getElementById('inv_phone').value = "+84 " + selectedCustomer.phone;
            document.getElementById('inv_address').value = selectedCustomer.address || '';
        }
        document.getElementById('invoice_modal').style.display = 'flex';
    }

    // Giáº£ láº­p chá»©c nÄƒng tra cá»©u MST tá»± Ä‘á»™ng cá»§a Há»‡ thá»‘ng
    function fetchCompanyInfoByMST() {
        let mst = document.getElementById('inv_mst').value.trim();
        if (mst.length < 10) {
            alert("MÃ£ sá»‘ thuáº¿ doanh nghiá»‡p pháº£i tá»« 10-12 kÃ½ tá»± sá»‘!");
            return;
        }

        // Giáº£ láº­p báº¯n API tra cá»©u thuáº¿ ná»™i bá»™
        document.getElementById('inv_company').value = "CÃ”NG TY Cá»” PHáº¦N CÃ”NG NGHá»† THÆ¯Æ NG Máº I ÄIá»†N Tá»¬ AAKC";
        document.getElementById('inv_address').value = "Sá»‘ 123 ÄÆ°á»ng Cáº§u Giáº¥y, Quáº­n Cáº§u Giáº¥y, ThÃ nh phá»‘ HÃ  Ná»™i";
        alert("âœ¨ ÄÃ£ liÃªn káº¿t API cÆ¡ sá»Ÿ dá»¯ liá»‡u Tá»•ng cá»¥c Thuáº¿: Láº¥y thÃ´ng tin cÃ´ng ty thÃ nh cÃ´ng!");
    }

    function saveInvoiceFormDetails() {
        let email = document.getElementById('inv_email').value.trim();
        let cccd = document.getElementById('inv_cccd').value.trim();
        let qhns = document.getElementById('inv_qhns').value.trim();

        // Kiá»ƒm tra Ä‘á»‹nh dáº¡ng dá»¯ liá»‡u cháº·t cháº½ theo tÃ i liá»‡u Há»‡ thá»‘ng
        if (email && !email.includes('@')) {
            alert("Äá»‹nh dáº¡ng email nháº­n hÃ³a Ä‘Æ¡n khÃ´ng há»£p lá»‡!");
            return;
        }
        if (cccd && cccd.length !== 12) {
            alert("CÄƒn cÆ°á»›c cÃ´ng dÃ¢n pháº£i nháº­p Ä‘Ãºng Ä‘á»‹nh dáº¡ng 12 kÃ½ tá»± sá»‘!");
            return;
        }
        if (qhns && qhns.length !== 7) {
            alert("MÃ£ Ä‘Æ¡n vá»‹ quan há»‡ ngÃ¢n sÃ¡ch pháº£i nháº­p Ä‘Ãºng Ä‘á»‹nh dáº¡ng 7 kÃ½ tá»± sá»‘!");
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

        // Cáº­p nháº­t tÃ³m táº¯t thÃ´ng tin ra giao diá»‡n chÃ­nh
        let summaryBox = document.getElementById('invoice_summary_box');
        if (invoiceData.no_invoice) {
            summaryBox.innerHTML = "âŒ ÄÆ¡n hÃ ng ghi nháº­n: <b>KhÃ¡ch hÃ ng khÃ´ng láº¥y hÃ³a Ä‘Æ¡n Ä‘á» VAT</b>";
        } else {
            summaryBox.innerHTML = `ðŸ¢ CÃ´ng ty: <b>${invoiceData.company || 'ChÆ°a Ä‘iá»n'}</b><br>ðŸ”¢ MST: ${invoiceData.mst} | ðŸ“§ Email: ${invoiceData.email}`;
        }
        closeModal('invoice_modal');
    }

    // =====================================
    // 8. Há»† THá»NG TAB Váº¬N CHUYá»‚N & PHÃ SHIP
    // =====================================
    function switchShippingTab(mode, btnElement) {
        currentShippingMode = mode;
        document.querySelectorAll('.ship-tab-item').forEach(b => b.classList.remove('active'));
        btnElement.classList.add('active');

        // áº¨n toÃ n bá»™ cÃ¡c khá»‘i subform váº­n chuyá»ƒn
        document.getElementById('ship_block_carrier').style.display = 'none';
        document.getElementById('ship_block_self').style.display = 'none';
        document.getElementById('ship_block_delivered').style.display = 'none';
        document.getElementById('ship_block_later').style.display = 'none';

        // Báº­t Ä‘Ãºng khá»‘i form ngÆ°á»i dÃ¹ng chá»n
        document.getElementById('ship_block_' + mode).style.display = 'block';

        // Äáº·t láº¡i phÃ­ ship tÆ°Æ¡ng á»©ng
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
            // Náº¿u shop tá»± tráº£ phÃ­ ship, phÃ­ ship tÃ­nh toÃ¡n cá»™ng thÃªm vÃ o Ä‘Æ¡n khÃ¡ch hÃ ng báº±ng = 0Ä‘
            orderShippingFee = 0;
            document.getElementById('lbl_shipping_fee').innerText = formatMoney(0) + " (Shop chá»‹u phÃ­)";
        }
        calculateOrderTotals();
    }

    // =====================================
    // LOGIC CORE TÃNH TOÃN TIá»€N CHUáº¨N ÄÃ‰T
    // =====================================
    function calculateOrderTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += (item.price - item.discount) * item.qty;
        });

        // Xá»­ lÃ½ phÃ­ ship tá»± giao hÃ ng theo tÃ¹y chá»n ngÆ°á»i tráº£ phÃ­ (Má»¥c 8.2)
        if (currentShippingMode === 'self') {
            let feeVal = parseFloat(document.getElementById('self_ship_fee_input').value) || 0;
            let payer = document.getElementById('self_fee_payer').value;
            if (payer === 'khach') {
                orderShippingFee = feeVal;
                document.getElementById('lbl_shipping_fee').innerText = formatMoney(orderShippingFee);
            } else {
                orderShippingFee = 0;
                document.getElementById('lbl_shipping_fee').innerText = formatMoney(0) + " (Shop tá»± chi)";
            }
        }

        let isTax = document.getElementById('cb_apply_tax').checked;
        let taxAmount = isTax ? Math.round(subtotal * 0.1) : 0;
        let grandTotal = subtotal + taxAmount + orderShippingFee;

        // Cáº­p nháº­t khá»‘i tÃ³m táº¯t tiá»n phÃ­a dÆ°á»›i gÃ³c pháº£i
        document.getElementById('sum_subtotal').innerText = formatMoney(subtotal);
        document.getElementById('sum_tax').innerText = formatMoney(taxAmount);
        document.getElementById('sum_after_dc').innerText = formatMoney(subtotal);
        document.getElementById('sum_ship_fee').innerText = formatMoney(orderShippingFee);
        document.getElementById('sum_final').innerText = formatMoney(grandTotal);

        // Äá»“ng bá»™ sá»‘ tiá»n thu há»™ COD máº·c Ä‘á»‹nh báº±ng tá»•ng tiá»n Ä‘Æ¡n hÃ ng (Má»¥c 8.2)
        let codInput = document.getElementById('self_cod_amount');
        if (codInput && document.activeElement !== codInput) {
            codInput.value = grandTotal;
        }
    }

    // =====================================
    // CORE CÃC HÃ€M PHá»¤ TRá»¢ (GIá»® NGUYÃŠN BÃ€I CÅ¨)
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
                div.innerHTML = `<div><div class="item-name">${p.product_name}</div><div class="item-sku">${p.sku} | Tá»“n: ${p.stock}</div></div><b>${formatMoney(p.price)}</b>`;
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

    // Chá»n khÃ¡ch hÃ ng vÃ  Ä‘iá»n Ä‘á»‹a chá»‰ giao tá»± Ä‘á»™ng sang Ã´ Ship (Má»¥c 8.1)
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
                div.innerHTML = `<span>ðŸ‘¤ <b>${c.customer_name}</b></span> <small>ðŸ“ž ${c.phone}</small>`;
                div.onmousedown = () => {
                    selectedCustomer = c;
                    document.getElementById('display_cust_name').innerText = c.customer_name;
                    document.getElementById('display_cust_phone').innerText = 'ðŸ“ž ' + c.phone;
                    document.getElementById('display_cust_address').innerText = 'ðŸ“ ' + (c.address || 'ChÆ°a cÃ³ Ä‘á»‹a chá»‰');
                    document.getElementById('self_delivery_address').value = c.address || ''; // Auto fill sang má»¥c 8.2
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
        let name = prompt("Nháº­p tÃªn dá»‹ch vá»¥ tÃ¹y chá»‰nh (Má»¥c 1.4):");
        let price = prompt("Nháº­p Ä‘Æ¡n giÃ¡:");
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
    // HÃ€M RENDER NÃšT Báº¤M Dá»°A VÃ€O TAB GIAO HÃ€NG
    // =====================================
    function renderActionButtons() {
        let container = document.getElementById('action_buttons_container');

        if (currentShippingMode === 'later') {
            // Náº¿u lÃ  "Giao hÃ ng sau" -> Gá»£i Ã½ 1, 2, 3
            container.innerHTML = `
                <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('draft')">LÆ°u nhÃ¡p</button>
                <button class="btn-outline" style="flex: 1; padding: 10px; font-size:13px; color:#0088ff; border-color:#0088ff;" onclick="handleOrderSubmit('create')">Táº¡o Ä‘Æ¡n hÃ ng</button>
                <button class="btn-primary" style="flex: 1.2; padding: 10px; font-size:13px;" onclick="handleOrderSubmit('confirm')">Táº¡o & XÃ¡c nháº­n</button>
            `;
        } else {
            // Náº¿u cÃ³ giao hÃ ng luÃ´n -> Gá»£i Ã½ 4
            container.innerHTML = `
                <button class="btn-primary" style="width: 100%; padding: 12px; font-size:15px;" onclick="handleOrderSubmit('ship')">ðŸš€ Táº¡o Ä‘Æ¡n vÃ  Giao hÃ ng</button>
            `;
        }
    }

    // Gá»i hÃ m render láº§n Ä‘áº§u khi vá»«a má»Ÿ trang
    window.addEventListener('DOMContentLoaded', renderActionButtons);

    // Bá»• sung gá»i hÃ m render má»—i khi chuyá»ƒn Tab giao hÃ ng
    // (KhÆ°Æ¡ng tÃ¬m hÃ m switchShippingTab cÅ© vÃ  THÃŠM dÃ²ng `renderActionButtons();` vÃ o cuá»‘i hÃ m Ä‘Ã³)
    const originalSwitchShippingTab = switchShippingTab;
    switchShippingTab = function(mode, btnElement) {
        originalSwitchShippingTab(mode, btnElement);
        renderActionButtons(); // Cáº­p nháº­t nÃºt ngay khi Ä‘á»•i tab
    }

    // =====================================
    // G. PHÃT Lá»†NH SUBMIT ÄÆ N HÃ€NG Äá»˜NG (CÃ“ CHECK BÃN Ã‚M)
    // =====================================
    function handleOrderSubmit(actionType) {
        if (cart.length === 0) {
            alert("Giá» hÃ ng Ä‘ang trá»‘ng, khÃ´ng thá»ƒ xuáº¥t Ä‘Æ¡n!");
            return;
        }

        // Cáº¢NH BÃO BÃN Ã‚M (TrÆ°á»ng há»£p mua vÆ°á»£t sá»‘ lÆ°á»£ng tá»“n kho)
        let outOfStockItems = cart.filter(item => item.qty > item.stock);
        if (outOfStockItems.length > 0) {
            let msg = "âš ï¸ Cáº¢NH BÃO: Má»˜T Sá» Máº¶T HÃ€NG ÄÃƒ BÃN Háº¾T!\n\n";
            outOfStockItems.forEach(i => {
                msg += `- ${i.product_name} (Tá»“n: ${i.stock} | KhÃ¡ch Ä‘áº·t: ${i.qty})\n`;
            });
            msg += "\nBáº¡n cÃ³ muá»‘n CHO PHÃ‰P BÃN Ã‚M Ä‘á»ƒ tiáº¿p tá»¥c lÃªn Ä‘Æ¡n khÃ´ng?\n(Nháº¥n OK Ä‘á»ƒ bÃ¡n Ã¢m / Nháº¥n Cancel Ä‘á»ƒ Quay láº¡i sá»­a Ä‘Æ¡n)";

            if (!confirm(msg)) {
                return; // Dá»«ng láº¡i, cho ngÆ°á»i dÃ¹ng sá»­a sá»‘ lÆ°á»£ng
            }
        }

        // ÄÃ“NG GÃ“I PAYLOAD Dá»® LIá»†U
        let payload = {
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
            payment_status: document.querySelector('input[name="payment_status"]:checked').value,
            payment_method: document.getElementById('order_payment_method').value,
            main_note: document.getElementById('order_main_note').value.trim(),
            summary: {
                subtotal: parseFloat(document.getElementById('sum_subtotal').innerText.replace(/[^\d]/g, '')),
                tax: parseFloat(document.getElementById('sum_tax').innerText.replace(/[^\d]/g, '')),
                discount: orderDiscountValue,
                grand_total: parseFloat(document.getElementById('sum_final').innerText.replace(/[^\d]/g, ''))
            }
        };

        // Gá»­i lÃªn Server (VÃ­ dá»¥: Fetch API)
        console.log("Dá»¯ liá»‡u gá»­i lÃªn Backend:", payload);

        fetch('index.php?action=store_online_order', {
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
                    window.location.href = 'index.php?action=order_list'; // Chuyá»ƒn vá» danh sÃ¡ch
                } else {
                    alert("Lá»—i: " + res.msg);
                }
            }).catch(err => {
                alert("ÄÃ£ gom dá»¯ liá»‡u thÃ nh cÃ´ng! (Má»Ÿ Console F12 Ä‘á»ƒ xem JSON).\nChá» Backend code hÃ m store_online_order Ä‘á»ƒ lÆ°u DB.");
            });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

