<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$branches = $branches ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$provinces = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Bắc Ninh', 'Bình Dương'];

$promo_apply = isset($promo['product_apply_settings']) ? json_decode($promo['product_apply_settings'], true) : [];
$promo_gift = isset($promo['gift_settings']) ? json_decode($promo['gift_settings'], true) : [];
$promo_ship = isset($promo['shipping_settings']) ? json_decode($promo['shipping_settings'], true) : [];
$channels = isset($promo['sales_channels']) ? json_decode($promo['sales_channels'], true) : ['pos', 'web'];
$combinations = isset($promo['allowed_combinations']) ? json_decode($promo['allowed_combinations'], true) : [];
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 20px;
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
        line-height: 1;
        margin-top: -4px;
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
        font-size: 15px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
        outline: none;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    .radio-box,
    .check-box {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
        margin-bottom: 10px;
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
    }

    .btn-primary:disabled {
        background: #a4c9f3;
        cursor: not-allowed;
    }

    .btn-outline {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        color: #212b36;
        cursor: pointer;
        text-decoration: none;
    }

    .hidden-section {
        display: none;
        margin-top: 10px;
        padding-left: 24px;
    }

    .block-title {
        font-size: 14px;
        font-weight: 600;
        color: #0088ff;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e6f7ff;
    }
</style>

<form action="index.php?action=add_promo" method="POST" id="promoForm">
    <div class="v3-header">
        <div class="v3-title"><a href="index.php?action=promo_list">←</a> <span id="page_title">Tạo khuyến mại</span></div>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?action=promo_list" class="btn-outline">Hủy</a>
            <button type="submit" id="btnSubmit" class="btn-primary">Tạo khuyến mại</button>
        </div>
    </div>

    <input type="hidden" name="is_coupon" id="is_coupon" value="<?php echo !empty($promo['promo_code']) ? '1' : '0'; ?>">
    <input type="hidden" name="promo_type" id="promo_type" value="<?php echo $promo['promo_type'] ?? 'discount_order'; ?>">

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 65%;">
            <div class="v3-card">
                <div class="v3-card-title">Thông tin chung</div>
                <div class="form-group">
                    <label class="form-label">Tên chương trình khuyến mại *</label>
                    <input type="text" name="promo_name" class="form-control" value="<?php echo htmlspecialchars($promo['promo_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group" id="coupon_code_box" style="display: none;">
                    <label class="form-label">Mã khuyến mại *</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="promo_code" id="promo_code" value="<?php echo htmlspecialchars($promo['promo_code'] ?? ''); ?>" class="form-control" style="text-transform: uppercase; font-family: monospace; font-weight: bold;">
                        <button type="button" class="btn-outline" onclick="document.getElementById('promo_code').value = 'KM' + Math.random().toString(36).substring(2, 10).toUpperCase();">Tạo mã</button>
                    </div>
                </div>
            </div>

            <div class="v3-card" id="card_standard_discount">
                <div class="v3-card-title">Giá trị khuyến mại</div>
                <div class="row-flex">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Hình thức giảm</label>
                        <select name="discount_type" id="discount_type" class="form-control" onchange="toggleMaxDiscount()">
                            <option value="amount" <?php echo (($promo['discount_type'] ?? '') == 'amount') ? 'selected' : ''; ?>>Theo số tiền (₫)</option>
                            <option value="percent" <?php echo (($promo['discount_type'] ?? '') == 'percent') ? 'selected' : ''; ?>>Theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Mức giảm</label>
                        <input type="text" name="discount_value" class="form-control" value="<?php echo isset($promo['discount_value']) ? number_format($promo['discount_value'], 0, '', '.') : ''; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>
                <div class="form-group" id="max_discount_box" style="<?php echo (($promo['discount_type'] ?? '') == 'percent') ? 'display:block;' : 'display:none;'; ?> background: #fafbfc; padding: 10px; border-radius: 4px;">
                    <label class="check-box"><input type="checkbox" id="has_max_discount" <?php echo !empty($promo['max_discount_amount']) ? 'checked' : ''; ?> onchange="document.getElementById('max_discount_input_box').style.display = this.checked ? 'block' : 'none';"> Giới hạn mức giảm tối đa</label>
                    <div id="max_discount_input_box" style="<?php echo !empty($promo['max_discount_amount']) ? 'display:block;' : 'display:none;'; ?> margin-top: 10px;">
                        <input type="text" name="max_discount_amount" class="form-control" value="<?php echo isset($promo['max_discount_amount']) ? number_format($promo['max_discount_amount'], 0, '', '.') : ''; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>

                <div id="product_target_box" style="display: none; margin-top: 20px; padding-top: 15px; border-top: 1px dashed #dfe3e8;">
                    <label class="form-label">Áp dụng cho:</label>
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <label class="radio-box"><input type="radio" name="apply_to" value="all" <?php echo ($promo_apply['apply_to'] ?? 'all') == 'all' ? 'checked' : ''; ?> onchange="toggleProductSelection()"> Tất cả SP</label>
                        <label class="radio-box"><input type="radio" name="apply_to" value="product" <?php echo ($promo_apply['apply_to'] ?? '') == 'product' ? 'checked' : ''; ?> onchange="toggleProductSelection()"> SP cụ thể</label>
                        <label class="radio-box"><input type="radio" name="apply_to" value="category" <?php echo ($promo_apply['apply_to'] ?? '') == 'category' ? 'checked' : ''; ?> onchange="toggleProductSelection()"> Danh mục</label>
                    </div>
                    <select name="apply_product_ids[]" id="select_product_box" class="form-control" multiple style="height: 100px;">
                        <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>" <?php echo in_array($p['id'], $promo_apply['product_ids'] ?? []) ? 'selected' : ''; ?>>📱 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                    </select>
                    <select name="apply_category_ids[]" id="select_category_box" class="form-control" multiple style="height: 100px;">
                        <?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo in_array($c['id'], $promo_apply['category_ids'] ?? []) ? 'selected' : ''; ?>>📂 <?php echo htmlspecialchars($c['category_name']); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="v3-card" id="card_gift_bogo" style="display: none;">
                <div class="v3-card-title">Cấu hình Mua X Tặng Y</div>
                <div style="background: #f4fbf7; border: 1px solid #8ce09f; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div class="block-title" style="color: #108043; border-bottom-color: #c9f0d4;">🛒 ĐIỀU KIỆN MUA (MUA X)</div>
                    <div class="row-flex">
                        <div class="form-group">
                            <label class="form-label">Hình thức</label>
                            <select name="buy_condition_type" class="form-control">
                                <option value="qty" <?php echo ($promo_gift['buy_condition_type'] ?? '') == 'qty' ? 'selected' : ''; ?>>Số lượng SP tối thiểu</option>
                                <option value="amount" <?php echo ($promo_gift['buy_condition_type'] ?? '') == 'amount' ? 'selected' : ''; ?>>Giá trị SP tối thiểu (₫)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mức đạt</label>
                            <input type="text" name="buy_min_value" class="form-control" value="<?php echo $promo_gift['buy_min_value'] ?? '1'; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sản phẩm khách cần mua</label>
                        <select name="buy_product_ids[]" class="form-control" multiple style="height: 80px;">
                            <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>" <?php echo in_array($p['id'], $promo_gift['buy_product_ids'] ?? []) ? 'selected' : ''; ?>>📱 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="background: #fff8ea; border: 1px solid #ffea8a; padding: 15px; border-radius: 6px;">
                    <div class="block-title" style="color: #8a6100; border-bottom-color: #ffea8a;">🎁 QUÀ TẶNG (TẶNG Y)</div>
                    <div class="row-flex">
                        <div class="form-group">
                            <label class="form-label">Số lượng được tặng</label>
                            <input type="number" name="get_qty" class="form-control" value="<?php echo $promo_gift['get_qty'] ?? '1'; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mức giảm giá cho Quà</label>
                            <select name="get_discount_type" class="form-control" onchange="document.getElementById('get_discount_val_box').style.display = (this.value==='free') ? 'none' : 'block';">
                                <option value="free" <?php echo ($promo_gift['get_discount_type'] ?? '') == 'free' ? 'selected' : ''; ?>>Miễn phí 100%</option>
                                <option value="percent" <?php echo ($promo_gift['get_discount_type'] ?? '') == 'percent' ? 'selected' : ''; ?>>Giảm theo %</option>
                                <option value="amount" <?php echo ($promo_gift['get_discount_type'] ?? '') == 'amount' ? 'selected' : ''; ?>>Giảm theo số tiền</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sản phẩm tặng kèm</label>
                        <select name="get_product_ids[]" class="form-control" multiple style="height: 80px;">
                            <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>" <?php echo in_array($p['id'], $promo_gift['get_product_ids'] ?? []) ? 'selected' : ''; ?>>🎁 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="v3-card" id="card_freeship" style="display: none;">
                <div class="v3-card-title">Giá trị miễn phí vận chuyển</div>
                <div class="form-group">
                    <label class="form-label">Mức miễn phí tối đa (₫) *</label>
                    <input type="text" name="shipping_max_discount" class="form-control" value="<?php echo isset($promo['max_discount_amount']) ? number_format($promo['max_discount_amount'], 0, '', '.') : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Áp dụng với phí vận chuyển dưới (₫)</label>
                    <input type="text" name="max_shipping_fee" class="form-control" value="<?php echo isset($promo_ship['max_shipping_fee']) ? number_format($promo_ship['max_shipping_fee'], 0, '', '.') : ''; ?>">
                </div>
            </div>

            <div class="v3-card" id="card_conditions">
                <div class="v3-card-title">Điều kiện áp dụng</div>
                <?php
                $cond_type = 'none';
                if (($promo['min_order_value'] ?? 0) > 0) $cond_type = 'min_amount';
                if (($promo['min_product_qty'] ?? 0) > 0) $cond_type = 'min_qty';
                ?>
                <label class="radio-box"><input type="radio" name="condition_type" value="none" <?php echo $cond_type == 'none' ? 'checked' : ''; ?> onchange="toggleCondition()"> Không có điều kiện</label>
                <label class="radio-box"><input type="radio" name="condition_type" value="min_amount" <?php echo $cond_type == 'min_amount' ? 'checked' : ''; ?> onchange="toggleCondition()"> Tổng giá trị đơn hàng tối thiểu</label>
                <div id="cond_min_amount" class="hidden-section">
                    <input type="text" name="min_order_value" class="form-control" value="<?php echo isset($promo['min_order_value']) ? number_format($promo['min_order_value'], 0, '', '.') : ''; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                </div>
                <label class="radio-box"><input type="radio" name="condition_type" value="min_qty" <?php echo $cond_type == 'min_qty' ? 'checked' : ''; ?> onchange="toggleCondition()"> Tổng số lượng sản phẩm tối thiểu</label>
                <div id="cond_min_qty" class="hidden-section">
                    <input type="number" name="min_product_qty" class="form-control" value="<?php echo $promo['min_product_qty'] ?? ''; ?>" min="1">
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="v3-card">
                <div class="v3-card-title">Thời gian áp dụng</div>
                <div class="form-group">
                    <label class="form-label">Bắt đầu</label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="start_date" class="form-control" value="<?php echo isset($promo['start_date']) ? date('Y-m-d', strtotime($promo['start_date'])) : date('Y-m-d'); ?>"><input type="time" name="start_time" class="form-control" value="<?php echo isset($promo['start_date']) ? date('H:i', strtotime($promo['start_date'])) : '00:00'; ?>"></div>
                </div>
                <div class="form-group" id="end_date_box" style="<?php echo ($promo['no_end_date'] ?? 0) == 1 ? 'display:none;' : ''; ?>">
                    <label class="form-label">Kết thúc</label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="end_date" class="form-control" value="<?php echo isset($promo['end_date']) ? date('Y-m-d', strtotime($promo['end_date'])) : date('Y-m-d', strtotime('+30 days')); ?>"><input type="time" name="end_time" class="form-control" value="<?php echo isset($promo['end_date']) ? date('H:i', strtotime($promo['end_date'])) : '23:59'; ?>"></div>
                </div>
                <label class="check-box"><input type="checkbox" name="no_end_date" id="no_end_date" value="1" <?php echo ($promo['no_end_date'] ?? 0) == 1 ? 'checked' : ''; ?> onchange="document.getElementById('end_date_box').style.display = this.checked ? 'none' : 'block';"> Không có ngày kết thúc</label>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Kênh bán hàng</div>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="pos" <?php echo in_array('pos', $channels) ? 'checked' : ''; ?>> Bán tại quầy (POS)</label>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="web" <?php echo in_array('web', $channels) ? 'checked' : ''; ?>> Website</label>
            </div>

            <div class="v3-card" id="usage_limit_card">
                <div class="v3-card-title">Giới hạn sử dụng</div>
                <label class="check-box"><input type="checkbox" name="has_usage_limit" id="has_usage_limit" <?php echo isset($promo['usage_limit']) ? 'checked' : ''; ?> onchange="document.getElementById('usage_limit_box').style.display = this.checked ? 'block' : 'none';"> Giới hạn tổng số lượt sử dụng</label>
                <div id="usage_limit_box" class="hidden-section" style="<?php echo isset($promo['usage_limit']) ? 'display:block;' : 'display:none;'; ?>">
                    <input type="number" name="usage_limit" class="form-control" value="<?php echo $promo['usage_limit'] ?? ''; ?>" min="1">
                </div>
                <label class="check-box" style="margin-top:10px;"><input type="checkbox" name="once_per_customer" value="1" <?php echo ($promo['once_per_customer'] ?? 0) == 1 ? 'checked' : ''; ?>> Mỗi khách chỉ được dùng 1 lần</label>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Kết hợp khuyến mại</div>
                <label class="check-box"><input type="checkbox" name="allowed_combinations[]" value="product" <?php echo in_array('product', $combinations) ? 'checked' : ''; ?>> Khuyến mại sản phẩm</label>
                <label class="check-box"><input type="checkbox" name="allowed_combinations[]" value="order" <?php echo in_array('order', $combinations) ? 'checked' : ''; ?>> Khuyến mại đơn hàng</label>
                <label class="check-box"><input type="checkbox" name="allowed_combinations[]" value="shipping" <?php echo in_array('shipping', $combinations) ? 'checked' : ''; ?>> Miễn phí vận chuyển</label>
            </div>
        </div>
    </div>
</form>

<script>
    // 1. CHỐNG DOUBLE-SUBMIT (NGĂN TẠO 2 MÃ)
    document.getElementById('promoForm').addEventListener('submit', function(e) {
        // Nếu form đã được báo là "đang gửi", thì chặn đứng mọi cú click tiếp theo
        if (this.dataset.isSubmitting) {
            e.preventDefault();
            return false;
        }

        // Đánh dấu form này "đang gửi"
        this.dataset.isSubmitting = "true";

        // Đổi trạng thái nút bấm
        let btn = document.getElementById('btnSubmit');
        if (btn) {
            btn.innerHTML = 'Đang xử lý... ⏳';
            btn.style.pointerEvents = 'none'; // Khóa click chuột
            btn.style.opacity = '0.7';

            // Không dùng btn.disabled = true; ở đây vì trình duyệt Safari/Edge 
            // có thể hiểu nhầm là form không hợp lệ và hủy bỏ việc gửi.
        }
    });

    // 2. LOGIC ẨN HIỆN GIAO DIỆN (UI MORPHING)
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const isCopy = <?php echo isset($promo) ? 'true' : 'false'; ?>;

        let mode = urlParams.get('mode');
        let type = urlParams.get('type');

        if (isCopy) {
            mode = document.getElementById('is_coupon').value === '1' ? 'coupon' : 'auto';
            type = document.getElementById('promo_type').value;
        }

        if (mode === 'coupon') {
            document.getElementById('is_coupon').value = '1';
            document.getElementById('coupon_code_box').style.display = 'block';
            document.getElementById('usage_limit_card').style.display = 'block';
            document.getElementById('page_title').innerText = isCopy ? 'Sao chép mã khuyến mại' : 'Tạo mã khuyến mại';
        } else {
            document.getElementById('is_coupon').value = '0';
            document.getElementById('coupon_code_box').style.display = 'none';
            document.getElementById('usage_limit_card').style.display = 'none';
            document.getElementById('page_title').innerText = isCopy ? 'Sao chép chương trình khuyến mại' : 'Tạo chương trình khuyến mại';
        }

        document.getElementById('card_standard_discount').style.display = 'none';
        document.getElementById('product_target_box').style.display = 'none';
        document.getElementById('card_gift_bogo').style.display = 'none';
        document.getElementById('card_freeship').style.display = 'none';

        if (type === 'discount_order') {
            document.getElementById('card_standard_discount').style.display = 'block';
        } else if (type === 'discount_product') {
            document.getElementById('card_standard_discount').style.display = 'block';
            document.getElementById('product_target_box').style.display = 'block';
            toggleProductSelection();
        } else if (type === 'gift_by_product' || type === 'gift_by_order') {
            document.getElementById('card_gift_bogo').style.display = 'block';
            document.getElementById('card_conditions').style.display = 'none';
        } else if (type === 'free_shipping') {
            document.getElementById('card_freeship').style.display = 'block';
        }
        toggleCondition();
    });

    function toggleProductSelection() {
        let val = document.querySelector('input[name="apply_to"]:checked').value;
        document.getElementById('select_product_box').style.display = (val === 'product') ? 'block' : 'none';
        document.getElementById('select_category_box').style.display = (val === 'category') ? 'block' : 'none';
    }

    function toggleCondition() {
        let condRadio = document.querySelector('input[name="condition_type"]:checked');
        if (!condRadio) return;
        let val = condRadio.value;
        document.getElementById('cond_min_amount').style.display = (val === 'min_amount') ? 'block' : 'none';
        document.getElementById('cond_min_qty').style.display = (val === 'min_qty') ? 'block' : 'none';
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
