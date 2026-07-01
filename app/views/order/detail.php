<?php

/** @var array $order */
/** @var array $items */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    /* CSS CHUẨN AKC OMNIAI V3 */
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
        padding-bottom: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

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

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .badge-paid {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .badge-pending {
        background: #fff8ea;
        color: #8a6100;
        border: 1px solid #ffea8a;
    }

    .badge-completed {
        background: #f4f6f8;
        color: #637381;
        border: 1px solid #c4cdd5;
    }

    .customer-info {
        font-size: 14px;
        line-height: 1.6;
        color: #212b36;
    }

    .customer-info span {
        color: #0088ff;
        font-weight: 500;
    }

    /* --- BỔ SUNG CSS CHO NÚT THAO TÁC CỦA BẠN --- */
    .btn-outline {
        background: #fff;
        color: #212b36;
        padding: 8px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
    }

    .btn-outline:hover {
        background: #f4f6f8;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-primary:hover {
        background: #0070d2;
    }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=order_list">←</a>
        <?php echo htmlspecialchars($order['order_code']); ?>
        <span style="font-size: 14px; font-weight: normal; color: #637381; margin-left: 10px;">
            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
        </span>

        <?php if (isset($order['is_archived']) && $order['is_archived'] == 1): ?>
            <span style="background: #f4f6f8; color: #637381; font-size: 12px; padding: 4px 8px; border-radius: 4px; border: 1px solid #c4cdd5; margin-left: 10px;">🗃️ Đã lưu trữ</span>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 10px;">
        <?php if (($order['order_status'] ?? '') === 'pending'): ?>
            <button class="btn-primary" style="background: #0088ff;" onclick="confirmOrder(<?php echo $order['id']; ?>)">✔️ Xác nhận đơn hàng</button>
        <?php endif; ?>

        <?php if (($order['order_status'] ?? '') !== 'cancelled' && ($order['shipping_status'] ?? '') !== 'delivered'): ?>
            <button class="btn-outline" style="color: #0088ff; border-color: #0088ff;" onclick="window.location='index.php?action=edit_order&id=<?php echo $order['id']; ?>'">✏️ Sửa đơn</button>
        <?php endif; ?>

        <?php if (($order['shipping_status'] ?? '') === 'delivered' || ($order['order_status'] ?? '') === 'completed'): ?>
            <button class="btn-outline" style="color: #d82c0d; border-color: #d82c0d;" onclick="window.location='index.php?action=create_return&order_id=<?php echo $order['id']; ?>'">↩️ Trả hàng</button>
        <?php endif; ?>

        <div style="position: relative; display: inline-block;">
            <button class="btn-outline" onclick="document.getElementById('more_actions_menu').style.display = document.getElementById('more_actions_menu').style.display == 'block' ? 'none' : 'block'">⚙️ Thao tác khác ▾</button>
            <div id="more_actions_menu" style="display:none; position: absolute; top: 100%; right: 0; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; padding: 5px 0; min-width: 170px; z-index: 100; text-align: left;">
                <a href="javascript:void(0)" onclick="copyOrder(<?php echo $order['id']; ?>)" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">📄 Sao chép đơn</a>
                <a href="javascript:void(0)" onclick="window.open('index.php?action=print_order&id=<?php echo $order['id']; ?>', '_blank')" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">🖨️ In đơn hàng</a>
                
                <?php if (($order['order_status'] ?? '') !== 'cancelled'): ?>
                    <a href="javascript:void(0)" onclick="processCancel(<?php echo $order['id']; ?>)" style="display:block; padding: 8px 15px; color:#d82c0d; text-decoration:none;">❌ Hủy đơn hàng</a>
                <?php endif; ?>

                <?php if (($order['order_status'] ?? '') == 'completed' && (!isset($order['is_archived']) || $order['is_archived'] == 0)): ?>
                    <a href="javascript:void(0)" onclick="archiveOrder(<?php echo $order['id']; ?>)" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">🗃️ Lưu trữ đơn hàng</a>
                <?php endif; ?>
                
                <?php if (($order['order_status'] ?? '') == 'cancelled' || (isset($order['is_archived']) && $order['is_archived'] == 1)): ?>
                    <a href="javascript:void(0)" onclick="deleteOrder(<?php echo $order['id']; ?>)" style="display:block; padding: 8px 15px; color:#d82c0d; text-decoration:none;">🗑️ Xóa đơn hàng</a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Close dropdown when clicking outside -->
        <script>
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-outline');
                if (!btn || !btn.textContent.includes('Thao tác khác')) {
                    const menu = document.getElementById('more_actions_menu');
                    if (menu) menu.style.display = 'none';
                }
            });
        </script>
    </div>
</div>

<div class="pos-layout">
    <div class="pos-left">
        <div class="v3-card" style="padding: 15px 20px;">
            <div class="v3-card" style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 15px;">
                        <?php if (($order['order_status'] ?? '') == 'cancelled'): ?>
                            <span class="badge" style="background:#ffe4e4; color:#d82c0d; border:1px solid #ffb8b8;">❌ Đã hủy</span>
                        <?php elseif (($order['shipping_status'] ?? '') == 'delivered'): ?>
                            <span class="badge badge-paid">🚚 Đã giao hàng</span>
                        <?php else: ?>
                            <span class="badge badge-pending">⏳ Chưa giao hàng</span>
                        <?php endif; ?>

                        <?php if (($order['payment_status'] ?? '') == 'paid'): ?>
                            <span class="badge badge-paid">💰 Đã thanh toán</span>
                        <?php else: ?>
                            <span class="badge badge-pending">🛑 Chưa thanh toán</span>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <?php if (($order['shipping_status'] ?? '') !== 'delivered' && ($order['order_status'] ?? '') !== 'cancelled'): ?>
                            <button type="button" class="btn-primary" id="btn_ship_action" style="width:auto; padding:8px 15px; background:#108043;" onclick="processShipping(<?php echo $order['id']; ?>)">🚀 Xác nhận xuất kho & Giao hàng</button>
                        <?php endif; ?>

                        <?php if (($order['payment_status'] ?? '') !== 'paid' && ($order['order_status'] ?? '') !== 'cancelled'): ?>
                            <button type="button" class="btn-primary" id="btn_pay_action" style="width:auto; padding:8px 15px; background:#e67e22;" onclick="document.getElementById('modal_receive_money').style.display='flex'">💵 Xác nhận thu tiền</button>

                            <button type="button" class="btn-outline" style="width:auto; padding:8px 15px; border-color:#0088ff; color:#0088ff; background: #e5f0ff;" onclick="document.getElementById('online_qr_modal').style.display='flex'">
                                <i class="fa-solid fa-qrcode"></i> Lấy mã QR gửi khách
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="v3-card-title" style="display:flex; justify-content:space-between; align-items:center; position:relative; margin-bottom:15px; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">
                Chi tiết sản phẩm
                <button class="btn-outline" style="padding: 2px 10px; font-size:16px; border:none; color:#637381; margin:0;" onclick="document.getElementById('product_more_actions_menu').style.display = document.getElementById('product_more_actions_menu').style.display == 'block' ? 'none' : 'block'">⋮</button>
                <div id="product_more_actions_menu" style="display:none; position: absolute; top: 100%; right: 0; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; padding: 5px 0; min-width: 220px; z-index: 100; text-align: left; font-weight:normal; font-size:14px;">
                    <a href="javascript:void(0)" onclick="openConfirmMarketplaceModal()" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">🛍️ Xác nhận đơn hàng Sàn</a>
                    <a href="javascript:void(0)" onclick="openChangeBranchModal()" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">🏢 Đổi chi nhánh xử lý</a>
                </div>
            </div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Sản phẩm</th>
                        <th style="width: 15%; text-align: center;">Số lượng</th>
                        <th style="width: 15%; text-align: right;">Đơn giá</th>
                        <th style="width: 20%; text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div style="font-weight:600; color:#0088ff;">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                    <?php if ($item['is_gift'] == 1) echo '<span style="background:#ffea8a; color:#8a6100; font-size:11px; padding:2px 6px; border-radius:4px; margin-left:8px;">🎁 Quà tặng</span>'; ?>
                                </div>
                                <div style="font-size:12px; color:#637381; margin-bottom:4px;">Mã: <?php echo $item['sku'] ?? '---'; ?></div>
                                <div id="note_text_<?php echo $item['id']; ?>" style="font-size:13px; color:#212b36; font-style:italic; margin-bottom: 4px;"><?php echo !empty($item['note']) ? htmlspecialchars($item['note']) : ''; ?></div>
                                <a href="javascript:void(0)" onclick="openProductNoteModal(<?php echo $item['id']; ?>, '<?php echo addslashes($item['note'] ?? ''); ?>')" style="font-size:12px; color:#0088ff; text-decoration:none;"><i class="fa-solid fa-pen"></i> <?php echo empty($item['note']) ? 'Thêm ghi chú' : 'Sửa ghi chú'; ?></a>
                            </td>
                            <td style="text-align:center;"><?php echo $item['qty']; ?></td>
                            <td style="text-align:right;">
                                <?php if ($item['final_price'] < $item['original_price']): ?>
                                    <div style="text-decoration:line-through; color:#8c98a4; font-size:12px;"><?php echo number_format($item['original_price'], 0, '', '.'); ?> ₫</div>
                                <?php endif; ?>
                                <div style="font-weight:500;"><?php echo number_format($item['final_price'], 0, '', '.'); ?> ₫</div>
                            </td>
                            <td style="text-align:right; font-weight:600;"><?php echo number_format($item['line_total'], 0, '', '.'); ?> ₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- NEW BLOCK: Đóng gói và Giao hàng -->
        <div class="v3-card">
            <div class="v3-card-title" style="display:flex; justify-content:space-between; align-items:center;">
                Đóng gói & Giao hàng
            </div>
            <div class="card-body">
                <div style="background:#f4f6f8; padding:15px; border-radius:6px; border:1px solid #dfe3e8; margin-bottom:15px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div>
                            <span style="font-weight:600; color:#212b36;">📦 Kiện hàng 1</span>
                            <span style="color:#637381; font-size:13px; margin-left:10px;">👤 NV Đóng gói: <span id="disp_pkg_staff"><?php echo !empty($order['packaging_staff_id']) ? "Nhân viên #" . $order['packaging_staff_id'] : "Chưa gắn"; ?></span></span>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; position:relative;">
                            <span class="badge" style="background:#e5f0ff; color:#0088ff; border:1px solid #8cc5ff;" id="disp_packaging_status">Chưa xử lý</span>
                            <button class="btn-outline" style="padding: 2px 10px; font-size:16px; border:none; color:#637381; margin:0;" onclick="document.getElementById('pkg_more_actions_menu').style.display = document.getElementById('pkg_more_actions_menu').style.display == 'block' ? 'none' : 'block'">⋮</button>
                            <div id="pkg_more_actions_menu" style="display:none; position: absolute; top: 100%; right: 0; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 4px; padding: 5px 0; min-width: 230px; z-index: 100; text-align: left; font-weight:normal; font-size:14px;">
                                <a href="javascript:void(0)" onclick="openUpdatePackagingStaffModal()" style="display:block; padding: 8px 15px; color:#212b36; text-decoration:none;">👤 Thêm/Cập nhật nhân viên đóng gói</a>
                                <a href="javascript:void(0)" onclick="cancelPackage()" style="display:block; padding: 8px 15px; color:#d82c0d; text-decoration:none;">🚫 Hủy gói hàng</a>
                                <a href="javascript:void(0)" onclick="cancelDelivery()" style="display:block; padding: 8px 15px; color:#d82c0d; text-decoration:none;">❌ Hủy giao hàng</a>
                            </div>
                        </div>
                    </div>
                    <div style="font-size:13px; color:#637381; margin-bottom:15px;">
                        Chưa có thông tin vận đơn.
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;" id="packaging_buttons_container">
                        <button type="button" class="btn-outline" style="color:#212b36;" onclick="requestPackaging()">📦 Yêu cầu đóng gói</button>
                        <button type="button" class="btn-primary" onclick="openPushShippingModal()">🚚 Đẩy qua đối tác vận chuyển</button>
                        <button type="button" class="btn-outline" style="color:#108043; border-color:#108043;" onclick="confirmSelfDelivery()">✔️ Xác nhận đã giao (Tự VC)</button>
                    </div>
                    
                    <div style="display:none; gap:10px; flex-wrap:wrap; margin-top:10px;" id="packaging_active_buttons_container">
                        <button type="button" class="btn-outline" onclick="openChangePackagingStateModal()">🔄 Chuyển trạng thái đóng gói</button>
                        <button type="button" class="btn-outline" onclick="printShippingNote()">🖨️ In phiếu giao hàng</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END BLOCK -->
    </div>

    <div class="pos-right">

        <div class="v3-card">
            <div class="v3-card-title" style="display:flex; justify-content:space-between; align-items:center;">
                Khách hàng
                <a href="javascript:void(0)" onclick="openCustomerModal()" style="font-size:14px; font-weight:normal; color:#0088ff; text-decoration:none;"><i class="fa-solid fa-pen"></i></a>
            </div>
            <div class="customer-info">
                <?php if (!empty($order['customer_name'])): ?>
                    <div style="margin-bottom: 8px;">👤 <span id="disp_cname"><?php echo htmlspecialchars($order['customer_name']); ?></span></div>
                    <div style="margin-bottom: 8px;">📞 <span id="disp_cphone"><?php echo htmlspecialchars($order['phone'] ?? 'Chưa cập nhật'); ?></span></div>
                    <div>📍 <span id="disp_caddress"><?php echo htmlspecialchars($order['address'] ?? 'Chưa cập nhật địa chỉ'); ?></span></div>
                <?php else: ?>
                    <div style="color: #8c98a4; font-style: italic;" id="disp_cname">Khách lẻ (Không lưu thông tin)</div>
                    <div style="display:none;" id="disp_cphone"></div>
                    <div style="display:none;" id="disp_caddress"></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="v3-card">
            <div class="v3-card-title" style="display:flex; justify-content:space-between; align-items:center;">
                Ghi chú
                <a href="javascript:void(0)" onclick="openMainNoteModal()" style="font-size:14px; font-weight:normal; color:#0088ff; text-decoration:none;"><i class="fa-solid fa-pen"></i></a>
            </div>
            <div id="disp_main_note" style="color:#212b36; font-size:14px; white-space:pre-wrap;"><?php echo !empty($order['main_note']) ? htmlspecialchars($order['main_note']) : '<span style="color:#8c98a4; font-style:italic;">Không có ghi chú</span>'; ?></div>
        </div>

        <div class="v3-card">
            <div class="v3-card-title">Thông tin bổ sung</div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <div style="color:#637381; font-size:14px;">Nhân viên phụ trách</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <?php
                        $staff_name = '---';
                        if (!empty($order['assigned_staff_id'])) {
                            // Find staff name from somewhere, maybe we can just show ID for now or fetch later
                            $staff_name = "Nhân viên #" . $order['assigned_staff_id'];
                        }
                    ?>
                    <span id="disp_staff_name" style="font-weight:500; font-size:14px;"><?php echo $staff_name; ?></span>
                    <a href="javascript:void(0)" onclick="openStaffModal()" style="color:#0088ff;"><i class="fa-solid fa-pen"></i></a>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <div style="color:#637381; font-size:14px;">Ngày hẹn giao</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span id="disp_delivery_date" style="font-weight:500; font-size:14px;"><?php echo !empty($order['delivery_date']) ? date('d/m/Y H:i', strtotime($order['delivery_date'])) : '---'; ?></span>
                    <a href="javascript:void(0)" onclick="openDeliveryDateModal()" style="color:#0088ff;"><i class="fa-solid fa-pen"></i></a>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="color:#637381; font-size:14px;">Danh sách tag</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span id="disp_tags" style="font-weight:500; font-size:14px; color:#108043;"><?php echo !empty($order['tags']) ? htmlspecialchars($order['tags']) : '---'; ?></span>
                    <a href="javascript:void(0)" onclick="openTagsModal()" style="color:#0088ff;"><i class="fa-solid fa-pen"></i></a>
                </div>
            </div>
        </div>

        <div class="v3-card" style="background: #f4f6f8; border: none;">
            <div class="v3-card-title">Thanh toán</div>

            <div class="summary-line">
                <span>Tổng tiền hàng</span>
                <span><?php echo number_format($order['subtotal'] ?? 0, 0, '', '.'); ?> ₫</span>
            </div>

            <?php
            $total_discount = ($order['total_product_discount'] ?? 0) + ($order['total_order_discount'] ?? 0);
            if ($total_discount > 0):
            ?>
                <div class="summary-line" style="color: #108043;">
                    <span>Chiết khấu</span>
                    <span>-<?php echo number_format($total_discount, 0, '', '.'); ?> ₫</span>
                </div>
            <?php endif; ?>

            <div class="summary-line">
                <span>Phí giao hàng</span>
                <span><?php echo number_format(($order['original_shipping_fee'] ?? 0) - ($order['total_shipping_discount'] ?? 0), 0, '', '.'); ?> ₫</span>
            </div>

            <div class="summary-total">
                <span>Khách phải trả</span>
                <span><?php echo number_format($order['grand_total'] ?? 0, 0, '', '.'); ?> ₫</span>
            </div>

            <div class="summary-line" style="margin-top: 15px; border-top: 1px dashed #c4cdd5; padding-top: 15px;">
                <span style="font-weight: 500;">Đã thanh toán</span>
                <span style="font-weight: 500; color: #108043;"><?php echo number_format($order['amount_paid'] ?? 0, 0, '', '.'); ?> ₫</span>
            </div>
        </div>

        <!-- HÓA ĐƠN ĐIỆN TỬ -->
        <div class="v3-card">
            <div class="v3-card-title">Hóa đơn điện tử</div>
            
            <?php 
                $invoice_status = $order['invoice_status'] ?? 'not_requested';
                if ($invoice_status == 'not_requested'): 
            ?>
                <div style="color: #637381; font-size: 14px; margin-bottom: 10px;">Chưa yêu cầu hóa đơn điện tử</div>
                <button class="btn-outline" onclick="openInvoiceModal()">📝 Yêu cầu hóa đơn</button>
            <?php else: ?>
                <?php if ($invoice_status == 'pending_issue' || $invoice_status == 'requested'): ?>
                    <div style="margin-bottom: 15px;">
                        <span class="badge" style="background: #fff8ea; color: #8a6100; border: 1px solid #ffea8a;">⏳ Chờ phát hành</span>
                    </div>
                    <div style="font-size: 14px; color: #212b36; margin-bottom: 15px; line-height: 1.6;">
                        <strong>Thông tin xuất hóa đơn:</strong><br>
                        MST: <?php echo htmlspecialchars($order['invoice_tax_code']); ?><br>
                        Đơn vị: <?php echo htmlspecialchars($order['invoice_company_name']); ?><br>
                        Người mua: <?php echo htmlspecialchars($order['invoice_buyer_name']); ?><br>
                        Email: <?php echo htmlspecialchars($order['invoice_email']); ?>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-primary" onclick="issueInvoice(<?php echo $order['id']; ?>)">🚀 Tạo & Phát hành hóa đơn</button>
                        <button class="btn-outline" onclick="openInvoiceModal()">✏️ Sửa thông tin</button>
                    </div>
                <?php elseif ($invoice_status == 'issued'): ?>
                    <div style="margin-bottom: 15px;">
                        <span class="badge" style="background: #eafff0; color: #108043; border: 1px solid #8ce09f;">✅ Đã phát hành & Cấp mã</span>
                    </div>
                    <div style="font-size: 14px; color: #212b36; line-height: 1.6;">
                        <div>Ký hiệu: <strong><?php echo htmlspecialchars($order['invoice_symbol']); ?></strong></div>
                        <div>Số HĐ: <strong style="color: #0088ff;"><?php echo htmlspecialchars($order['invoice_number']); ?></strong></div>
                        <div>Mã CQT: <strong><?php echo htmlspecialchars($order['invoice_cqt_code']); ?></strong></div>
                        <div>Mã tra cứu: <strong><?php echo htmlspecialchars($order['invoice_lookup_code']); ?></strong></div>
                        <div style="color: #637381; font-size: 12px; margin-top: 5px;">Ngày phát hành: <?php echo date('d/m/Y H:i', strtotime($order['invoice_date'])); ?></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="modal-overlay" id="online_qr_modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 450px; padding: 25px; border-radius: 8px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 15px; color: #212b36;">Thanh Toán Mã QR</h3>
        
        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
            <button class="btn-outline" id="tab_qr_pro" style="background: #e5f0ff; color: #0088ff; border-color: #0088ff;" onclick="switchQRTab('pro')">VietQR Pro</button>
            <button class="btn-outline" id="tab_qr_normal" onclick="switchQRTab('normal')">Mã thường</button>
        </div>

        <div id="qr_pro_content">
            <?php
            // Logic PHP: Lấy config MBBank từ Database đổ ra đây
            $mb_config = [];
            if (isset($payment_methods)) {
                foreach ($payment_methods as $pm) {
                    if ($pm['code'] == 'vietqr' && $pm['is_active']) {
                        $mb_config = json_decode($pm['config_data'], true);
                        break;
                    }
                }
            }
            ?>

            <?php if (empty($mb_config)): ?>
                <div style="padding: 20px; background: #fff1f0; border: 1px solid #ffa39e; border-radius: 8px; margin-bottom: 15px;">
                    <p style="color: #d82c0d; font-weight: bold; margin-bottom: 10px;">⚠️ Lỗi Cấu hình</p>
                    <p style="color: #cf1322; font-size: 13px;">Cửa hàng chưa cấu hình tài khoản VietQR Pro! Vui lòng vào <b>Cấu hình > Phương thức thanh toán</b> để thiết lập.</p>
                </div>
            <?php else: ?>
                <?php
                $bank_code = $mb_config['bank_code'] ?? 'MB';
                $acc_no = $mb_config['account_no'] ?? '';
                $acc_name = urlencode($mb_config['fullname'] ?? '');
                $amount = $order['grand_total'] - $order['amount_paid'];
                $amount = $amount > 0 ? $amount : 0;
                $desc = urlencode("Thanh toan don " . ($order['order_code'] ?? $order['id']));

                // Nối chuỗi API của vietqr.io
                $qr_url = "https://img.vietqr.io/image/{$bank_code}-{$acc_no}-compact.png?amount={$amount}&addInfo={$desc}&accountName={$acc_name}";
                ?>
                <img id="online_qr_img_pro" src="<?php echo $qr_url; ?>" style="width: 250px; border-radius: 8px; border: 1px solid #dfe3e8; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                <div style="font-size: 14px; color: #637381; margin-bottom: 5px;">Số tiền thanh toán:</div>
                <h2 style="color: #0088ff; margin-bottom: 20px; font-size: 28px;"><?php echo number_format($amount, 0, ',', '.'); ?> ₫</h2>
            <?php endif; ?>
        </div>

        <div id="qr_normal_content" style="display: none; text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:500;">Chọn tài khoản nhận tiền:</label>
                <select id="qr_bank_account" class="form-control" onchange="generateNormalQR()">
                    <?php foreach ($bank_accounts as $acc): ?>
                        <option value="<?php echo htmlspecialchars($acc['bank_name'] . '|' . $acc['account_number'] . '|' . $acc['account_name']); ?>">
                            <?php echo htmlspecialchars($acc['bank_name'] . ' - ' . $acc['account_number']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:500;">Số tiền thanh toán:</label>
                <input type="number" id="qr_normal_amount" class="form-control" value="<?php echo max(0, $order['grand_total'] - $order['amount_paid']); ?>" oninput="generateNormalQR()">
            </div>
            
            <div style="text-align: center;">
                <img id="online_qr_img_normal" src="" style="width: 250px; border-radius: 8px; border: 1px solid #dfe3e8; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: none;">
            </div>
        </div>
        
        <button class="btn-primary" style="width: 100%; padding: 12px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="copyQRImage()">
            <i class="fa-solid fa-copy"></i> Sao chép ảnh QR
        </button>
        <div style="font-size: 12px; color: #8c98a4; margin-bottom: 15px;">(Dán ảnh vào Zalo / Messenger để gửi cho khách)</div>

        <button class="btn-outline" style="width: 100%; padding: 10px; display: flex; align-items: center; justify-content: center;" onclick="document.getElementById('online_qr_modal').style.display='none'">Đóng</button>
    </div>
</div>

<div class="modal-overlay" id="modal_receive_money" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 400px; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 20px; color: #212b36; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px;">Xác nhận thu tiền</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500; font-size:14px;">Số tiền thanh toán</label>
            <input type="number" id="pay_amount" class="form-control" value="<?php echo max(0, $order['grand_total'] - $order['amount_paid']); ?>">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500; font-size:14px;">Phương thức thanh toán</label>
            <select id="pay_method" class="form-control">
                <option value="cash">Tiền mặt</option>
                <option value="transfer">Chuyển khoản</option>
                <option value="cod">COD</option>
            </select>
        </div>
        
        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:5px; font-weight:500; font-size:14px;">Mã tham chiếu (Tùy chọn)</label>
            <input type="text" id="pay_reference" class="form-control" placeholder="Ví dụ: Mã giao dịch ngân hàng">
        </div>
        
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" class="btn-outline" onclick="document.getElementById('modal_receive_money').style.display='none'">Hủy</button>
            <button type="button" class="btn-primary" onclick="submitPayment(<?php echo $order['id']; ?>)">Xác nhận nhận tiền</button>
        </div>
    </div>
</div>

<script>
    // Lệnh AJAX xử lý xuất kho giao hàng (Giữ nguyên)
    function processShipping(orderId) {
        if (!confirm('Xác nhận xuất kho và chuyển trạng thái đơn hàng thành ĐÃ GIAO HÀNG? Hệ thống sẽ tự động trừ số lượng tồn kho sản phẩm!')) return;

        document.getElementById('btn_ship_action').disabled = true;

        fetch('index.php?action=update_order_ship', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: 'delivered'
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }

    function submitPayment(orderId) {
        let amount = document.getElementById('pay_amount').value;
        let method = document.getElementById('pay_method').value;
        let ref = document.getElementById('pay_reference').value;
        
        if (amount <= 0) {
            alert('Vui lòng nhập số tiền hợp lệ!');
            return;
        }

        fetch('index.php?action=collect_order_pay', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    amount: amount,
                    payment_method: method,
                    reference: ref
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }

    function confirmOrder(orderId) {
        if (!confirm('Xác nhận đơn hàng này để chuyển sang Đang giao dịch?')) return;

        fetch('index.php?action=confirm_order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }
    
    // JS for QR code tabs
    let currentQRTab = 'pro';
    function switchQRTab(tab) {
        currentQRTab = tab;
        if (tab === 'pro') {
            document.getElementById('tab_qr_pro').style.background = '#e5f0ff';
            document.getElementById('tab_qr_pro').style.color = '#0088ff';
            document.getElementById('tab_qr_pro').style.borderColor = '#0088ff';
            document.getElementById('tab_qr_normal').style.background = '#fff';
            document.getElementById('tab_qr_normal').style.color = '#212b36';
            document.getElementById('tab_qr_normal').style.borderColor = '#c4cdd5';
            
            document.getElementById('qr_pro_content').style.display = 'block';
            document.getElementById('qr_normal_content').style.display = 'none';
        } else {
            document.getElementById('tab_qr_normal').style.background = '#e5f0ff';
            document.getElementById('tab_qr_normal').style.color = '#0088ff';
            document.getElementById('tab_qr_normal').style.borderColor = '#0088ff';
            document.getElementById('tab_qr_pro').style.background = '#fff';
            document.getElementById('tab_qr_pro').style.color = '#212b36';
            document.getElementById('tab_qr_pro').style.borderColor = '#c4cdd5';
            
            document.getElementById('qr_pro_content').style.display = 'none';
            document.getElementById('qr_normal_content').style.display = 'block';
            generateNormalQR();
        }
    }
    
    function generateNormalQR() {
        let select = document.getElementById('qr_bank_account');
        if(!select || !select.value) return;
        
        let parts = select.value.split('|');
        let bankName = parts[0];
        let accNo = parts[1];
        let accName = parts[2];
        let amount = document.getElementById('qr_normal_amount').value;
        let desc = "Thanh toan don <?php echo $order['order_code'] ?? $order['id']; ?>";
        
        if (amount > 0) {
            let url = `https://img.vietqr.io/image/${bankName}-${accNo}-compact.png?amount=${amount}&addInfo=${encodeURIComponent(desc)}&accountName=${encodeURIComponent(accName)}`;
            let img = document.getElementById('online_qr_img_normal');
            img.src = url;
            img.style.display = 'inline-block';
        } else {
            document.getElementById('online_qr_img_normal').style.display = 'none';
        }
    }
    
    function copyQRImage() {
        let imgId = currentQRTab === 'pro' ? 'online_qr_img_pro' : 'online_qr_img_normal';
        let img = document.getElementById(imgId);
        
        if(!img || img.style.display === 'none') {
            alert('Không có mã QR để copy!');
            return;
        }

        // Tạo canvas
        let canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        let ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, img.width, img.height);
        
        canvas.toBlob(function(blob) {
            const item = new ClipboardItem({ "image/png": blob });
            navigator.clipboard.write([item]).then(function() {
                alert('Đã copy ảnh mã QR! Bạn có thể dán (Ctrl+V) vào Zalo/Messenger.');
            }, function(error) {
                alert('Lỗi copy ảnh: ' + error);
            });
        });
    }

    function copyOrder(orderId) {
        if (!confirm('Bạn có muốn sao chép đơn hàng này?')) return;
        let cancelOld = confirm('Bạn có muốn TỰ ĐỘNG HỦY đơn hàng hiện tại sau khi sao chép không?');
        window.location.href = `index.php?action=edit_order&id=${orderId}&copy=1&cancel_old=${cancelOld ? 1 : 0}`;
    }

    function processCancel(orderId) {
        if (!confirm('⚠️ BẠN CÓ CHẮC CHẮN MUỐN HỦY ĐƠN HÀNG NÀY KHÔNG?\n\nNếu đơn đã xuất kho, hệ thống sẽ tự động hoàn trả số lượng lại vào kho.')) return;

        fetch('index.php?action=cancel_order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: orderId })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }

    function archiveOrder(orderId) {
        if (!confirm('Bạn có chắc chắn muốn cất gọn đơn hàng này vào Kho Lưu Trữ?')) return;
        window.location.href = 'index.php?action=archive_order&id=' + orderId;
    }

    function deleteOrder(orderId) {
        if (!confirm('⚠️ XÓA VĨNH VIỄN: Đơn hàng sẽ bị xóa hoàn toàn khỏi hệ thống, thao tác không thể hoàn tác!\nBạn có chắc chắn muốn xóa?')) return;
        
        fetch('index.php?action=delete_order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: orderId })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.href = 'index.php?action=order_list';
            });
    }

    // Modal UI functions
    function updateOrderMeta(data) {
        data.order_id = <?php echo $order['id']; ?>;
        fetch('index.php?action=update_order_meta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(res => res.json()).then(res => {
            if(res.status == 'success') {
                window.location.reload();
            } else {
                alert(res.msg);
            }
        });
    }

    function openProductNoteModal(itemId, currentNote) {
        let note = prompt('Nhập ghi chú cho sản phẩm (Tối đa 255 ký tự):', currentNote);
        if (note !== null) {
            updateOrderMeta({ action_type: 'product_note', item_id: itemId, note: note });
        }
    }

    function openMainNoteModal() {
        let note = prompt('Nhập ghi chú đơn hàng:', document.getElementById('disp_main_note').innerText);
        if (note !== null) {
            updateOrderMeta({ action_type: 'main_note', note: note });
        }
    }

    function openStaffModal() {
        let staff = prompt('Nhập ID hoặc Tên nhân viên phụ trách:', document.getElementById('disp_staff_name').innerText);
        if (staff !== null) {
            updateOrderMeta({ action_type: 'assigned_staff', staff_id: staff });
        }
    }

    function openDeliveryDateModal() {
        let date = prompt('Nhập ngày hẹn giao (YYYY-MM-DD HH:MM):', '');
        if (date !== null) {
            updateOrderMeta({ action_type: 'delivery_date', date: date });
        }
    }

    function openTagsModal() {
        let tags = prompt('Nhập tags (cách nhau bởi dấu phẩy):', document.getElementById('disp_tags').innerText);
        if (tags !== null) {
            updateOrderMeta({ action_type: 'tags', tags: tags });
        }
    }

    function openCustomerModal() {
        let cname = prompt('Tên Khách Hàng:', document.getElementById('disp_cname').innerText);
        if (cname !== null) {
            let phone = prompt('SĐT:', document.getElementById('disp_cphone').innerText);
            let address = prompt('Địa chỉ:', document.getElementById('disp_caddress').innerText);
            let cid = <?php echo $order['customer_id'] ?? 0; ?>;
            let update_profile = confirm('Bạn có muốn cập nhật thông tin này vào hồ sơ khách hàng không?');
            updateOrderMeta({ 
                action_type: 'customer_info', 
                customer_id: cid,
                customer_name: cname,
                phone: phone,
                address: address,
                update_profile: update_profile ? 1 : 0
            });
        }
    }

    function openConfirmMarketplaceModal() {
        document.getElementById('marketplace_modal').style.display = 'flex';
    }

    function openChangeBranchModal() {
        document.getElementById('change_branch_modal').style.display = 'flex';
    }

    function requestPackaging() {
        if(!confirm('Xác nhận yêu cầu đóng gói cho đơn hàng này? Hệ thống sẽ tạo mã kiện hàng mới.')) return;
        
        fetch('index.php?action=mock_action', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'request_packaging', order_id: <?php echo $order['id']; ?> })
        }).then(res => res.json()).then(res => {
            alert("Đã yêu cầu đóng gói thành công. Trạng thái kiện hàng: Chờ đóng gói.");
            document.getElementById('disp_packaging_status').innerText = 'Chờ đóng gói';
            document.getElementById('disp_packaging_status').style.background = '#fff8ea';
            document.getElementById('disp_packaging_status').style.color = '#8a6100';
            document.getElementById('disp_packaging_status').style.borderColor = '#ffea8a';
            document.getElementById('packaging_buttons_container').style.display = 'none';
            document.getElementById('packaging_active_buttons_container').style.display = 'flex';
        });
    }

    function openPushShippingModal() {
        document.getElementById('push_shipping_modal').style.display = 'flex';
    }

    function openChangePackagingStateModal() {
        document.getElementById('change_packaging_state_modal').style.display = 'flex';
    }

    function printShippingNote() {
        window.open('index.php?action=print_order&id=<?php echo $order['id']; ?>', '_blank');
    }

    function confirmSelfDelivery() {
        if(!confirm('Xác nhận cập nhật trạng thái đơn này thành "Đã giao hàng"? (Dành cho tự vận chuyển)')) return;
        fetch('index.php?action=mock_action', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'confirm_self_delivery', order_id: <?php echo $order['id']; ?> })
        }).then(res => res.json()).then(res => {
            alert("Đã cập nhật trạng thái đơn thành: Đã giao hàng.");
            window.location.reload();
        });
    }

    function openUpdatePackagingStaffModal() {
        document.getElementById('update_packaging_staff_modal').style.display = 'flex';
    }

    function cancelPackage() {
        if(!confirm('Bạn có chắc chắn muốn hủy gói hàng này? Thao tác này sẽ đưa kiện hàng về trạng thái chưa chuẩn bị.')) return;
        submitMockAction('cancel_package', {}, null);
    }

    function cancelDelivery() {
        if(!confirm('Xác nhận hủy giao hàng? Đơn hàng sẽ bị hủy đẩy qua Đối tác vận chuyển.')) return;
        submitMockAction('cancel_delivery', {}, null);
    }

    function submitMockAction(actionName, payload, modalId) {
        payload.action = actionName;
        payload.order_id = <?php echo $order['id']; ?>;
        fetch('index.php?action=mock_action', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        }).then(res => res.json()).then(res => {
            alert("Xử lý thành công!");
            if(modalId) document.getElementById(modalId).style.display = 'none';
            window.location.reload();
        });
    }
</script>

<!-- New Modal for Packaging Staff -->
<div class="modal-overlay" id="update_packaging_staff_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:400px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Cập nhật nhân viên đóng gói</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px;">Chọn nhân viên đóng gói</label>
            <select class="form-control" style="width:100%; padding:8px;" id="new_packaging_staff_id">
                <option value="1">Nhân viên #1 (Nguyễn Văn A)</option>
                <option value="2">Nhân viên #2 (Trần Thị B)</option>
                <option value="3">Nhân viên #3 (Lê Văn C)</option>
            </select>
        </div>
        <div style="text-align:right;">
            <button class="btn-outline" onclick="document.getElementById('update_packaging_staff_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitMockAction('update_packaging_staff', {staff_id: document.getElementById('new_packaging_staff_id').value}, 'update_packaging_staff_modal')">Xác nhận</button>
        </div>
    </div>
</div>


<!-- Modals -->
<div class="modal-overlay" id="marketplace_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:400px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Xác nhận đơn hàng Sàn</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px;">Phương thức giao hàng</label>
            <select class="form-control" style="width:100%; padding:8px;" id="mp_shipping_method">
                <option value="pickup">Đơn vị vận chuyển tới lấy hàng</option>
                <option value="dropoff">Gửi hàng tại bưu cục</option>
            </select>
        </div>
        <div style="text-align:right;">
            <button class="btn-outline" onclick="document.getElementById('marketplace_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitMockAction('confirm_marketplace', {method: document.getElementById('mp_shipping_method').value}, 'marketplace_modal')">Xác nhận</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="change_branch_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:400px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Đổi chi nhánh xử lý đơn hàng</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px;">Chọn chi nhánh mới</label>
            <select class="form-control" style="width:100%; padding:8px;" id="new_branch_select">
                <option value="Chi nhánh Trung tâm">Chi nhánh Trung tâm</option>
                <option value="Chi nhánh Quận 1">Chi nhánh Quận 1</option>
                <option value="Kho tổng">Kho tổng</option>
            </select>
        </div>
        <div style="text-align:right;">
            <button class="btn-outline" onclick="document.getElementById('change_branch_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitMockAction('change_branch', {branch: document.getElementById('new_branch_select').value}, 'change_branch_modal')">Lưu</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="change_packaging_state_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:400px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Chuyển trạng thái đóng gói</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px;">Chọn trạng thái mới</label>
            <select class="form-control" style="width:100%; padding:8px;" id="new_pkg_state">
                <option value="Chờ đóng gói">Chờ đóng gói</option>
                <option value="Chờ dán phiếu giao hàng">Chờ dán phiếu giao hàng</option>
                <option value="Đã đóng gói">Đã đóng gói</option>
            </select>
        </div>
        <div style="text-align:right;">
            <button class="btn-outline" onclick="document.getElementById('change_packaging_state_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitMockAction('change_pkg_state', {state: document.getElementById('new_pkg_state').value}, 'change_packaging_state_modal')">Xác nhận</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="push_shipping_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:800px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Đẩy qua đối tác vận chuyển</h3>
        <div style="display:flex; gap:20px;">
            <div style="flex:1;">
                <h4 style="border-bottom:1px solid #dfe3e8; padding-bottom:5px;">Thông tin gói hàng</h4>
                <div class="form-group" style="margin-bottom:10px;"><label>Tiền thu hộ (COD)</label><input type="text" class="form-control" value="0"></div>
                <div class="form-group" style="margin-bottom:10px;"><label>Khối lượng (gram)</label><input type="number" class="form-control" value="500"></div>
                <div class="form-group" style="margin-bottom:10px;"><label>Kích thước (D x R x C cm)</label><input type="text" class="form-control" value="10x10x5"></div>
                <div class="form-group" style="margin-bottom:10px;"><label>Yêu cầu giao hàng</label>
                    <select class="form-control" style="width:100%;"><option>Cho xem hàng không thử</option><option>Không cho xem hàng</option></select>
                </div>
            </div>
            <div style="flex:1;">
                <h4 style="border-bottom:1px solid #dfe3e8; padding-bottom:5px;">Hình thức đẩy đơn</h4>
                <div class="form-group" style="margin-bottom:10px;"><label>Chọn đối tác</label>
                    <select class="form-control" style="width:100%;">
                        <option>Giao Hàng Nhanh (GHN)</option>
                        <option>Viettel Post</option>
                        <option>Shipper nội bộ (Tự liên hệ)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:10px;"><label>Người trả phí</label>
                    <select class="form-control" style="width:100%;"><option>Khách trả</option><option>Shop trả</option></select>
                </div>
            </div>
        </div>
        <div style="text-align:right; margin-top:20px;">
            <button class="btn-outline" onclick="document.getElementById('push_shipping_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="submitMockAction('push_shipping', {}, 'push_shipping_modal')">Gửi yêu cầu vận chuyển</button>
        </div>
    </div>
</div>
<!-- Hóa đơn Modal -->
<div class="modal-overlay" id="invoice_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:600px; padding:20px; border-radius:8px;">
        <h3 style="margin-top:0; color:#0088ff;">Thông tin xuất hóa đơn điện tử</h3>
        
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Mã số thuế</label>
            <div style="display:flex; gap:10px;">
                <input type="text" id="inv_tax_code" class="form-control" style="flex:1;" value="<?php echo htmlspecialchars($order['invoice_tax_code'] ?? ''); ?>" placeholder="Nhập MST (nếu có)">
                <button class="btn-outline" onclick="mockFetchCompanyInfo()">Lấy thông tin</button>
            </div>
        </div>

        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Tên công ty / Đơn vị</label>
            <input type="text" id="inv_company" class="form-control" style="width:100%;" value="<?php echo htmlspecialchars($order['invoice_company_name'] ?? ''); ?>">
        </div>

        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Địa chỉ</label>
            <input type="text" id="inv_address" class="form-control" style="width:100%;" value="<?php echo htmlspecialchars($order['invoice_address'] ?? ''); ?>">
        </div>

        <div style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Tên người mua</label>
                <input type="text" id="inv_buyer" class="form-control" style="width:100%;" value="<?php echo htmlspecialchars($order['invoice_buyer_name'] ?? $order['customer_name']); ?>">
            </div>
            <div class="form-group" style="flex:1;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Số điện thoại</label>
                <input type="text" id="inv_phone" class="form-control" style="width:100%;" value="<?php echo htmlspecialchars($order['invoice_phone'] ?? $order['phone']); ?>">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Email nhận hóa đơn *</label>
            <input type="email" id="inv_email" class="form-control" style="width:100%;" value="<?php echo htmlspecialchars($order['invoice_email'] ?? ''); ?>">
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" id="inv_no_receipt" <?php echo ($order['invoice_no_receipt'] ?? 0) ? 'checked' : ''; ?>>
                <span>Người mua không lấy hóa đơn</span>
            </label>
        </div>

        <div style="text-align:right;">
            <button class="btn-outline" onclick="document.getElementById('invoice_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="requestInvoice()">Xác nhận lưu</button>
        </div>
    </div>
</div>

<script>
    function openInvoiceModal() {
        document.getElementById('invoice_modal').style.display = 'flex';
    }

    function mockFetchCompanyInfo() {
        let taxCode = document.getElementById('inv_tax_code').value.trim();
        if (taxCode.length < 10) {
            alert('Mã số thuế không hợp lệ');
            return;
        }
        // Giả lập điền tự động
        document.getElementById('inv_company').value = "CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ GIẢ LẬP";
        document.getElementById('inv_address').value = "Số 1, Đường Lê Duẩn, Phường Bến Nghé, Quận 1, TP HCM";
    }

    function requestInvoice() {
        let payload = {
            order_id: <?php echo $order['id']; ?>,
            tax_code: document.getElementById('inv_tax_code').value,
            company_name: document.getElementById('inv_company').value,
            address: document.getElementById('inv_address').value,
            buyer_name: document.getElementById('inv_buyer').value,
            phone: document.getElementById('inv_phone').value,
            email: document.getElementById('inv_email').value,
            no_receipt: document.getElementById('inv_no_receipt').checked ? 1 : 0
        };

        if (!payload.no_receipt && !payload.email) {
            alert("Vui lòng nhập Email nhận hóa đơn!");
            return;
        }

        fetch('index.php?action=request_invoice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Lưu thông tin thành công!");
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function issueInvoice(orderId) {
        if (!confirm("Bạn có chắc chắn muốn phát hành hóa đơn điện tử cho đơn hàng này? Thao tác này sẽ gửi dữ liệu lên Cơ quan Thuế.")) return;

        fetch('index.php?action=issue_invoice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, symbol: '1C24TML' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
