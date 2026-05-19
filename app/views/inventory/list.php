<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .sapo-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
    }

    .sapo-filter-bar input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .sapo-filter-bar button,
    .sapo-filter-bar select {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .sapo-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    .sapo-table th,
    .sapo-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
        vertical-align: middle;
    }

    .sapo-table th {
        color: #637381;
        font-weight: 500;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
    }

    .col-cb {
        width: 40px;
        text-align: center !important;
    }

    .col-img {
        width: 60px;
    }

    .col-name {
        width: 30%;
    }

    .col-num {
        width: 120px;
        text-align: right !important;
    }

    /* Hover Row hiện nút bút chì */
    .product-row:hover {
        background-color: #f9fafb;
    }

    .stock-cell {
        position: relative;
        cursor: pointer;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .edit-icon {
        display: none;
        color: #0088ff;
        margin-right: 10px;
        font-size: 16px;
    }

    .product-row:hover .edit-icon {
        display: inline-block;
    }

    /* Pop-up Edit Stock nhanh */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-box {
        background: #fff;
        width: 450px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        justify-content: space-between;
        font-weight: bold;
        font-size: 16px;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #dfe3e8;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fafbfc;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        margin-top: 5px;
        margin-bottom: 15px;
        box-sizing: border-box;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Quản lý kho</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer;">↑ Xuất file</button>
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer;">↓ Nhập file</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật tồn kho thành công!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 400px;">

    <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; display: flex; gap: 20px; font-size: 14px;">
        <span style="color: #0088ff; font-weight: 500; border-bottom: 2px solid #0088ff; padding-bottom: 10px; margin-bottom: -11px;">Tất cả</span>
    </div>

    <div class="sapo-filter-bar">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm theo mã SKU, tên, barcode sản phẩm..." style="padding-left: 35px; width: 100%;">
        </div>
        <select>
            <option>Ngày tạo ▼</option>
        </select>
        <select>
            <option>Tồn kho ▼</option>
        </select>
        <button>Y Bộ lọc khác</button>
        <button style="color: #0088ff; font-weight: 500; background: #e6f7ff; border-color: transparent;">Lưu bộ lọc</button>
    </div>

    <table class="sapo-table">
        <thead>
            <tr>
                <th class="col-cb"><input type="checkbox"></th>
                <th class="col-img">Ảnh</th>
                <th class="col-name">Sản phẩm</th>
                <th class="col-num">Có thể bán</th>
                <th class="col-num">Tồn kho ⓘ</th>
                <th class="col-num">Đang giao dịch</th>
                <th class="col-num">Đang về kho</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inventories)): ?>
                <?php foreach ($inventories as $row): ?>
                    <tr class="product-row">
                        <td class="col-cb"><input type="checkbox"></td>
                        <td class="col-img">
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border:1px solid #dfe3e8;">
                            <?php else: ?>
                                <div style="width:40px; height:40px; background:#f4f6f8; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px; font-size: 20px;">📱</div>
                            <?php endif; ?>
                        </td>
                        <td class="col-name">
                            <a href="#" style="color: #0088ff; font-weight: 500; text-decoration: none;"><?php echo htmlspecialchars($row['product_name']); ?></a><br>
                            <span style="color: #637381; font-size: 12px;"><?php echo !empty($row['sku']) ? htmlspecialchars($row['sku']) : '---'; ?> | <?php echo !empty($row['barcode']) ? htmlspecialchars($row['barcode']) : '---'; ?></span>
                        </td>

                        <td class="col-num" style="color: #108043; font-weight: 500;"><?php echo $row['available']; ?></td>

                        <td class="col-num">
                            <div class="stock-cell" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['product_name']); ?>', <?php echo $row['stock']; ?>)">
                                <span class="edit-icon">✏️</span>
                                <span style="font-weight: 500; border-bottom: 1px dashed #c4cdd5;"><?php echo $row['stock']; ?></span>
                            </div>
                        </td>

                        <td class="col-num" style="color: #b7791f;"><?php echo $row['trading']; ?></td>
                        <td class="col-num" style="color: #0088ff;"><?php echo $row['incoming']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 50px;">
                        <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                        <div style="color: #212b36; font-weight: bold;">Chưa có dữ liệu tồn kho</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <span>Chỉnh sửa tồn kho</span>
            <span style="cursor: pointer; color: #637381;" onclick="closeEditModal()">✖</span>
        </div>
        <form action="index.php?action=update_stock" method="POST">
            <div class="modal-body">
                <input type="hidden" name="product_id" id="modal_product_id">
                <div style="font-weight: 500; color: #0088ff; margin-bottom: 15px;" id="modal_product_name"></div>

                <label style="font-weight: 500; font-size: 14px;">Tồn kho mới</label>
                <input type="number" name="new_stock" id="modal_new_stock" class="form-control" required>

                <p style="font-size: 13px; color: #637381; margin: 0;">Lưu ý: Số lượng "Có thể bán" sẽ tự động được hệ thống tính toán lại.</p>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Hủy</button>
                <button type="submit" style="background: #0088ff; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, currentStock) {
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('modal_product_id').value = id;
        document.getElementById('modal_product_name').innerText = name;
        document.getElementById('modal_new_stock').value = currentStock;
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
