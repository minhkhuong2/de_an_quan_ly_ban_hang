<?php

/** @var array $price_list */
/** @var array $products */
/** @var array $existing_items */
require_once __DIR__ . '/../layout/header.php';

// Xử lý hiển thị Text quy tắc
$rule_text = ($price_list['adjustment_type'] == 'increase' ? 'Tăng giá' : 'Giảm giá') . ' ' . $price_list['adjustment_value'] . '%';
$rule_color = ($price_list['adjustment_type'] == 'increase' ? '#d82c0d' : '#108043');
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

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
    }

    .info-bar {
        background: #fafbfc;
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        justify-content: space-between;
        border-radius: 8px 8px 0 0;
    }

    .table-products {
        width: 100%;
        border-collapse: collapse;
    }

    .table-products th {
        background: #f4f6f8;
        color: #637381;
        font-size: 13px;
        font-weight: 600;
        text-align: left;
        padding: 12px 20px;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-products td {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        vertical-align: middle;
        color: #212b36;
        font-size: 14px;
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

    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">
        <a href="index.php?action=product_price" style="text-decoration:none; color:#637381;">←</a>
        Chọn sản phẩm áp dụng bảng giá
    </div>
    <button type="button" class="btn-primary" onclick="document.getElementById('frm_items').submit()">💾 Lưu cấu hình bảng giá</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Đã tạo bảng giá thành công! Vui lòng chọn sản phẩm để áp dụng.</div>
<?php endif; ?>

<div class="v3-card">
    <div class="info-bar">
        <div>
            <div style="font-size: 13px; color: #637381;">Tên bảng giá đang cấu hình:</div>
            <div style="font-weight: bold; font-size: 16px; margin-top: 5px; color: #212b36;"><?php echo htmlspecialchars($price_list['name']); ?></div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 13px; color: #637381;">Quy tắc áp dụng:</div>
            <div style="font-weight: bold; font-size: 16px; margin-top: 5px; color: <?php echo $rule_color; ?>;">
                <?php echo $rule_text; ?>
            </div>
        </div>
    </div>

    <form id="frm_items" action="index.php?action=store_price_list_items" method="POST">
        <input type="hidden" name="price_list_id" value="<?php echo $price_list['id']; ?>">

        <table class="table-products">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                    <th style="width: 45%;">Sản phẩm</th>
                    <th style="width: 15%; text-align: right;">Giá bán lẻ (Gốc)</th>
                    <th style="width: 20%; text-align: center;">Mức điều chỉnh</th>
                    <th style="width: 15%; text-align: right;">Giá mới áp dụng</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #8c98a4;">Chưa có sản phẩm nào trong hệ thống.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <?php
                        $is_checked = in_array($p['id'], $existing_items) ? 'checked' : '';
                        $base_price = $p['base_price'];

                        // Tự động tính toán giá mới (Làm tròn số nguyên theo tài liệu Hệ thống)
                        $adj_val = $price_list['adjustment_value'];
                        if ($price_list['adjustment_type'] == 'increase') {
                            $custom_price = round($base_price * (1 + ($adj_val / 100)));
                        } else {
                            $custom_price = round($base_price * (1 - ($adj_val / 100)));
                        }
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" class="chk-item" name="product_ids[]" value="<?php echo $p['id']; ?>" <?php echo $is_checked; ?>>
                            </td>
                            <td>
                                <div class="item-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                <div class="item-sku">SKU: <?php echo $p['sku']; ?></div>
                            </td>
                            <td style="text-align: right; color: #637381; text-decoration: line-through;">
                                <?php echo number_format($base_price, 0, ',', '.'); ?> ₫
                            </td>
                            <td style="text-align: center; font-weight: 500; color: <?php echo $rule_color; ?>;">
                                <?php echo $rule_text; ?>
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #0088ff; font-size: 15px;">
                                <?php echo number_format($custom_price, 0, ',', '.'); ?> ₫
                                <input type="hidden" name="custom_prices[<?php echo $p['id']; ?>]" value="<?php echo $custom_price; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    // Logic Check All
    function toggleAll(source) {
        let checkboxes = document.querySelectorAll('.chk-item');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
