<?php
/** 
 * @var array $customer_groups 
 * @var array $branches 
 * @var array $channels
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
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
        font-size: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
        color: #212b36;
    }

    .form-group label span {
        color: red;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #0088ff;
    }

    .input-group {
        display: flex;
        gap: 10px;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
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

    .info-box {
        background: #f4f9ff;
        border: 1px dashed #0088ff;
        padding: 12px;
        border-radius: 4px;
        color: #0056b3;
        font-size: 13px;
        margin-top: 10px;
        line-height: 1.5;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=product_price" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Thêm mới bảng giá</div>
</div>

<form id="frm_price_list" action="index.php?action=store_price_list" method="POST">
    <input type="hidden" name="action_status" id="action_status" value="draft">

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div style="flex: 2;">
            <div class="v3-card">
                <div class="card-header">Thông tin chung</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tên bảng giá <span>*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Giá Sỉ Đại Lý, Giá Khuyến Mãi CN Hà Nội..." required>
                    </div>

                    <div class="form-group">
                        <label>Điều chỉnh giá so với giá bán lẻ gốc <span>*</span></label>
                        <div class="input-group" style="align-items: center;">
                            <select name="adjustment_type" class="form-control" style="width: 150px;">
                                <option value="increase">Tăng giá (%)</option>
                                <option value="decrease">Giảm giá (%)</option>
                            </select>
                            <input type="number" name="adjustment_value" class="form-control" placeholder="Nhập số nguyên, VD: 5" min="0" max="100" style="width: 200px;" required>
                        </div>
                        <div class="info-box">
                            💡 <b>Ví dụ tự động:</b> Nếu sản phẩm gốc giá 100.000đ. Bạn chọn <b>Giảm giá (%)</b> và nhập <b>5</b>. Hệ thống sẽ tự động set giá cho khách hàng/chi nhánh này là: 100.000 - 5% = <b>95.000đ</b>.
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="auto_add_new_product" value="1" style="width:16px; height:16px;">
                            <b>Tự động thêm sản phẩm vào bảng giá</b>
                        </label>
                        <p style="font-size: 13px; color: #637381; margin: 5px 0 0 24px;">Sản phẩm sau khi tạo mới ở Danh sách sản phẩm sẽ tự động được thêm vào bảng giá này (Các sản phẩm cũ sẽ không tự động thêm).</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="v3-card">
                <div class="card-header">Đối tượng áp dụng</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Bảng giá này áp dụng cho:</label>
                        <select name="target_type" id="target_type" class="form-control" onchange="toggleTargetBlock()">
                            <option value="customer_group">1. Nhóm khách hàng</option>
                            <option value="branch">2. Chi nhánh</option>
                            <option value="channel">3. Kênh bán hàng</option>
                        </select>
                    </div>

                    <div id="block_customer_group" class="form-group target-block">
                        <label>Chọn nhóm khách hàng <span>*</span></label>
                        <select name="customer_group_id" class="form-control">
                            <?php foreach ($customer_groups as $cg): ?>
                                <option value="<?php echo $cg['id']; ?>"><?php echo htmlspecialchars($cg['group_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p style="font-size: 12px; color: #d82c0d; margin-top: 5px;">Thứ tự ưu tiên 1: Cao nhất.</p>
                    </div>

                    <div id="block_branch" class="form-group target-block" style="display: none;">
                        <label>Chọn chi nhánh <span>*</span></label>
                        <select name="branch_id" class="form-control">
                            <?php foreach ($branches as $b): ?>
                                <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p style="font-size: 12px; color: #e67e22; margin-top: 5px;">Thứ tự ưu tiên 2: Trung bình.</p>
                    </div>

                    <div id="block_channel" class="form-group target-block" style="display: none;">
                        <label>Chọn Kênh bán hàng (Nguồn đơn) <span>*</span></label>
                        <select name="channel_id" class="form-control">
                            <?php foreach ($channels as $ch): ?>
                                <option value="<?php echo $ch['id']; ?>"><?php echo htmlspecialchars($ch['source_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p style="font-size: 12px; color: #108043; margin-top: 5px;">Thứ tự ưu tiên 3: Thấp nhất.</p>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #dfe3e8; display: flex; flex-direction: column; gap: 10px;">
                <button type="button" class="btn-outline" style="width: 100%; border-color: #d82c0d; color: #d82c0d;" onclick="window.location.href='index.php?action=product_price'">Hủy</button>
                <button type="button" class="btn-outline" style="width: 100%;" onclick="submitForm('draft')">💾 Lưu nháp</button>
                <button type="button" class="btn-primary" style="width: 100%;" onclick="submitForm('active')">🚀 Lưu & Áp dụng ngay</button>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleTargetBlock() {
        let type = document.getElementById('target_type').value;
        // Ẩn tất cả các khối trước
        document.querySelectorAll('.target-block').forEach(b => b.style.display = 'none');
        // Hiện đúng khối được chọn
        document.getElementById('block_' + type).style.display = 'block';
    }

    function submitForm(status) {
        // Gán trạng thái vào input ẩn trước khi gửi
        document.getElementById('action_status').value = status;

        let msg = status === 'draft' ? "Bảng giá sẽ được Lưu nháp. Tiếp tục?" : "Bảng giá sẽ Kích hoạt và chuyển sang màn hình thêm sản phẩm. Tiếp tục?";
        if (confirm(msg)) {
            document.getElementById('frm_price_list').submit();
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
