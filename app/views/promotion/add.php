<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$branches = $branches ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
// Mockup Tỉnh Thành (Trong thực tế có thể lấy từ DB)
$provinces = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Bắc Ninh', 'Bình Dương'];
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
            <button type="submit" class="btn-primary">Tạo khuyến mại</button>
        </div>
    </div>

    <input type="hidden" name="is_coupon" id="is_coupon" value="0">
    <input type="hidden" name="promo_type" id="promo_type" value="discount_order">

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 65%;">

            <div class="v3-card">
                <div class="v3-card-title">Thông tin chung</div>
                <div class="form-group">
                    <label class="form-label">Tên chương trình khuyến mại *</label>
                    <input type="text" name="promo_name" class="form-control" placeholder="VD: Khuyến mại Hè 2024" required>
                </div>
                <div class="form-group" id="coupon_code_box" style="display: none;">
                    <label class="form-label">Mã khuyến mại *</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="promo_code" id="promo_code" class="form-control" style="text-transform: uppercase; font-family: monospace; font-weight: bold;">
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
                            <option value="amount">Theo số tiền (₫)</option>
                            <option value="percent">Theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Mức giảm</label>
                        <input type="text" name="discount_value" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>
                <div class="form-group" id="max_discount_box" style="display: none; background: #fafbfc; padding: 10px; border-radius: 4px;">
                    <label class="check-box"><input type="checkbox" id="has_max_discount" onchange="document.getElementById('max_discount_input_box').style.display = this.checked ? 'block' : 'none';"> Giới hạn mức giảm tối đa</label>
                    <div id="max_discount_input_box" style="display: none; margin-top: 10px;">
                        <input type="text" name="max_discount_amount" class="form-control" placeholder="Số tiền giảm tối đa (₫)" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>

                <div id="product_target_box" style="display: none; margin-top: 20px; padding-top: 15px; border-top: 1px dashed #dfe3e8;">
                    <label class="form-label">Áp dụng cho:</label>
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <label class="radio-box"><input type="radio" name="apply_to" value="all" checked onchange="toggleProductSelection()"> Tất cả SP</label>
                        <label class="radio-box"><input type="radio" name="apply_to" value="product" onchange="toggleProductSelection()"> SP cụ thể</label>
                        <label class="radio-box"><input type="radio" name="apply_to" value="category" onchange="toggleProductSelection()"> Danh mục</label>
                    </div>
                    <select name="apply_product_ids[]" id="select_product_box" class="form-control" multiple style="display:none; height: 100px;">
                        <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>">📱 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                    </select>
                    <select name="apply_category_ids[]" id="select_category_box" class="form-control" multiple style="display:none; height: 100px;">
                        <?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>">📂 <?php echo htmlspecialchars($c['category_name']); ?></option><?php endforeach; ?>
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
                                <option value="qty">Số lượng SP tối thiểu</option>
                                <option value="amount">Giá trị SP tối thiểu (₫)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mức đạt</label>
                            <input type="text" name="buy_min_value" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sản phẩm khách cần mua</label>
                        <select name="buy_product_ids[]" class="form-control" multiple style="height: 80px;">
                            <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>">📱 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="background: #fff8ea; border: 1px solid #ffea8a; padding: 15px; border-radius: 6px;">
                    <div class="block-title" style="color: #8a6100; border-bottom-color: #ffea8a;">🎁 QUÀ TẶNG (TẶNG Y)</div>
                    <div class="row-flex">
                        <div class="form-group">
                            <label class="form-label">Số lượng được tặng</label>
                            <input type="number" name="get_qty" class="form-control" value="1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mức giảm giá cho Quà</label>
                            <select name="get_discount_type" class="form-control" onchange="document.getElementById('get_discount_val_box').style.display = (this.value==='free') ? 'none' : 'block';">
                                <option value="free">Miễn phí 100% (0đ)</option>
                                <option value="percent">Giảm theo %</option>
                                <option value="amount">Giảm theo số tiền</option>
                            </select>
                        </div>
                        <div class="form-group" id="get_discount_val_box" style="display:none;">
                            <label class="form-label">Mức giảm</label>
                            <input type="text" name="get_discount_value" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sản phẩm tặng kèm</label>
                        <select name="get_product_ids[]" class="form-control" multiple style="height: 80px;">
                            <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>">🎁 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giới hạn số lần tặng cho 1 đơn hàng</label>
                        <input type="number" name="max_gift_applies" class="form-control" placeholder="Để trống nếu muốn nhân dồn...">
                    </div>
                </div>
            </div>

            <div class="v3-card" id="card_freeship" style="display: none;">
                <div class="v3-card-title">Giá trị miễn phí vận chuyển</div>
                <div class="form-group">
                    <label class="form-label">Mức miễn phí tối đa (₫) *</label>
                    <input type="text" name="shipping_max_discount" class="form-control" placeholder="VD: 30000" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                </div>
                <div class="form-group">
                    <label class="form-label">Áp dụng với phí vận chuyển dưới (₫)</label>
                    <input type="text" name="max_shipping_fee" class="form-control" placeholder="Bỏ trống nếu áp dụng mọi mức phí">
                </div>
                <div class="form-group">
                    <label class="form-label">Tỉnh/thành áp dụng</label>
                    <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                        <label class="radio-box"><input type="radio" name="shipping_area_scope" value="all" checked onchange="document.getElementById('shipping_provinces_box').style.display = 'none';"> Toàn quốc</label>
                        <label class="radio-box"><input type="radio" name="shipping_area_scope" value="specific" onchange="document.getElementById('shipping_provinces_box').style.display = 'block';"> Khu vực cụ thể</label>
                    </div>
                    <select name="shipping_provinces[]" id="shipping_provinces_box" class="form-control" multiple style="display:none; height: 100px;">
                        <?php foreach ($provinces as $prov): ?><option value="<?php echo $prov; ?>">📍 <?php echo $prov; ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="v3-card" id="card_conditions">
                <div class="v3-card-title">Điều kiện áp dụng</div>
                <label class="radio-box"><input type="radio" name="condition_type" value="none" checked onchange="toggleCondition()"> Không có điều kiện</label>
                <label class="radio-box" id="lbl_cond_amount"><input type="radio" name="condition_type" value="min_amount" onchange="toggleCondition()"> <span id="txt_cond_amount">Tổng giá trị đơn hàng tối thiểu</span></label>
                <div id="cond_min_amount" class="hidden-section" style="margin-bottom: 10px;">
                    <input type="text" name="min_order_value" class="form-control" placeholder="Nhập số tiền (₫)..." oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                </div>
                <label class="radio-box"><input type="radio" name="condition_type" value="min_qty" onchange="toggleCondition()"> Tổng số lượng sản phẩm tối thiểu</label>
                <div id="cond_min_qty" class="hidden-section">
                    <input type="number" name="min_product_qty" class="form-control" placeholder="Nhập số lượng..." min="1">
                </div>
            </div>

        </div>

        <div style="flex: 1;">
            <div class="v3-card">
                <div class="v3-card-title">Thời gian áp dụng</div>
                <div class="form-group">
                    <label class="form-label">Bắt đầu</label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required><input type="time" name="start_time" class="form-control" value="00:00"></div>
                </div>
                <div class="form-group" id="end_date_box">
                    <label class="form-label">Kết thúc</label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"><input type="time" name="end_time" class="form-control" value="23:59"></div>
                </div>
                <label class="check-box"><input type="checkbox" name="no_end_date" id="no_end_date" value="1" onchange="document.getElementById('end_date_box').style.display = this.checked ? 'none' : 'block';"> Không có ngày kết thúc</label>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Kênh bán hàng</div>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="pos" checked> Bán tại quầy (POS)</label>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="web" checked> Website</label>
                <label class="check-box"><input type="checkbox" name="sales_channels[]" value="facebook"> Facebook / Chat OmniAI</label>
            </div>

            <div class="v3-card">
                <div class="v3-card-title">Giới hạn sử dụng</div>
                <div class="form-group">
                    <label class="check-box"><input type="checkbox" id="has_usage_limit" onchange="document.getElementById('usage_limit_box').style.display = this.checked ? 'block' : 'none';"> Giới hạn tổng số lượt sử dụng</label>
                    <div id="usage_limit_box" class="hidden-section" style="margin-top: 5px; padding-left: 24px;">
                        <input type="number" name="usage_limit" class="form-control" placeholder="Nhập tổng số lượt..." min="1">
                    </div>
                </div>
                <label class="check-box"><input type="checkbox" name="once_per_customer" value="1"> Mỗi khách hàng chỉ được dùng 1 lần</label>
            </div>

            <div class="v3-card" id="combination_box">
                <div class="v3-card-title">Kết hợp khuyến mại</div>
                <label class="check-box" id="cb_combo_prod"><input type="checkbox" name="allowed_combinations[]" value="product"> Khuyến mại sản phẩm</label>
                <label class="check-box" id="cb_combo_order"><input type="checkbox" name="allowed_combinations[]" value="order"> Khuyến mại đơn hàng</label>
                <label class="check-box" id="cb_combo_ship"><input type="checkbox" name="allowed_combinations[]" value="shipping"> Miễn phí vận chuyển</label>
            </div>
        </div>
    </div>
</form>

<script>
    // CORE LOGIC: BIẾN HÌNH FORM THEO LOẠI KHUYẾN MẠI
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode');
        const type = urlParams.get('type');

        if (mode === 'coupon') {
            document.getElementById('is_coupon').value = '1';
            document.getElementById('coupon_code_box').style.display = 'block';
            document.getElementById('promo_code').setAttribute('required', 'required');
            document.getElementById('page_title').innerText = 'Tạo mã khuyến mại';
        } else {
            document.getElementById('page_title').innerText = 'Tạo chương trình khuyến mại';
        }

        if (type) {
            document.getElementById('promo_type').value = type;

            // Ẩn tất cả các khối đặc thù trước
            document.getElementById('card_standard_discount').style.display = 'none';
            document.getElementById('product_target_box').style.display = 'none';
            document.getElementById('card_gift_bogo').style.display = 'none';
            document.getElementById('card_freeship').style.display = 'none';
            document.getElementById('card_conditions').style.display = 'block';

            // Xử lý hiển thị theo Type
            if (type === 'discount_order') {
                document.getElementById('card_standard_discount').style.display = 'block';
                document.getElementById('cb_combo_order').style.display = 'none'; // Không tự kết hợp với chính mình
            } else if (type === 'discount_product') {
                document.getElementById('card_standard_discount').style.display = 'block';
                document.getElementById('product_target_box').style.display = 'block';
                document.getElementById('txt_cond_amount').innerText = 'Tổng giá trị sản phẩm được khuyến mại tối thiểu';
                document.getElementById('cb_combo_prod').style.display = 'none';
            } else if (type === 'gift_by_product' || type === 'gift_by_order') {
                document.getElementById('card_gift_bogo').style.display = 'block';
                document.getElementById('card_conditions').style.display = 'none'; // Điều kiện đã nằm trong Mua X
            } else if (type === 'free_shipping') {
                document.getElementById('card_freeship').style.display = 'block';
                document.getElementById('cb_combo_ship').style.display = 'none'; // Freeship ko kết hợp Freeship
            }
        }
    });

    function toggleProductSelection() {
        let val = document.querySelector('input[name="apply_to"]:checked').value;
        document.getElementById('select_product_box').style.display = (val === 'product') ? 'block' : 'none';
        document.getElementById('select_category_box').style.display = (val === 'category') ? 'block' : 'none';
    }

    function toggleMaxDiscount() {
        let type = document.getElementById('discount_type').value;
        document.getElementById('max_discount_box').style.display = (type === 'percent') ? 'block' : 'none';
    }

    function toggleCondition() {
        let val = document.querySelector('input[name="condition_type"]:checked').value;
        document.getElementById('cond_min_amount').style.display = (val === 'min_amount') ? 'block' : 'none';
        document.getElementById('cond_min_qty').style.display = (val === 'min_qty') ? 'block' : 'none';
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
