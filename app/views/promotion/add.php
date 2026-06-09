<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
// Đảm bảo các mảng không bị lỗi nếu Controller chưa kịp truyền sang
$branches = $branches ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$brands = $brands ?? [];
?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
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
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    .radio-box {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .hidden-section {
        display: none;
        margin-top: 10px;
        padding: 15px;
        background: #fafbfc;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
    }
</style>

<div style="margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=promo_list" style="text-decoration:none; color:#637381;">←</a> Tạo chương trình khuyến mại</h2>
</div>

<form action="index.php?action=add_promo" method="POST">
    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 0 0 65%;">

            <div class="sapo-card">
                <div class="sapo-card-title">1. Thông tin chung</div>
                <div class="form-group">
                    <label>Tên khuyến mại <span style="color:red;">*</span></label>
                    <input type="text" name="promo_name" class="form-control" required placeholder="VD: Khuyến mại Tết...">
                </div>
                <div class="form-group" style="background: #fafbfc; padding: 15px; border: 1px solid #dfe3e8; border-radius: 6px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="checkbox" id="has_code" onchange="togglePromoCode()" style="width: 16px; height: 16px; accent-color: #0088ff;">
                        <b>Phát hành dưới dạng Mã giảm giá (Coupon)</b>
                    </label>
                    <p style="font-size: 12px; color: #637381; margin: 5px 0 0 25px;">Nếu không chọn, hệ thống sẽ tự động áp dụng cho khách khi đủ điều kiện.</p>
                    <div id="code_input_box" style="display: none; margin-top: 15px; margin-left: 25px;">
                        <input type="text" name="promo_code" class="form-control" placeholder="Nhập mã (VD: SALE50K, FREESHIP)..." style="font-family: monospace; font-weight: bold; text-transform: uppercase;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Số lượng áp dụng <span style="color:red;">*</span></label>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <input type="number" id="usage_limit" name="usage_limit" class="form-control" value="100" style="width: 150px;" required>
                        <label style="display: flex; align-items: center; gap: 5px; margin: 0; cursor: pointer;">
                            <input type="checkbox" name="unlimited_usage" id="unlimited_usage" value="1" onchange="toggleLimit()"> Không giới hạn số lượng
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Nội dung khuyến mại..."></textarea>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">2. Thiết lập mức ưu đãi</div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Hình thức khuyến mại</label>
                    <select name="promo_type" id="promo_type" class="form-control" onchange="switchPromoType()" style="font-weight: 600; color: #0088ff;">
                        <option value="discount_order">📉 Chiết khấu / Giảm giá trên tổng đơn hàng</option>
                        <option value="discount_product">📉 Chiết khấu / Giảm giá theo sản phẩm cụ thể</option>
                        <option value="gift_by_order">🎁 Tặng quà theo tổng giá trị đơn hàng</option>
                        <option value="gift_by_product">🎁 Tặng quà theo sản phẩm mua (Mua X tặng Y)</option>
                    </select>
                </div>

                <div id="section_discount_normal">
                    <div class="row-flex">
                        <div class="form-group">
                            <label>Mức giảm</label>
                            <div style="display: flex;">
                                <select name="discount_type" class="form-control" style="width: 40%; border-radius: 4px 0 0 4px; border-right: none; background: #fafbfc;">
                                    <option value="amount">Số tiền (₫)</option>
                                    <option value="percent">Phần trăm (%)</option>
                                </select>
                                <input type="text" name="discount_value" class="form-control" value="0" style="border-radius: 0 4px 4px 0;" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="min_order_box">
                        <label>Điều kiện: Tổng giá trị đơn hàng tối thiểu từ (₫)</label>
                        <input type="text" name="min_order_value" class="form-control" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                </div>

                <div id="section_gift_by_order" class="hidden-section" style="border: 1px dashed #0088ff; padding: 15px; border-radius: 6px; background: #f9fbfd;">
                    <div class="form-group">
                        <label style="color:#0088ff;">Giá trị đơn hàng tối thiểu để nhận quà (₫) *</label>
                        <input type="text" name="min_order_value" class="form-control" value="1.000.000" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                    </div>
                    <div class="form-group">
                        <label>Chọn các sản phẩm quà tặng được phép nhận *</label>
                        <select name="gift_order_product_ids[]" class="form-control" multiple style="height: 100px;">
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['id']; ?>">📱 <?php echo htmlspecialchars($p['product_name']); ?> (<?php echo $p['sku']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color:#637381;">Giữ phím Ctrl để chọn nhiều sản phẩm.</small>
                    </div>
                    <div class="form-group">
                        <label>Số lượng sản phẩm quà tặng tối đa khách được chọn *</label>
                        <input type="number" name="max_gift_order_qty" class="form-control" value="1" min="1" style="width: 120px;">
                    </div>
                </div>

                <div id="section_gift_by_product" class="hidden-section" style="border: 1px dashed #108043; padding: 15px; border-radius: 6px; background: #f4fbf7;">
                    <div style="background: #fff; padding: 15px; border-radius: 6px; border:1px solid #dfe3e8; margin-bottom: 15px;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; color: #108043;">🛒 Điều kiện áp dụng</h4>
                        <div class="row-flex">
                            <div class="form-group" style="flex: 1;">
                                <label>Chọn sản phẩm áp dụng theo:</label>
                                <select name="apply_prod_condition" class="form-control" onchange="toggleProdCondition(this.value)">
                                    <option value="product">Phiên bản sản phẩm</option>
                                    <option value="category">Loại sản phẩm (Danh mục)</option>
                                    <option value="brand">Nhãn hiệu (Thương hiệu)</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 2;" id="box_select_products">
                                <label>Chọn các sản phẩm cụ thể *</label>
                                <select name="apply_prod_values[]" class="form-control" multiple style="height: 60px;">
                                    <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>">📱 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 2; display:none;" id="box_select_categories">
                                <label>Chọn Loại sản phẩm (Danh mục) *</label>
                                <select name="apply_prod_values[]" class="form-control" multiple style="height: 60px;">
                                    <?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>">📂 <?php echo htmlspecialchars($c['category_name']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 2; display:none;" id="box_select_brands">
                                <label>Chọn Nhãn hiệu *</label>
                                <select name="apply_prod_values[]" class="form-control" multiple style="height: 60px;">
                                    <?php foreach ($brands as $b): ?><option value="<?php echo htmlspecialchars($b); ?>">🏷️ <?php echo htmlspecialchars($b); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="width: 50%; margin-top: 10px;">
                            <label>Số lượng tối thiểu cần mua *</label>
                            <input type="number" name="buy_qty" class="form-control" value="2" min="1">
                        </div>
                    </div>

                    <div style="background: #fff; padding: 15px; border-radius: 6px; border:1px solid #dfe3e8;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #cf1322;">🎁 Quy định quà tặng</h4>
                        <div class="form-group">
                            <label>Chọn sản phẩm quà tặng kèm *</label>
                            <select name="gift_prod_product_ids[]" class="form-control" multiple style="height: 80px;">
                                <?php foreach ($products as $p): ?><option value="<?php echo $p['id']; ?>">🎁 <?php echo htmlspecialchars($p['product_name']); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="width: 50%;">
                            <label>Số lượng tối đa KH được nhận *</label>
                            <input type="number" name="max_gift_prod_qty" class="form-control" value="1" min="1">
                        </div>
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: bold; color: #212b36; cursor: pointer; margin-top: 15px;">
                            <input type="checkbox" name="apply_multiple" value="1" checked style="width: 16px; height: 16px; accent-color: #108043;"> ⚡ Cho phép áp dụng nhiều lần (Ví dụ: Mua 2 tặng 1, Mua 4 tặng 2...)
                        </label>
                    </div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">3. Cài đặt phạm vi áp dụng</div>
                <label class="radio-box"><input type="radio" name="branch_scope" value="all" checked onchange="toggleBranch()"> Áp dụng cho tất cả chi nhánh</label>
                <label class="radio-box"><input type="radio" name="branch_scope" value="specific" onchange="toggleBranch()"> Chọn chi nhánh áp dụng</label>

                <div id="branch_list" class="hidden-section">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <?php foreach ($branches as $b): ?>
                            <label style="font-size: 13px; display: flex; gap: 5px;"><input type="checkbox" name="branches[]" value="<?php echo $b['id']; ?>"> 🏢 <?php echo htmlspecialchars($b['branch_name']); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">4. Cài đặt đối tượng khách hàng</div>
                <label class="radio-box"><input type="radio" name="customer_scope" value="all" checked onchange="toggleCustomer()"> Toàn bộ khách hàng</label>
                <label class="radio-box"><input type="radio" name="customer_scope" value="specific" onchange="toggleCustomer()"> Chọn đối tượng khách hàng cụ thể (Nâng cao)</label>

                <div id="customer_cond_box" class="hidden-section">
                    <div class="form-group"><label>Nhóm khách hàng</label><select name="customer_cond[group]" class="form-control">
                            <option value="all">Tất cả nhóm</option>
                            <option value="VIP">Khách hàng VIP</option>
                            <option value="BBM">Khách Bán Buôn</option>
                        </select></div>
                    <div class="form-group"><label>Giới tính</label><select name="customer_cond[gender]" class="form-control">
                            <option value="all">Tất cả</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select></div>
                </div>
            </div>
        </div>

        <div style="flex: 1;">

            <div class="sapo-card">
                <div class="sapo-card-title">5. Thời gian áp dụng</div>
                <div class="form-group">
                    <label>Bắt đầu <span style="color:red;">*</span></label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required><input type="time" name="start_time" class="form-control" value="00:00"></div>
                </div>
                <div class="form-group" id="end_date_box">
                    <label>Kết thúc</label>
                    <div class="row-flex" style="gap: 5px;"><input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"><input type="time" name="end_time" class="form-control" value="23:59"></div>
                </div>
                <label style="display: flex; gap: 5px; font-size: 14px; margin-bottom: 20px;"><input type="checkbox" name="no_end_date" id="no_end_date" value="1" onchange="toggleEndDate()"> Không cần ngày kết thúc</label>

                <div style="border-top: 1px dashed #dfe3e8; padding-top: 15px;">
                    <label style="display: flex; gap: 5px; font-size: 14px; font-weight: 500; color: #0088ff;"><input type="checkbox" name="enable_advanced_time" value="1" onchange="toggleAdvancedTime()"> + Tùy chỉnh hiển thị nâng cao</label>
                    <div id="advanced_time_box" class="hidden-section" style="padding: 10px;">
                        <label style="font-size:12px; font-weight:bold;">Khung giờ vàng:</label>
                        <div class="row-flex" style="gap: 5px; margin-bottom: 10px;">
                            <input type="time" name="advanced_time[hour_start]" class="form-control" placeholder="Từ">
                            <input type="time" name="advanced_time[hour_end]" class="form-control" placeholder="Đến">
                        </div>
                        <label style="font-size:12px; font-weight:bold;">Ngày trong tuần:</label>
                        <select name="advanced_time[days][]" class="form-control" multiple style="height: 80px; font-size:12px;">
                            <option value="2">Thứ 2</option>
                            <option value="3">Thứ 3</option>
                            <option value="4">Thứ 4</option>
                            <option value="5">Thứ 5</option>
                            <option value="6">Thứ 6</option>
                            <option value="7">Thứ 7</option>
                            <option value="cn">Chủ nhật</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                <button type="button" onclick="window.location.href='index.php?action=promo_list'" style="background:#fff; color:#212b36; border:1px solid #c4cdd5; padding:12px 20px; border-radius:4px; font-weight:500; cursor:pointer;">Hủy</button>
                <button type="submit" name="btn_save_draft" value="1" style="background:#f4f6f8; color:#212b36; border:1px solid #c4cdd5; padding:12px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">Lưu (Chưa kích hoạt)</button>
                <button type="submit" name="btn_save_active" value="1" style="background:#0088ff; color:#fff; border:none; padding:12px 20px; border-radius:4px; font-weight:bold; cursor:pointer; font-size: 14px;">Lưu & Kích hoạt</button>
            </div>
        </div>
    </div>
</form>

<script>
    // Xử lý bật/tắt Mã giảm giá
    function togglePromoCode() {
        var isChecked = document.getElementById('has_code').checked;
        document.getElementById('code_input_box').style.display = isChecked ? 'block' : 'none';
        if (!isChecked) document.querySelector('input[name="promo_code"]').value = '';
    }

    // Xử lý giới hạn số lượng
    function toggleLimit() {
        let isChecked = document.getElementById('unlimited_usage').checked;
        let limitInput = document.getElementById('usage_limit');
        limitInput.disabled = isChecked;
        if (isChecked) limitInput.value = '';
    }

    // Xử lý Ngày kết thúc
    function toggleEndDate() {
        let isChecked = document.getElementById('no_end_date').checked;
        document.getElementById('end_date_box').style.display = isChecked ? 'none' : 'block';
    }

    // Xử lý Khung giờ nâng cao
    function toggleAdvancedTime() {
        let isChecked = document.querySelector('input[name="enable_advanced_time"]').checked;
        document.getElementById('advanced_time_box').style.display = isChecked ? 'block' : 'none';
    }

    // Xử lý Chi nhánh
    function toggleBranch() {
        let val = document.querySelector('input[name="branch_scope"]:checked').value;
        document.getElementById('branch_list').style.display = (val === 'specific') ? 'block' : 'none';
    }

    // Xử lý Khách hàng
    function toggleCustomer() {
        let val = document.querySelector('input[name="customer_scope"]:checked').value;
        document.getElementById('customer_cond_box').style.display = (val === 'specific') ? 'block' : 'none';
    }

    // Xử lý chuyển đổi giữa các Loại Khuyến mại (Giảm giá vs Tặng quà)
    function switchPromoType() {
        var type = document.getElementById('promo_type').value;

        // Ẩn tất cả các khối trước
        document.getElementById('section_discount_normal').style.display = 'none';
        document.getElementById('section_gift_by_order').style.display = 'none';
        document.getElementById('section_gift_by_product').style.display = 'none';

        // Hiển thị khối tương ứng theo cấu hình chọn
        if (type === 'discount_order' || type === 'discount_product') {
            document.getElementById('section_discount_normal').style.display = 'block';
            document.getElementById('min_order_box').style.display = (type === 'discount_order') ? 'block' : 'none';
        } else if (type === 'gift_by_order') {
            document.getElementById('section_gift_by_order').style.display = 'block';
        } else if (type === 'gift_by_product') {
            document.getElementById('section_gift_by_product').style.display = 'block';
        }
    }

    // Xử lý chuyển đổi điều kiện Mua X (Sản phẩm vs Danh mục vs Nhãn hiệu)
    function toggleProdCondition(val) {
        document.getElementById('box_select_products').style.display = (val === 'product') ? 'block' : 'none';
        document.getElementById('box_select_categories').style.display = (val === 'category') ? 'block' : 'none';
        document.getElementById('box_select_brands').style.display = (val === 'brand') ? 'block' : 'none';
    }

    // Chạy kích hoạt lần đầu khi vừa tải trang để form hiển thị đúng tab mặc định
    window.addEventListener('DOMContentLoaded', (event) => {
        switchPromoType();
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
