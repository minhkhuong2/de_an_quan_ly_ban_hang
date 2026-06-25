<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $setting
 * @var array $rates
 * @var array $branches
 */
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 24px;
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
        font-size: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        padding: 20px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 8px;
        color: #212b36;
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

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group input {
        border-radius: 4px 0 0 4px;
        border-right: none;
    }

    .input-group-text {
        padding: 10px 15px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 0 4px 4px 0;
        color: #637381;
        font-weight: 600;
    }

    /* Bảng Fee */
    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 13px;
        color: #637381;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 650px;
        padding: 25px;
        border-radius: 8px;
        max-height: 90vh;
        overflow-y: auto;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Cấu hình Vận chuyển</div>
</div>

<?php if (isset($_GET['success_pkg'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Lưu cấu hình Gói hàng mặc định thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_rate'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thêm biểu phí vận chuyển thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_del'])): ?>
    <div style="background:#fff8ea; color:#8a6100; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #ffea8a;">🗑️ Đã xóa biểu phí vận chuyển!</div>
<?php endif; ?>

<form action="index.php?action=update_shipping_pkg" method="POST">
    <div class="v3-card">
        <div class="card-header">
            📦 Thông tin gói hàng mặc định
            <button type="submit" class="btn-primary" style="padding: 6px 15px;">Lưu gói hàng</button>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div>
                    <div class="form-group">
                        <label>Khối lượng gói hàng</label>
                        <select name="weight_mode" class="form-control" onchange="toggleWeightInput(this.value)">
                            <option value="product" <?php echo ($setting['weight_mode'] ?? '') == 'product' ? 'selected' : ''; ?>>Theo sản phẩm trong đơn hàng (Cộng dồn)</option>
                            <option value="custom" <?php echo ($setting['weight_mode'] ?? '') == 'custom' ? 'selected' : ''; ?>>Tùy chỉnh khối lượng cố định</option>
                        </select>
                    </div>
                    <div class="form-group" id="custom_weight_box" style="display: <?php echo ($setting['weight_mode'] ?? '') == 'custom' ? 'block' : 'none'; ?>;">
                        <label>Nhập khối lượng cố định</label>
                        <div class="input-group">
                            <input type="number" name="default_weight" class="form-control" value="<?php echo $setting['default_weight'] ?? 500; ?>">
                            <div class="input-group-text">Gram</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 13px; margin-bottom: 8px; display:block; color:#212b36;">Kích thước mặc định (D x R x C)</label>
                    <div class="grid-3">
                        <div class="input-group">
                            <input type="number" name="length" class="form-control" value="<?php echo $setting['length'] ?? 10; ?>">
                            <div class="input-group-text">cm</div>
                        </div>
                        <div class="input-group">
                            <input type="number" name="width" class="form-control" value="<?php echo $setting['width'] ?? 10; ?>">
                            <div class="input-group-text">cm</div>
                        </div>
                        <div class="input-group">
                            <input type="number" name="height" class="form-control" value="<?php echo $setting['height'] ?? 10; ?>">
                            <div class="input-group-text">cm</div>
                        </div>
                    </div>
                    <p style="font-size: 12px; color: #637381; margin-top: 5px;">Hữu ích khi gửi thông tin sang Giao Hàng Nhanh / Viettel Post để tính thể tích.</p>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 20px 0;">

            <div class="grid-2">
                <div class="form-group">
                    <label>Yêu cầu giao hàng mặc định cho Shipper</label>
                    <select name="delivery_requirement" class="form-control">
                        <option value="no_check" <?php echo ($setting['delivery_requirement'] ?? '') == 'no_check' ? 'selected' : ''; ?>>Không cho xem hàng</option>
                        <option value="check_no_try" <?php echo ($setting['delivery_requirement'] ?? '') == 'check_no_try' ? 'selected' : ''; ?>>Cho xem hàng, KHÔNG cho thử</option>
                        <option value="check_and_try" <?php echo ($setting['delivery_requirement'] ?? '') == 'check_and_try' ? 'selected' : ''; ?>>Cho xem hàng và cho thử</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; margin-top: 25px; cursor:pointer;">
                        <input type="checkbox" name="auto_sync_return" value="1" style="width: 18px; height: 18px;" <?php echo ($setting['auto_sync_return'] ?? 1) == 1 ? 'checked' : ''; ?>>
                        <span>Tự động nhập kho khi Shipper hoàn hàng thành công</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="v3-card">
    <div class="card-header">
        🚚 Phí giao hàng theo khu vực
        <button class="btn-outline" style="color: #0088ff; border-color: #0088ff;" onclick="document.getElementById('rate_modal').style.display='flex'">+ Thêm phí vận chuyển</button>
    </div>

    <table class="v3-table">
        <thead>
            <tr>
                <th>Tên cấu hình</th>
                <th>Khu vực / Chi nhánh xuất</th>
                <th>Loại phí</th>
                <th style="text-align: right;">Mức phí / Xử lý</th>
                <th style="text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rates)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#637381;">Chưa có cấu hình phí vận chuyển nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rates as $r): ?>
                    <tr>
                        <td>
                            <b><?php echo htmlspecialchars($r['rate_name']); ?></b><br>
                            <?php if ($r['min_order_value'] > 0): ?>
                                <span style="font-size:12px; color:#108043; background:#eafff0; padding:2px 6px; border-radius:4px;">Điều kiện: Đơn ≥ <?php echo number_format($r['min_order_value'], 0, '', '.'); ?>đ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:13px; color:#212b36;">KV: <b><?php echo htmlspecialchars($r['zone_name']); ?></b></div>
                            <div style="font-size:12px; color:#637381;">Kho: <?php echo htmlspecialchars($r['branch_name']); ?></div>
                        </td>
                        <td>
                            <?php if ($r['rate_type'] == 'custom'): ?>
                                <span style="color:#e67e22; font-weight:600;"><i class="fa-solid fa-truck"></i> Phí tự cấu hình</span>
                            <?php else: ?>
                                <span style="color:#0088ff; font-weight:600;"><i class="fa-solid fa-bolt"></i> Đối tác: <?php echo strtoupper($r['partner_code']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            <?php if ($r['rate_type'] == 'custom'): ?>
                                <span style="color:#d82c0d;"><?php echo number_format($r['base_fee'], 0, '', '.'); ?> ₫</span>
                            <?php else: ?>
                                Phí ĐT +
                                <span style="color:#d82c0d;">
                                    <?php echo $r['handling_fee_type'] == 'percent' ? $r['handling_fee_value'] . '%' : number_format($r['handling_fee_value'], 0, '', '.') . ' ₫'; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="index.php?action=delete_shipping_rate&id=<?php echo $r['id']; ?>" class="btn-outline" style="border:none; color:#d82c0d;" onclick="return confirm('Xóa cấu hình phí này?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="rate_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thêm Cấu hình Phí vận chuyển</h3>
        <form action="index.php?action=add_shipping_rate" method="POST">

            <div class="grid-2">
                <div class="form-group">
                    <label>Chi nhánh xuất hàng <span>*</span></label>
                    <select name="branch_id" class="form-control" required>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tên khu vực (Zone) <span>*</span></label>
                    <input type="text" name="zone_name" class="form-control" required placeholder="VD: Nội thành Hà Nội">
                </div>
            </div>

            <div class="form-group">
                <label>Các Tỉnh / Thành phố áp dụng</label>
                <input type="text" name="provinces" class="form-control" placeholder="VD: Hà Nội, Hưng Yên, Bắc Ninh (Bỏ trống = Toàn quốc)">
            </div>

            <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 15px 0;">

            <div class="form-group">
                <label style="font-size: 15px; color: #0088ff;"><i class="fa-solid fa-money-bill-wave"></i> Loại cấu hình phí <span>*</span></label>
                <select name="rate_type" id="rate_type" class="form-control" onchange="toggleRateType(this.value)" style="border-color:#0088ff; background:#f4f9ff; font-weight:bold;">
                    <option value="custom">1. Phí vận chuyển tự cấu hình (Cố định)</option>
                    <option value="partner">2. Tính tự động theo Đối tác giao hàng (API)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tên hiển thị phí cho khách xem <span>*</span></label>
                <input type="text" name="rate_name" class="form-control" required placeholder="VD: Giao hàng tiêu chuẩn / Giao hàng siêu tốc">
            </div>

            <div id="block_custom_rate">
                <div class="form-group">
                    <label>Phí giao hàng cố định <span>*</span></label>
                    <div class="input-group">
                        <input type="number" name="base_fee" class="form-control" value="0">
                        <div class="input-group-text">VNĐ</div>
                    </div>
                </div>
            </div>

            <div id="block_partner_rate" style="display:none; background:#f4f6f8; padding:15px; border-radius:6px; border:1px solid #dfe3e8; margin-bottom:15px;">
                <div class="form-group">
                    <label>Đối tác giao hàng tích hợp</label>
                    <select name="partner_code" class="form-control">
                        <option value="ghn">Giao Hàng Nhanh (GHN)</option>
                        <option value="vtp">Viettel Post</option>
                        <option value="ghtk">Giao Hàng Tiết Kiệm</option>
                    </select>
                </div>
                <label style="font-weight:bold; font-size:13px;">Thêm phí xử lý gói hàng (Thu thêm so với phí ĐT)</label>
                <div style="display:flex; gap:10px; margin-top:5px;">
                    <select name="handling_fee_type" class="form-control" style="width: 40%;">
                        <option value="amount">Cộng tiền (VNĐ)</option>
                        <option value="percent">Cộng % phí</option>
                    </select>
                    <input type="number" name="handling_fee_value" class="form-control" style="width: 60%;" value="0" placeholder="Mức thu thêm...">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Thời gian giao hàng dự kiến</label>
                    <input type="text" name="estimated_time" class="form-control" placeholder="VD: 1-2 ngày / Trong 24h">
                </div>
                <div class="form-group">
                    <label>Điều kiện: Tổng đơn Tối thiểu (Freeship)</label>
                    <div class="input-group">
                        <input type="number" name="min_order_value" class="form-control" placeholder="Để trống nếu không có">
                        <div class="input-group-text">VNĐ</div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('rate_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu cấu hình phí</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleWeightInput(val) {
        document.getElementById('custom_weight_box').style.display = (val === 'custom') ? 'block' : 'none';
    }

    function toggleRateType(val) {
        if (val === 'custom') {
            document.getElementById('block_custom_rate').style.display = 'block';
            document.getElementById('block_partner_rate').style.display = 'none';
        } else {
            document.getElementById('block_custom_rate').style.display = 'none';
            document.getElementById('block_partner_rate').style.display = 'block';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
