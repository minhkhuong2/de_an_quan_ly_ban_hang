<?php

/** @var array $price_list */
/** @var array $items */
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
        padding: 20px;
    }

    .table-details {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table-details th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-details td {
        padding: 12px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
        vertical-align: middle;
    }

    .icon-btn {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 15px;
        padding: 5px;
        margin-left: 5px;
    }

    .icon-edit {
        color: #0088ff;
    }

    .icon-delete {
        color: #d82c0d;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        padding: 8px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Chi tiết sản phẩm đăng bán: <?php echo htmlspecialchars($price_list['name']); ?></div>
    <a href="index.php?action=product_price" class="btn-outline">← Quay lại danh sách</a>
</div>

<div class="v3-card">
    <div style="background:#fafbfc; padding:15px; border-radius:6px; border:1px solid #dfe3e8; margin-bottom:20px; font-size:14px; line-height:1.6;">
        🔹 <b>Phân loại:</b> <?php echo $price_list['target_type'] == 'customer_group' ? 'Theo nhóm khách hàng' : 'Theo chi nhánh'; ?><br>
        🔹 <b>Trạng thái:</b> <span style="font-weight:600; color:#0088ff;"><?php echo $price_list['status'] == 'active' ? 'Đang áp dụng' : 'Ngừng áp dụng'; ?></span>
    </div>

    <h3 style="font-size:16px; margin-bottom:10px; color:#212b36;">Danh sách sản phẩm đăng bán</h3>
    <table class="table-details">
        <thead>
            <tr>
                <th style="width: 45%;">Tên sản phẩm</th>
                <th style="width: 20%; text-align: right;">Giá bán lẻ lẻ gốc</th>
                <th style="width: 20%; text-align: right;">Giá trong bảng giá</th>
                <th style="width: 15%; text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr id="empty_row">
                    <td colspan="4" style="text-align: center; color: #8c98a4; padding: 25px;">Chưa áp dụng sản phẩm nào cho bảng giá này. Bạn có thể bổ sung hàng loạt bằng cách nhấn nút sửa hàng loạt ở ngoài.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr id="row_<?php echo $item['id']; ?>">
                        <td>
                            <div style="font-weight:600; color:#212b36;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div style="font-size:12px; color:#637381;">SKU: <?php echo $item['sku']; ?></div>
                        </td>
                        <td style="text-align: right; color:#637381; text-decoration:line-through;"><?php echo number_format($item['base_price'], 0, ',', '.'); ?> ₫</td>
                        <td style="text-align: right; font-weight: bold; color: #108043;" id="price_text_<?php echo $item['id']; ?>">
                            <?php echo number_format($item['custom_price'], 0, ',', '.'); ?> ₫
                        </td>
                        <td style="text-align: center;">
                            <button class="icon-btn icon-edit" title="Sửa đơn giá riêng món này" onclick="editSingleItemPrice(<?php echo $price_list['id']; ?>, <?php echo $item['id']; ?>, <?php echo $item['custom_price']; ?>)">✏️</button>
                            <button class="icon-btn icon-delete" title="Xóa khỏi bảng giá" onclick="deleteItemFromPriceList(<?php echo $price_list['id']; ?>, <?php echo $item['id']; ?>)">🗑️</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Xử lý nút bút chì gọi API sửa giá động
    function editSingleItemPrice(priceListId, productId, currentPrice) {
        let newPrice = prompt("Nhập số tiền giá bán mới cụ thể cho sản phẩm này (Số nguyên):", currentPrice);
        if (newPrice !== null && !isNaN(newPrice) && newPrice.trim() !== "") {
            let priceVal = parseFloat(newPrice);

            fetch('index.php?action=api_update_price_item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        price_list_id: priceListId,
                        product_id: productId,
                        new_price: priceVal
                    })
                })
                .then(res => res.json()).then(res => {
                    if (res.status === 'success') {
                        alert(res.msg);
                        // Cập nhật giá text trực tiếp trên dòng
                        document.getElementById('price_text_' + productId).innerText = new Intl.NumberFormat('vi-VN').format(priceVal) + ' ₫';
                    } else {
                        alert(res.msg);
                    }
                });
        }
    }

    // Xử lý nút thùng rác xóa lẻ sản phẩm ra khỏi đăng bán
    function deleteItemFromPriceList(priceListId, productId) {
        if (!confirm("Bạn có chắc chắn muốn xóa gỡ sản phẩm này ra khỏi danh sách đăng bán của bảng giá?")) return;

        fetch('index.php?action=api_delete_price_item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    price_list_id: priceListId,
                    product_id: productId
                })
            })
            .then(res => res.json()).then(res => {
                if (res.status === 'success') {
                    alert(res.msg);
                    // Xóa dòng tr trên giao diện ngay lập tức
                    document.getElementById('row_' + productId).remove();
                } else {
                    alert(res.msg);
                }
            });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
